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
 * Class JBCartElementShippingBoxberry
 */
class JBCartElementShippingBoxberry extends JBCartElementShipping
{
    protected $_currency    = 'rub';

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_shipping_boxberry', $this->app->path->path('jbapp:cart-elements').'/shipping/boxberry', null, true);

        $this->app->jbassets->addVar('JBZOO_ELEMENT_SHIPPING_BOXBERRY_CHANGE', JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_CHANGE'));
        $this->app->jbassets->addVar('JBZOO_ELEMENT_SHIPPING_BOXBERRY_SELECT', JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_SELECT'));
    }

    /**
     * @return array
     */
    public function getAjaxData()
    {   
        return array(
            'weight' => $this->getWeight(),
        );
    }

    /**
     * @return $this
     */
    public function loadAssets()
    {   
        $this->app->jbassets->js('https://points.boxberry.ru/js/boxberry.js');
        $this->app->jbassets->js('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.$this->config->get('yandex_map_key'));

        parent::loadAssets();
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {   
        if ($this->isFree()) {
            return $this->_order->val(0);
        }

        $price  = $this->get('value', '');
        $summ   = $this->_order->val(0, $this->_currency);

        if ($price) {
            $summ->set($price, $this->_currency);   
        }

        return $summ;
    }


    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {   
        $pvz        = $this->get('pvz', '');
        $address    = $this->get('address', '');
        $price      = $this->get('value', '');

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'    => $params,
                'pvz'       => $pvz,
                'address'   => $address,
                'price'     => $price,
                'weight'    => $this->getWeight(),
                'sum'       => $this->_order->getTotalForItems()->val()
            ));
        }
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     * @throws JBCartElementShippingException
     */
    public function validateSubmission($value, $params)
    {   
        $value      = $this->app->data->create($value);
        $pvz        = $value->get('pvz', '');
        $address    = $value->get('address', '');
        $price      = $value->get('value', '');

        if (empty($pvz) || empty($address) || empty($price)) {
            throw new JBCartElementShippingException(JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_EXCEPTION'));
        }

        // for calculate rate
        $this->bindData($value);

        $rate = $this->getRate();
        $value->set('rate', $rate->data(true));

        return $value;
    }

    /**
     * @return array
     */
    public function getWeight()
    {
        $items  = $this->_order->getItems();
        $weight = 0;

        foreach ($items as $item) {
            $properties = $this->getProperties($item);
            $quantity   = $item->get('quantity');
            $weight     += (int)$properties['weight'] * $quantity;
        }

        return $weight;
    }

    /**
     * @param json $jsonItem
     * @return array
     */

    public function getProperties($jsonItem) 
    {   
        $source         = $this->config->get('source', 'price');
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
        }

        $weight = $weight ? $weight : $defaultWeight;

        return array(
            'weight' => $weight * 1000,
        );
    }

    /**
     * @param string    $var
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