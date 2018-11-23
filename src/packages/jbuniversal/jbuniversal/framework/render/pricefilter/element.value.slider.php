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
 * Class JBPriceFilterElementValueSlider
 */
class JBPriceFilterElementValueSlider extends JBPriceFilterElementValue
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $html  = array();
        $value = $this->_prepareValues();

        $html[] = $this->_html->slider(
            $this->_getSliderParams(),
            $value['range'],
            $this->_getName('range'),
            $this->app->jbstring->getId('jsSlider-')
        );
        $html[] = $this->renderCurrency();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return array
     */
    protected function _getSliderParams()
    {
        $categoryId = $min = $max = null;

        $to = $this->_getCurrency();

        $params = array(
            'auto' => (int)$this->_params->get('jbzoo_filter_slider_auto', 0),
            'min'  => $this->_params->get('jbzoo_filter_slider_min', 0),
            'max'  => $this->_params->get('jbzoo_filter_slider_max', 10000),
            'step' => $this->_params->get('jbzoo_filter_slider_step', 100)
        );

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

            $params['min'] = JBCart::val($rangesData['total_min'], $cur)->val($to);
            $params['max'] = JBCart::val($rangesData['total_max'], $cur)->val($to);
        }

        return $params;
    }

}
