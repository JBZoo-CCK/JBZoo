<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingCourier extends JBCartElementShipping
{
    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M';

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     *
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        $shipping = $this->getRate();

        return $sum + $shipping;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Get price form element config
     *
     * @param  array $params
     *
     * @return integer
     */
    public function getPrice($params = array())
    {
        $cost = (float)$this->config->get('cost', 0);

        return $this->app->data->create(array(
            'price'  => $this->_jbmoney->format($cost),
            'symbol' => $this->_symbol
        ));
    }

    /**
     * @param array $params
     *
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        $shipping = $this->config->get('cost', 0);

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'shipping' => $this->_jbmoney->toFormat($shipping)
            ));
        }

        return false;
    }

    /**
     * Validates the submitted element
     *
     * @param $value
     * @param $params
     *
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $shipping = $this->config->get('cost', 0);

        $delivery = $this->app->validator->create('date')
            ->addOption('date_format', self::EDIT_DATE_FORMAT)
            ->clean($value->get('delivery_date'));

        $date = new JDate($delivery);

        return array(
            'value'  => $shipping,
            'fields' => array(
                'delivery_date' => $date->calendar('D, d M Y H:i', false, true)
            )
        );
    }

    /**
     * @return int|mixed
     */
    public function getRate()
    {
        $shipping = $this->config->get('cost', 0);

        return $shipping;
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        parent::loadAssets();
        $this->app->jbassets->jqueryui();
        $this->app->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');

        return $this;
    }

    /**
     * Get array of parameters to push it into(data-params) element div
     *
     * @param  boolean $encode - Encode array or no
     *
     * @return string|array
     */
    public function getWidgetParams($encode = true)
    {
        $params = array(
            'shippingfields' => implode(':', $this->config->get('shippingfields', array())),
            'getPriceUrl'    => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetPrice'),
            'default_price'  => $this->_jbmoney->format($this->config->get('cost', '-')),
            'symbol'         => $this->_symbol
        );

        return $encode ? json_encode($params) : $params;
    }
}
