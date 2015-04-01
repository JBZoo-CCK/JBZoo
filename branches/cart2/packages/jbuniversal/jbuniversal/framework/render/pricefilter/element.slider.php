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
 * Class JBPriceFilterElementSlider
 */
class JBPriceFilterElementSlider extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $value = (isset($this->_value['range'])) ? $this->_value['range'] : null;

        $params = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100),
        );

        if ($params['auto']) {

            $applicationId = (int)$this->_params->get('item_application_id', 0);
            $itemType      = $this->_params->get('item_type', null);

            $ranges = (array)JBModelValues::model()->getRangeByPrice($this->_jbprice->identifier, $itemType, $applicationId);
            $params = array_merge($params, $ranges);
        }

        return $this->_html->slider(
            $params,
            $value,
            $this->_getName(),
            $this->_getId(null, true)
        );

    }

}
