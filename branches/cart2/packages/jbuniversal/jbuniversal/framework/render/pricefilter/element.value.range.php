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
 * Class JBPriceFilterElementValueRange
 */
class JBPriceFilterElementValueRange extends JBPriceFilterElementValue
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $value = $this->_prepareValues();

        $html = '<label for="' . $this->_getId('min') . '">' . JText::_('JBZOO_FROM') . '</label>';
        $html .= $this->_html->text($this->_getName('min'), $value['min'], 'class="jbprice-filter-value-min"', $this->_getId('min'));

        $html .= '<label for="' . $this->_getId('max') . '">' . JText::_('JBZOO_TO') . '</label>';
        $html .= $this->_html->text($this->_getName('max'), $value['max'], 'class="jbprice-filter-value-max"', $this->_getId('max'));

        return '<div class="jbprice-ranges">' . $html . '</div>';
    }

    /**
     * @return array
     */
    protected function _prepareValues()
    {
        $min = $max = '';

        if (is_string($this->_value) && strpos($this->_value, '/')) {
            list($min, $max) = explode('/', $this->_value);

        } else if (is_array($this->_value)) {
            $min = $this->_value['min'];
            $max = $this->_value['max'];
        }

        $result = array(
            'min' => $this->app->jbvars->number($min),
            'max' => $this->app->jbvars->number($max)
        );

        return $result;
    }


}
