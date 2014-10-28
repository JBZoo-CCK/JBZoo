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
 * Class JBCartElementEmailShipping
 */
class JBCartElementEmailShipping extends JBCartElementEmail
{
    const DEFAULT_TITLE       = 'JBZOO_ORDER_SHIPPING_TITLE';
    const FIELD_DEFAULT_TITLE = 'JBZOO_ORDER_SHIPPING_SHIPPINGFIELD_TITLE';

    /**
     * Check elements value.
     * Output element or no.
     *
     * @param  array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $order = $this->getOrder();
        if (!empty($order->id) && $order->getShipping()) {
            return true;
        }

        return false;
    }

    /**
     * Render elements data
     *
     * @param  array $params
     *
     * @return null|string
     */
    public function render($params = array())
    {
        $on = (int)$this->config->get('shippingfield', 1);

        $options = array();
        if ($on) {
            $options = $this->getFieldsParams();
        }

        if ($layout = $this->getLayout('order.php')) {
            return self::renderLayout($layout, array(
                'params'      => $params,
                'order'       => $this->getOrder(),
                'shipping'    => $this->_getShipping(),
                'data'        => $this->_getShippingData(),
                'title'       => $this->getTitle(self::DEFAULT_TITLE),
                'fieldParams' => $options
            ));
        }

        return false;
    }

    /**
     * Get params for shippingfield layout
     *
     * @return JSONData
     */
    public function getFieldsParams()
    {
        $title = $this->config->get('shippingfieldtitle');
        $title = !empty($title) ? $title : self::FIELD_DEFAULT_TITLE;

        $params = array(
            'data'  => $this->_getShippingFieldsData(),
            'title' => JText::_($title)
        );

        return $this->app->data->create($params);
    }
}
