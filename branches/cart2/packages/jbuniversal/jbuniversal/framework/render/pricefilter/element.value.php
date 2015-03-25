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
            $html = $this->renderSlider();

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
     * @return string
     */
    public function renderSlider()
    {
        /*
         *         $categoryId = null;
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

            $rangesData = (array)JBModelValues::model()->getRangeByPrice(
                $this->_jbprice->identifier,
                $itemType,
                $applicationId,
                $categoryId
            );

            $ranges = array(
                'min' => (float)$rangesData['total_min'],
                'max' => (float)$rangesData['total_max'],
            );

            $from = $this->money->getDefaultCur();
            $to   = $this->_params->get('jbzoo_filter_currency_default', 'EUR');

            $ranges['min'] = floor($this->money->convert($from, $to, $ranges['min']));
            $ranges['max'] = ceil($this->money->convert($from, $to, $ranges['max']));

            $params = array_merge($params, array(
                'min' => $ranges['min'],
                'max' => $ranges['max']
            ));
        }

        $html = '<div class="jbslider">' .
            $this->html->slider($params, $value['range'], $this->_getName('range'),
                $this->_getId('range', true)) .
            '</div>';

        return $html;
         */
        $categoryId = $min = $max = null;
        $params     = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100),
        );
        $cur = JBCart::val();
        if ($params['auto']) {
            $applicationId = (int)$this->_params->get('item_application_id', 0);
            $isCatDepend   = (int)$this->_params->moduleParams->get('depend_category');

            $itemType = $this->_params->get('item_type', null);
            if ($isCatDepend) {
                $categoryId = $this->app->jbrequest->getSystem('category');
            }

            $rangesData = (array)JBModelValues::model()->getRangeByPrice(
                $this->_jbprice->identifier,
                $itemType,
                $applicationId,
                $categoryId
            );
            $to  = $this->_params->get('jbzoo_filter_currency_default', 'EUR');

            $min = JBCart::val($rangesData['total_min'] . $cur->cur());
            $max = JBCart::val($rangesData['total_max'] . $cur->cur());

            $min_str = floor($min->convert($to)->val());
            $max_str = ceil($max->convert($to)->val());
            $params  = array_merge($params, array(
                'min' => $min_str,
                'max' => $max_str
            ));
        }
        $id   = $this->_getId('range', true);
        $html = '<div class="' . $id . '-jbslider">' .
            $this->html->slider($params, array($min, $max), $this->_getName(null, 'range'), $id, $cur->cur()) .
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
        $html = '<label for="' . $this->_getId('min') . '">' . JText::_('JBZOO_FROM') . '</label>';

        $html .= $this->html->text($this->_getName('min', 0), $value['min'], 'class="val_min"',
            $this->_getId('min'));

        $html .= '<label for="' . $this->_getId('max') . '">' . JText::_('JBZOO_TO') . '</label>';

        $html .= $this->html->text($this->_getName('max', 0), $value['max'], 'class="val_max"',
            $this->_getId('max'));

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
        $this->html->text($this->_getName('value'), $value['value'], 'class="val"', $this->_getId('val'));
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
            'value'   => null,
            'min'     => null,
            'max'     => null,
            'range'   => null,
        ), $this->_value);
    }

}
