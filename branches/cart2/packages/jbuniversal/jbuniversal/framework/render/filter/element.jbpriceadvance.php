<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBFilterElementJBPriceAdvance
 */
class JBFilterElementJBPriceAdvance extends JBFilterElement
{
    /**
     * Get main attrs
     * @param array $attrs
     * @return array
     */
    protected function _getAttrs(array $attrs)
    {
        $attrs = parent::_getAttrs($attrs);

        if ((int)$this->_params->get('jbzoo_filter_autocomplete', 0)) {
            $attrs['class'][]     = 'jsAutocomplete';
            $attrs['placeholder'] = $this->_getPlaceholder();
        }

        return $attrs;
    }

    /**
     * Get placeholder text
     * @return string
     */
    protected function _getPlaceholder()
    {
        $default     = JText::_('JBZOO_FILTER_PLACEHOLDER_DEFAULT_SKU');
        $placeholder = JString::trim($this->_params->get('jbzoo_filter_placeholder', $default));
        if (!$placeholder) {
            $placeholder = $default;
        }

        return $placeholder;
    }

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $values = $this->_prepareValues();

        $html = array();

        if ($valueTmpl = (int)$this->_params->get('jbzoo_filter_value', 1)) {
            $html[] = $this->_renderValueControl($values, $valueTmpl);
        }

        if ((int)$this->_params->get('jbzoo_filter_sku', 1)) {
            $html[] = '<label for="' . $this->_getId('val') . '">' . JText::_('JBZOO_FILTER_JBPRICE_SKU') . '</label>' .
                $this->_jbhtml->text($this->_getName('sku'), $values['sku'], $this->_attrs, $this->_getId('sku'));
        }

        if ((int)$this->_params->get('jbzoo_filter_balance', 1)) {
            $options = array('1' => JText::_('JBZOO_FILTER_JBPRICE_BALANCE_CHECKBOX'));
            $html[]  = $this->_jbhtml->checkbox($options, $this->_getName('balance'), '', $values['balance'], $this->_getId('balance'));
        }

        if ((int)$this->_params->get('jbzoo_filter_sale', 1)) {
            $options = array('1' => JText::_('JBZOO_FILTER_JBPRICE_SALE_CHECKBOX'));
            $html[]  = $this->_jbhtml->checkbox($options, $this->_getName('sale'), '', $values['sale'], $this->_getId('sale'));
        }

        if ((int)$this->_params->get('jbzoo_filter_new', 1)) {
            $options = array('1' => JText::_('JBZOO_FILTER_JBPRICE_NEW_CHECKBOX'));
            $html[]  = $this->_jbhtml->checkbox($options, $this->_getName('new'), '', $values['new'], $this->_getId('new'));
        }

        if ((int)$this->_params->get('jbzoo_filter_hit', 1)) {
            $options = array('1' => JText::_('JBZOO_FILTER_JBPRICE_HIT_CHECKBOX'));
            $html[]  = $this->_jbhtml->checkbox($options, $this->_getName('hit'), '', $values['hit'], $this->_getId('hit'));
        }

        if (!empty($html)) {
            $result = '<div class="filter-element-row">' .
                implode("<div class=\"clear clr\"></div></div>\n <div class=\"filter-element-row\">", $html) .
                '<div class="clear clr"></div></div>';

            return $result;
        }

        return null;
    }

    /**
     * Render value controls
     * @param array $values
     * @param int $valueTmpl
     * @return string
     */
    protected function _renderValueControl($values, $valueTmpl)
    {
        $html = '';

        $valueType = (int)$this->_params->get('jbzoo_filter_value_type', 0);
        $priceType = (int)$this->_params->get('jbzoo_filter_price_type', 0);

        if ($valueTmpl == 1) {
            $html = '<label for="' . $this->_getId('val') . '">' . JText::_('JBZOO_FILTER_JBPRICE_VALUE') . '</label>' .
                $this->_jbhtml->text($this->_getName('val'), $values['val'], 'class="val"', $this->_getId('val'));
        }

        if ($valueTmpl == 2) {
            $htmlRange   = array();
            $htmlRange[] = '<label for="' . $this->_getId('val_min') . '">' . JText::_('JBZOO_FROM') . '</label>';
            $htmlRange[] = $this->_jbhtml->text($this->_getName('val_min'), $values['val_min'], 'class="val_min"', $this->_getId('val_min'));;
            $htmlRange[] = '<label for="' . $this->_getId('val_max') . '">' . JText::_('JBZOO_TO') . '</label>';
            $htmlRange[] = $this->_jbhtml->text($this->_getName('val_max'), $values['val_max'], 'class="val_max"', $this->_getId('val_max'));

            $html = '<div class="jbprice-ranges">' . implode("\n ", $htmlRange) . '</div>';
        }

        if ($valueTmpl == 3) {
            $params = array(
                'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
                'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
                'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
                'step' => $this->_params->get('jbzoo_filter_slider_step', 100),
            );

            if ($params['auto']) {
                $applicationId = (int)$this->_params->get('item_application_id', 0);
                $itemType      = $this->_params->get('item_type', null);
                $rangesData    = (array)JBModelValues::model()->getRangeByPrice($this->_identifier, $itemType, $applicationId);

                if ($valueType == 1) {
                    $ranges = array('min' => $rangesData['price_min'], 'max' => $rangesData['price_max']);
                } else if ($valueType == 2) {
                    $ranges = array('min' => $rangesData['total_min'], 'max' => $rangesData['total_max']);
                } else {
                    $ranges = array(
                        'min' => min((float)$rangesData['price_min'], (float)$rangesData['total_min']),
                        'max' => max((float)$rangesData['price_max'], (float)$rangesData['total_max']),
                    );
                }

                $from = $this->_config->get('currency_default', 'EUR');
                $to   = $this->_params->get('jbzoo_filter_currency_default', 'EUR');

                $ranges['min'] = floor($this->app->jbmoney->convert($from, $to, $ranges['min']));
                $ranges['max'] = ceil($this->app->jbmoney->convert($from, $to, $ranges['max']));

                $params = array_merge($params, array('min' => $ranges['min'], 'max' => $ranges['max']));
            }

            $html = '<div class="jbslider">' .
                $this->_jbhtml->slider($params, $values['range'], $this->_getName('range'), $this->_getId('range', true)) .
                '</div>';
        }

        if ($currency = $this->_params->get('jbzoo_filter_currency_default', 'EUR')) {
            $html .= $this->_jbhtml->hidden($this->_getName('currency'), $currency);
        }

        if ($valueType) {
            $html .= $this->_jbhtml->hidden($this->_getName('val_type'), $valueType);
        }

        if ($priceType) {
            $html .= $this->_jbhtml->hidden($this->_getName('price_type'), $priceType);
        }

        return $html;
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
            'val'     => null,
            'val_min' => null,
            'val_max' => null,
            'range'   => null,
        ), $this->_value);
    }

}
