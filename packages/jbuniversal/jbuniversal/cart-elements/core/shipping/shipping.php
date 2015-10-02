<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementShipping
 */
abstract class JBCartElementShipping extends JBCartElement
{
    /**
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_SHIPPING;

    /**
     * @type JSONData
     */
    protected $_cartConfig = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney;


    protected $_currency = 'eur';

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbmoney    = $this->app->jbmoney;
        $this->_cartConfig = JBModelConfig::model()->getGroup('cart.config');
    }

    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        if ($this->_currency) {
            return (bool)$this->app->jbvars->currency($this->_currency);
        }

        return false;
    }

    /**
     * @param JBCartValue $summa
     * @return JBCartValue
     */
    public function modify(JBCartValue $summa)
    {
        if ($this->isModify()) {

            if ($this->get('rate')) {
                $rate = $this->get('rate');
            } else {
                try {
                    $rate = $this->getRate();
                } catch (JBCartElementShippingException $e) {
                    $rate = $this->_order->val();
                }
            }

            $summa->add($rate);
        }

        return $summa;
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val(0);
    }

    /**
     * Render shipping in order
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
                'fields' => $this->get('fields', array()),
            ));
        }
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $shipping = JBCart::getInstance()->getShipping();
        $shipping = $this->app->data->create($shipping);

        return $this->identifier == $shipping->get('_shipping_id') || $this->identifier == $shipping->get('element_id');
    }

    /**
     * @return JBCartElementStatus
     */
    public function getStatus()
    {
        $default = JBCart::getInstance()->getDefaultStatus(JBCart::STATUS_SHIPPING);

        $curStatus = $this->get('status');
        if (empty($curStatus)) {
            $curStatus = $default;
        }

        if (!is_object($curStatus)) {

            /** @var JBCartStatusHelper $jbstatus */
            $jbstatus = $this->app->jbcartstatus;

            $status = $jbstatus->getByCode($curStatus, JBCart::STATUS_SHIPPING, $this->getOrder());
            if (!empty($status)) {
                return $status;
            }

            // if not found in current configs
            // TODO get status info from order params
            $unfound = $jbstatus->getUndefined();
            $unfound->config->set('code', $curStatus);
            $unfound->config->set('name', $curStatus);

            return $unfound;
        }

        return $curStatus;
    }

    /**
     * @param      $name
     * @param bool $array
     * @return string|void
     */
    public function getControlName($name, $array = false)
    {
        return $this->_namespace . '[' . $this->identifier . '][' . $name . ']' . ($array ? '[]' : '');
    }

    /**
     * Default params to Call Service.
     * @return array
     */
    public function _getDefaultParams()
    {
        $prop   = $this->getCartProperties();
        $params = array(
            'city'   => $this->_getDefaultCity(),
            'weight' => JBCart::getInstance()->getWeight(),
            'height' => $prop['height'],
            'width'  => $prop['width'],
            'depth'  => $prop['length'],
            'date'   => date('Y-m-d H:i:s'),
        );

        return $params;
    }

    /**
     * Save data in the order.
     * Data comes from method - validateSubmission
     * return JSONData
     */
    public function getOrderData()
    {
        $data = parent::getOrderData();

        $data->set('status', $this->getStatus());
        $data->set('name', $this->getName());
        $data->set('rate', $this->getRate()->data(true));

        return $data;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $this->bindData($value);
        $value->set('rate', $this->getRate()->data(true));

        return $value;
    }

    /**
     * City location of the store
     * @return string
     */
    protected function _getDefaultCity()
    {
        $city = $this->_cartConfig->get('default_shipping_city');
        return $this->app->jbvars->lower($city);
    }

    /**
     * Country location of the store
     * @return string
     */
    protected function _getDefaultCountry()
    {
        $country = $this->_cartConfig->get('default_shipping_country');
        $country = JString::trim($country);
        $country = JString::strtolower($country);

        return $country;
    }

    /**
     * Change shipping status and fire event
     * @param $newStatus
     */
    public function setStatus($newStatus)
    {
        $oldStatus = (string)$this->getStatus();
        $newStatus = (string)$newStatus;

        $isChanged = $oldStatus // is not first set on order creating
            && $oldStatus != JBCartStatusHelper::UNDEFINED // old is not empty
            && $newStatus != JBCartStatusHelper::UNDEFINED // new is not empty
            && $oldStatus != $newStatus; // is really changed

        $this->set('status', $newStatus);

        if ($isChanged) {
            $this->app->jbevent->fire($this->getOrder(), 'basket:shippingStatus', array(
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
            ));
        }
    }

    /**
     * @return float
     */
    protected function _getWeight()
    {
        return $this->_order->getTotalWeight();
    }

    /**
     * Important libs
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->js('jbassets:js/cart/shipping.js');
        $this->app->jbassets->js('jbassets:js/cart/shipping-type.js');
        return parent::loadAssets();
    }

    /**
     * @param $elementId
     * @return bool
     */
    public function hasShippingField($elementId)
    {
        $list = (array)$this->config->get('shippingfields', array());
        return in_array($elementId, $list, true);
    }

    /**
     * @return int
     */
    public function isModify()
    {
        return (int)$this->config->get('modifytotal', 0);
    }

    /**
     * @return array
     */
    public function getAjaxData()
    {
        return array();
    }

}

/**
 * Class JBCartElementShippingException
 */
class JBCartElementShippingException extends JBCartElementException
{
}
