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
 * Class JBCartElementHookBalanceReduce
 */
class JBCartElementHookSdekOrder extends JBCartElementHook
{   
    const CACHE_TTL = 1440;

    /**
     * @var string
     */
    private $_realOrder = 'https://api.cdek.ru/v2/orders';

    /**
     * @var string
     */
    private $_testOrder = 'https://api.edu.cdek.ru/v2/orders';

    /**
     * @var string
     */
    private $_realOauth = 'https://api.cdek.ru/v2/oauth/token';

    /**
     * @var string
     */
    private $_testOauth = 'https://api.edu.cdek.ru/v2/oauth/token';

    /**
     * @var array
     */
    private $_oauth = array();

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_hook_sdekorder', $this->app->path->path('jbapp:cart-elements').'/hook/sdekorder', null, true);
    }

    /**
     * @param array $params
     */
    public function notify($params = array())
    {   
        if ($this->getOrder()->getShipping()->getType() == 'flsdek' || $this->getOrder()->getShipping()->getType() == 'sdek') {
            $uuid = $this->sdekSend();

            if ($uuid) {
                $result = false;
                $i      = 1;
            
                while (!$result) {
                    sleep(1);

                    $track = $this->sdekTrack($uuid);

                    if ($track || $i == 5) { // track or 5 times max
                        $result = true;
                    }

                    $i++;
                }
            }
        }
    }

    /**
     * @return null|string
     */
    public function sdekSend()
    {   
        $order      = $this->getOrder();
        $shipping   = $order->getShipping();
        $from       = $shipping->config->get('from')['city-id'];
        $type       = (int) $this->config->get('shipping_type', 1);
        $to         = $shipping->get('to')['city-id'];
        $tariff     = (int) $shipping->get('tariff');
        $address    = $shipping->get('address');
        $pvz        = $shipping->get('pvz');
        
        if (!empty($from) && !empty($to)) {
            $options = array(
                'type'              => $type,
                'number'            => $this->getOrder()->getName(),
                'tariff_code'       => $tariff,
                'from_location'     => array(
                    'code'          => $from,
                    'address'       => $this->config->get('sender_address'),

                ),
                'sender'            => array(
                    'company'       => $this->config->get('sender_company'),
                    'name'          => $this->config->get('sender_name'),
                    'email'         => $this->config->get('sender_email'),
                    'phones'        => array(
                        0 => array(
                            'number' => $this->config->get('sender_phone'),
                        ),
                    ),
                ),
                'seller'            => array(
                    'name'          => JFactory::getConfig()->get('sitename'),
                ),
                'recipient'         => array(
                    'name'          => $order->getFieldElement($this->config->get('receiver_name'))->data()['value'],
                    'email'         => $order->getFieldElement($this->config->get('receiver_email'))->data()['value'],
                    'phones'        => array(
                        0 => array(
                            'number'    => $order->getFieldElement($this->config->get('receiver_phone'))->data()['value'],
                        )
                    ),
                ),
                'packages'          => array(
                    0 => array(
                        'number'        => $this->getOrder()->getName(),
                        'weight'        => $this->getWeight() * 1000,
                        'comment'       => 'Упаковка #1',
                    )
                )
            );

            // Доставка до Двери и доставка курьером

            if ($tariff == 136) {
                $options['delivery_point'] = $pvz;
            } else {
                $options['to_location'] = array(
                    'code'      => $to,
                    'address'   => $address
                );
            }

            // Список товаров для Интернет-магазина

            if ($type == 1) {
                $options['packages'][0]['items'] = $this->getGoods();
            }

            $uuid = $this->registerRequest($options);

            return $uuid;
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function sdekTrack($uuid)
    {   
        $order  = $this->getOrder();
        $track  = $this->infoRequest($uuid);

        // Error
        if ($track == -1) {
            return $track;
        }

        // Update Track
        if ($track) {
            // get order
            $orderModel = JBModelOrder::model();

            $data = array(
                'track' => $track,
            );

            $order->updateData($data);
            $orderModel->save($order);

            return $track;
        }

        return false;
    }

    /**
     * @param $options
     * @return null|array
     */
    public function registerRequest($options)
    {       
        $url        = $this->isDebug() ? $this->_testOrder : $this->_realOrder;
        $oauth      = $this->apiOauth();

        $response = $this->app->jbhttp->request($url, json_encode($options), array(
            'headers'   => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$oauth['access_token']),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'post',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.hook.sdekorder', 'registerRequest', $result);
        }

        if (!isset($result['requests'][0]['errors'])) {
            return $result['entity']['uuid'];
        }

        return null;
    }

    /**
     * @param $uuid
     * @return null|array
     */
    public function infoRequest($uuid)
    {       
        $url        = $this->isDebug() ? $this->_testOrder : $this->_realOrder;
        $oauth      = $this->apiOauth();

        $response = $this->app->jbhttp->request($url.'/'.$uuid, array(), array(
            'headers'   => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$oauth['access_token']),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'get',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.hook.sdekorder', 'infoRequest', $result);
        }

        if (!isset($result['requests'][0]['errors'])) {
            return isset($result['entity']['cdek_number']) ? $result['entity']['cdek_number'] : '';
        } else {
            return -1;
        }

        return null;
    }

    /**
     * @return array
     */
    public function apiOauth()
    {   
        if (!$this->_oauth) {
            $shipping   = $this->getOrder()->getShipping();
            $login      = $shipping->config->get('login');
            $password   = $shipping->config->get('password');
            $url        = $this->isDebug() ? $this->_testOauth : $this->_realOauth;

            $options    = array(
                'grant_type'    => 'client_credentials',
                'client_id'     => $login,
                'client_secret' => $password,
            );

            $response = $this->app->jbhttp->request($url, $options, array(
                'headers'   => array('Content-Type' => 'application/x-www-form-urlencoded'),
                'cache'     => 0,
                'cache_ttl' => self::CACHE_TTL,
                'debug'     => 1,
                'method'    => 'post',
            ));

            $result = json_decode($response, true);

            if ($result) {
                $this->_oauth = $result;
            }
        }

        return $this->_oauth;
    }

    /**
     * Check is debug mode enabled
     * @return int
     */
    public function isDebug()
    {
        return (int)$this->config->get('debug', 0);
    }

    /**
     * @return float
     */
    protected function getGoods()
    {
        $items      = $this->getOrder()->getItems();
        $shipping   = $this->getOrder()->getShipping();
        $goods      = array();
        $cost       = (float) $this->config->get('item_price', 0);

        foreach ($items as $item) {
            $properties = $this->getProperties($item);

            $goods[] = array(
                'name'      => $item->get('item_name'),
                'ware_key'  => $item->get('elements._sku', $item->get('item_id')),
                'payment'   => array(
                    'value' => 0,
                ),
                'value'     => 0,
                'cost'      => $cost ? $cost : JBCart::val($item->get('total'))->val(),
                'weight'    => $properties['weight'] * 1000,
                'amount'    => $item->get('quantity')
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
            $weight     = $weight + $properties['weight']*$quantity;
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
        $defaultLength  = (float) $this->config->get('default_length', '0.1');
        $defaultWidth   = (float) $this->config->get('default_width', '0.1');
        $defaultHeight  = (float) $this->config->get('default_height', '0.1');

        $weight = $length = $width = $height  = 0;

        if ($source == 'price') {
            $variations = $jsonItem->get('variations');

            if (isset($variations[0])) {
                $properties = isset($variations[0]['_properties']) ? $variations[0]['_properties'] : 0;

                if ($properties) {
                    $length = $this->clear($properties['length']);
                    $width  = $this->clear($properties['width']);
                    $height = $this->clear($properties['height']);
                }
                
                $weight = isset($variations[0]['_weight']) ? $this->clear($variations[0]['_weight']['value']) : 0;
            }
        } else {
            $item = $jsonItem->get('item');

            $elementWeight  = $this->config->get('element_weight');
            $elementLength  = $this->config->get('element_length');
            $elementWidth   = $this->config->get('element_width');
            $elementHeight  = $this->config->get('element_height');

            $elementWeight  = $item->getElement($elementWeight);
            $elementLength  = $item->getElement($elementLength);
            $elementWidth   = $item->getElement($elementWidth);
            $elementHeight  = $item->getElement($elementHeight);

            if ($elementWeight) {
                $weight = $this->clear($elementWeight->render());
            }

            if ($elementLength) {
                $length = $this->clear($elementLength->render());
            }

            if ($elementWidth) {
                $width = $this->clear($elementWidth->render());
            }

            if ($elementHeight) {
                $height = $this->clear($elementHeight->render());
            }
        }

        $weight = $weight ? $weight : $defaultWeight;
        $length = $length ? $length : $defaultLength;
        $width  = $width ? $width : $defaultWidth;
        $height = $height ? $height : $defaultHeight;

        return array(
            'weight' => $weight,
            'length' => $length * 100,
            'width'  => $width * 100,
            'height' => $height * 100,
        );
    }

    /**
     * @return int
     */
    public function isLog()
    {
        return (int) $this->config->get('log', 0);
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
}
