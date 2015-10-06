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
 * Class JBPriceFilterElementValueSliderInput
 */
class JBPriceFilterElementValueSliderInput extends JBPriceFilterElementValueSlider
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $html       = array();
        $value      = $this->_prepareValues();
        $categoryId = $min = $max = null;

        $params = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100)
        );

        $to = $this->_params->get('jbzoo_filter_currency_default', 'default_cur');

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

            $cur = JBModelConfig::model()->getCurrency();

            $params['min'] = JBCart::val($rangesData['total_min'] ? $rangesData['total_min'] . $cur : $params['min'])->val($to);
            $params['max'] = JBCart::val($rangesData['total_max'] ? $rangesData['total_max'] . $cur : $params['max'])->val($to);
        }

        $html[] = $this->_html->sliderInput($params, $value['range'], $this->_getName('range'), $this->app->jbstring->getId('jsSlider-'), $to);
        $html[] = $this->renderCurrency();

        return implode(PHP_EOL, $html);
    }
}
