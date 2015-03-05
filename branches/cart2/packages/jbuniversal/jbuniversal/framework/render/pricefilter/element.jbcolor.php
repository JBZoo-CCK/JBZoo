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
 * Class JBPriceFilterElementJBColor
 */
class JBPriceFilterElementJBColor extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        if ((int)$this->_params->get('jbzoo_filter_multiple', 1)) {
            $type    = 'checkbox';
            $postfix = null;
            $id      = 'id';
        } else {
            $type    = 'radio';
            $postfix = null;
            $id      = 'id';
        }

        if (is_string($this->_value)) {
            $this->_value = $this->app->jbcolor->clean(explode(',', $this->_value));
        }
        if (is_array($this->_value) && count($this->_value) > 1) {
            $this->_value = JArrayHelper::getColumn($this->_value, 'id');
        }

        $colors = explode(PHP_EOL, $this->_element->config->get('options'));
        $path   = JString::trim($this->_element->config->get('path'));
        $colors = $this->app->jbcolor->getColors($colors, $path);
        $data   = array();
        $titles = array();
        $values = $this->_createValues($this->_getDbValues());

        foreach ($values as $key => $value) {
            if (isset($colors[$value])) {
                $color = $colors[$value];

                $data[$key]   = $color;
                $titles[$key] = $value;
            }
        }

        return $this->html->colors(
            $type,
            $data,
            $this->_getName($id, ''),
            $this->_value,
            array(),
            '26px',
            '26px',
            $titles
        );
    }

    /**
     * @param $values
     * @return array
     */
    protected function _createValues($values)
    {
        $result = array();
        foreach ($values as $value) {
            $result[$value['value']] = $value['text'];
        }

        return $result;
    }

}