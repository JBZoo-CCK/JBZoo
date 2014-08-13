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
            $type = 'checkbox';
        } else {
            $type = 'radio';
        }

        if (is_string($this->_value)) {
            $this->_value = $this->app->jbcolor->clean(explode(',', $this->_value));
        }

        $values = $this->_createValues($this->_getDbValues());

        return $this->_jbhtml->colors(
            $type,
            $values,
            $this->_getName(),
            $this->_value
        );
    }

    /**
     * @param $values
     * @return array
     */
    protected function _createValues($values)
    {
        $colors = explode("\n", $this->_element->config->get('options'));
        $path   = JString::trim($this->_element->config->get('path'));

        $result = array();
        $colors = $this->app->jbcolor->getColors($colors, $path);

        foreach ($values as $value) {
            $result[$value['value']] = $value['value'];
        }

        $colors = array_intersect(array_flip($colors), $result);

        return array_flip($colors);
    }

}