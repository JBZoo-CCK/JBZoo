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
 * Class JBPriceFilterElementValue
 */
class JBPriceFilterElementValue extends JBPriceFilterElement
{
    const TEMPLATE_SLIDER = 'slider';
    const TEMPLATE_RANGE  = 'range';
    const TEMPLATE_SIMPLE = 'simple';

    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $template = $this->_params->get('jbzoo_filter_template', self::TEMPLATE_SLIDER);
        $value    = $this->_prepareValues();
        $html     = null;

        if ($template == self::TEMPLATE_SLIDER) {

            $html = $this->renderSlider($value);

        } else if ($template == self::TEMPLATE_RANGE) {

            $html = $this->renderRange($value);

        } else if ($template == self::TEMPLATE_SIMPLE) {

            $html = $this->renderText($value);
        }

        if ($html) {
            $html .= $this->_renderCurrency();
        }

        return $html;
    }

    /**
     * Render slider template
     *
     * @param $value
     *
     * @return string
     */
    public function renderSlider($value)
    {
        $categoryId = null;
        $params     = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100),
        );

        if ($params['auto']) {

            $applicationId = (int)$this->_params->get('item_application_id', 0);
            $itemType      = $this->_params->get('item_type', null);

            $isCatDepend = (int)$this->_params->moduleParams->get('depend_category');
            if ($isCatDepend) {
                $categoryId = $this->app->jbrequest->getSystem('category');
            }

            $rangesData = (array)JBModelValues::model()
                ->getRangeByPrice($this->_jbprice->identifier, $itemType, $applicationId, $categoryId);

            $ranges = array(
                'min' => min((float)$rangesData['price_min'], (float)$rangesData['total_min']),
                'max' => max((float)$rangesData['price_max'], (float)$rangesData['total_max']),
            );

            $from = $this->_config->get('currency_default', 'EUR');
            $to   = $this->_params->get('jbzoo_filter_currency_default', 'EUR');

            $ranges['min'] = floor($this->money->convert($from, $to, $ranges['min']));
            $ranges['max'] = ceil($this->money->convert($from, $to, $ranges['max']));

            $params = array_merge($params, array('min' => $ranges['min'], 'max' => $ranges['max']));
        }

        $html = '<div class="jbslider">' .
                $this->html->slider($params, $value['range'], $this->_getName('range'),
                    $this->_getId('range', true)) .
                '</div>';

        return $html;
    }

    /**
     * Render range template
     *
     * @param $value
     *
     * @return string
     */
    public function renderRange($value)
    {
        $html = '<label for="' . $this->_getId('val_min') . '">' . JText::_('JBZOO_FROM') . '</label>';

        $html .= $this->html->text($this->_getName('val_min'), $value['val_min'], 'class="val_min"',
            $this->_getId('val_min'));

        $html .= '<label for="' . $this->_getId('val_max') . '">' . JText::_('JBZOO_TO') . '</label>';

        $html .= $this->html->text($this->_getName('val_max'), $value['val_max'], 'class="val_max"',
            $this->_getId('val_max'));

        return '<div class="jbprice-ranges">' . $html . '</div>';
    }

    /**
     * Render simple template
     *
     * @param $value
     *
     * @return string
     */
    public function renderText($value)
    {
        return '<label for="' . $this->_getId('val') . '">' . JText::_('JBZOO_FILTER_JBPRICE_VALUE') . '</label>' .
               $this->html->text($this->_getName('val'), $value['val'], 'class="val"', $this->_getId('val'));
    }

    /**
     * Render hidden input width currency value from config.
     *
     * @return null|string
     */
    protected function _renderCurrency()
    {
        $html = null;

        if ($currency = $this->_params->get('jbzoo_filter_currency_default', 'EUR')) {
            $html .= $this->html->hidden($this->_getName('currency'), $currency);
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
