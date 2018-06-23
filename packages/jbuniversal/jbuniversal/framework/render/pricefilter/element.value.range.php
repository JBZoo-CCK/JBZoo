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
        $html .= $this->_html->text($this->_getName('min'), $value['min'], 'class="jbprice-filter-value-min"', $this->_getId('min', true));

        $html .= '<label for="' . $this->_getId('max') . '">' . JText::_('JBZOO_TO') . '</label>';
        $html .= $this->_html->text($this->_getName('max'), $value['max'], 'class="jbprice-filter-value-max"', $this->_getId('max', true));

        $html .= $this->renderCurrency();

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

        } elseif (is_array($this->_value)) {
            $min = $this->_value['min'];
            $max = $this->_value['max'];
        }

        $result = array(
            'min' => $min !== '' && $min !== null ? $this->app->jbvars->number($min) : '',
            'max' => $max !== '' && $max !== null ? $this->app->jbvars->number($max) : ''
        );

        return $result;
    }


}
