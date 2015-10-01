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
 * Class JBPriceFilterElementValue
 */
class JBPriceFilterElementValue extends JBPriceFilterElement
{
    /**
     * @return string
     */
    public function html()
    {
        $value = $this->_prepareValues();

        $html = $this->_html->text(
            $this->_getName('value'),
            $value['value'],
            'class="jbprice-filter-value"',
            $this->_getId('val')
        );

        $html .= $this->renderCurrency();

        return $html;
    }

    /**
     * @return string
     */
    public function renderCurrency()
    {
        return $this->_html->hidden($this->_getName('currency'), $this->_getCurrency());
    }

    /**
     * Get name
     * @param string $key
     * @param  bool  $postFix
     * @return string
     */
    protected function _getName($key = null, $postFix = null)
    {
        return parent::_getName() . '[' . $key . ']' . ($postFix !== null ? '[' . $postFix . ']' : null);
    }

    /**
     * @return mixed
     */
    protected function _getCurrency()
    {
        return $this->_params->get('jbzoo_filter_currency_default', 'default_cur');
    }

    /**
     * Prepare values
     * @return array
     */
    protected function _prepareValues()
    {
        if (empty($this->_value) || !is_array($this->_value)) {
            $this->_value = array();
        }

        return array_merge(array(
            'sku'     => null,
            'balance' => null,
            'sale'    => null,
            'new'     => null,
            'hit'     => null,
            'value'   => null,
            'min'     => null,
            'max'     => null,
            'range'   => null,
        ), $this->_value);
    }
}
