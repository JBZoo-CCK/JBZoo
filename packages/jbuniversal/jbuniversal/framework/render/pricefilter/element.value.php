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
            $html = $this->_renderSlider($value);

        } else if ($template == self::TEMPLATE_RANGE) {
            $html = $this->_renderRange($value);

        } else if ($template == self::TEMPLATE_SIMPLE) {
            $html = $this->_renderText($value);
        }

        if ($html) {
            $html .= $this->_renderCurrency();
        }

        return $html;
    }

    /**
     * Render slider template
     * @param $value
     * @return string
     */
    protected function _renderSlider($value)
    {
        $categoryId = $min = $max = null;
        $params     = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100),
        );

        $to = $this->_params->get('jbzoo_filter_currency_default', 'EUR');

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

            $min = JBCart::val($rangesData['total_min'])->convert($to);
            $max = JBCart::val($rangesData['total_max'])->convert($to);

            $params = array_merge($params, array(
                'min' => $min->val(),
                'max' => $max->val()
            ));
        }

        $html = $this->_html->sliderInput($params, $value['range'], $this->_getName('range'), $this->app->jbstring->getId('jsSlider-'), $to);

        return $html;
    }

    /**
     * Render range template
     * @param $value
     * @return string
     */
    protected function _renderRange($value)
    {
        $html = '<label for="' . $this->_getId('min') . '">' . JText::_('JBZOO_FROM') . '</label>';
        $html .= $this->_html->text($this->_getName('min'), $value['min'], 'class="val_min"', $this->_getId('min'));

        $html .= '<label for="' . $this->_getId('max') . '">' . JText::_('JBZOO_TO') . '</label>';
        $html .= $this->_html->text($this->_getName('max'), $value['max'], 'class="val_max"',
            $this->_getId('max'));

        return '<div class="jbprice-ranges">' . $html . '</div>';
    }

    /**
     * Render simple template
     * @param $value
     * @return string
     */
    protected function _renderText($value)
    {
        return $this->_html->text($this->_getName('value'), $value['value'], 'class="val"', $this->_getId('val'));
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
     * Render hidden input width currency value from config.
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
