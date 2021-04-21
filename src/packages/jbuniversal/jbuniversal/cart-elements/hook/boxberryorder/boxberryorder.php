<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementHookBoxberryOrder
 */
class JBCartElementHookBoxberryOrder extends JBCartElementHook
{   
    const CACHE_TTL = 1440;

    /**
     * @var string
     */
    private $_realOrder = 'https://api.boxberry.ru/json.php';

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_hook_boxberryorder', $this->app->path->path('jbapp:cart-elements').'/hook/boxberryorder', null, true);
    }

    /**
     * @param array $params
     */
    public function notify($params = array())
    {   
        if ($this->getOrder()->getShipping()->getType() == 'boxberry') {
            $this->boxberrySend();
        }
    }

    /**
     * @return null|string
     */
    public function boxberrySend()
    {   
        $order      = $this->getOrder();
        $shipping   = $order->getShipping();
        $to         = $shipping->get('pvz');
        
        if (!empty($to)) {
            $data       =  array(
                'order_id'          => $order->getName(),
                'price'             => $this->_order->getTotalForItems()->val(),
                'payment_sum'       => 0, // Полностью оплаченный заказ
                'delivery_sum'      => (int) $shipping->get('value'),
                'vid'               => 1, // 1 - Самовывоз
                'sender_name'       => JFactory::getConfig()->get('sitename'),
                'shop'              => array(
                    'name'          => $to,
                ),
                'customer'          => array(
                    'fio'           => $order->getFieldElement($this->config->get('receiver_name'))->data()['value'],
                    'email'         => $order->getFieldElement($this->config->get('receiver_email'))->data()['value'],
                    'phone'         => $this->clearPhone($order->getFieldElement($this->config->get('receiver_phone'))->data()['value']),
                ),
                'items'             => $this->getGoods(),
                'weights'           => array(
                    'weight'        => $this->getWeight() * 1000,
                )
            );

            $options    = array(
                'token'     => $this->config->get('token'),
                'method'    => 'ParselCreate',
                'sdata'     => json_encode($data)
            );

            $result = $this->registerRequest($options);

            if (!$result || isset($result['err'])) {
                return null;
            }

            if (isset($result['track'])) {
                return $this->boxberryTrack($result['track']);
            }
        }

        return null;
    }

    /**
     * @param $tarck
     * @return null|string
     */
    public function boxberryTrack($track)
    {   
        $order  = $this->getOrder();

        if (!empty($track)) {
            // get order
            $orderModel = JBModelOrder::model();

            $data = array(
                'track' => $track,
            );

            $order->updateData($data);
            $orderModel->save($order);

            return $track;
        }

        return null;
    }

    /**
     * @param $options
     * @return null|array
     */
    public function registerRequest($options)
    {       
        $response = $this->app->jbhttp->request($this->_realOrder, $options, array(
            'headers'   => array('Content-Type' => 'application/x-www-form-urlencoded'),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'post',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.hook.boxberryorder', 'registerRequest', $result);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getGoods()
    {
        $items      = $this->getOrder()->getItems();
        $goods      = array();

        foreach ($items as $item) {
            $properties = $this->getProperties($item);

            $goods[] = array(
                'id'        => $item->get('elements._sku', $item->get('item_id')),
                'name'      => $item->get('item_name'),
                'quantity'  => (int) $item->get('quantity'),
                'price'     => JBCart::val($item->get('total'))->val()
            );
        }

        return $goods;
    }

    /**
     * @return float
     */
    protected function getWeight()
    {
        $items      = $this->getOrder()->getItems();
        $weight     = 0;

        foreach ($items as $item) {
            $properties = $this->getProperties($item);
            $quantity   = (int) $item->get('quantity');
            $weight     = $weight + $properties['weight'] * $quantity;
        }

        return $weight;
    }

    /**
     * @return array
     */
    public function getProperties($jsonItem) 
    {   
        $shipping       = $this->getOrder()->getShipping();
        $source         = $shipping->config->get('source', 'price');
        $defaultWeight  = (float) $this->config->get('default_weight', '0.2');
        $weight         = 0;

        if ($source == 'price') {
            $variations = $jsonItem->get('variations');

            if (isset($variations[0])) {
                $weight = isset($variations[0]['_weight']) ? $this->clear($variations[0]['_weight']['value']) : 0;
            }
        } else {
            $item = $jsonItem->get('item');

            $elementWeight  = $this->config->get('element_weight');

            $elementWeight  = $item->getElement($elementWeight);

            if ($elementWeight) {
                $weight = $this->clear($elementWeight->render());
            }
        }

        $weight = $weight ? $weight : $defaultWeight;

        return array(
            'weight' => $weight,
        );
    }

    /**
     * @return string
     */
    public function clear($var)
    {
        //$clearVar = htmlentities(strip_tags(JString::trim($var)), ENT_QUOTES, "UTF-8");
        $clearVar = strip_tags(JString::trim($var));
        $clearVar = str_replace(array('см.', 'м.', 'мл.', 'л.', 'кг.', 'г.', ','), array('', '', '', '', '', '', '.'), $clearVar);

        return $clearVar;
    }

    /**
     * @return string
     */
    public function clearPhone($var)
    {
        //$clearVar = htmlentities(strip_tags(JString::trim($var)), ENT_QUOTES, "UTF-8");
        $clearVar = strip_tags(JString::trim($var));
        $clearVar = str_replace(array('(', ')', ' ', '-', '+'), array('', '', '', '', ''), $clearVar);

        return $clearVar;
    }
}
