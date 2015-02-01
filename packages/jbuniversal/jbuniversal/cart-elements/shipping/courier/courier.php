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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingCourier extends JBCartElementShipping
{
    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M';

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

        return null;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $delivery = $this->app->validator->create('date')
            ->addOption('date_format', self::EDIT_DATE_FORMAT)
            ->clean($value->get('delivery_date'));

        $date = new JDate($delivery);

        return array(
            'value'  => $this->getRate(),
            'fields' => array(
                'delivery_date' => $date->calendar('D, d M Y H:i', false, true)
            )
        );
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        $cost = $this->config->get('cost', 0);
        return $this->_order->val($cost, $this->getCurrency());
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->calendar();
        $this->app->jbassets->less('cart-elements:shipping/courier/assets/less/courier.less');

        return $this;
    }

}
