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
 * Class JBPriceFilterElementBalance
 */
class JBPriceFilterElementBalance extends JBPriceFilterElement
{
    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $options = $this->_getValues();

        return $this->_html->buttonsJqueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * Get DB values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $values = (array)$this->_getDbValues();

        foreach ($values as $key => $value) {

            if ($value['value'] == JBCartElementPriceBalance::AVAILABLE) {
                $values[$key]['text'] = JText::_('JBZOO_FILTER_JBPRICE_BALANCE_AVAILABLE');
                continue;
            }

            if ($value['value'] == JBCartElementPriceBalance::COUNT_REQUEST) {
                $values[$key]['text'] = JText::_('JBZOO_ELEMENT_PRICE_BALANCE_EDIT_REQUEST');
                continue;
            }
        }

        return $values;
    }

    /**
     * Get data from db index table by element identifier
     * @return array
     */
    protected function _getDbValues()
    {
        $isCatDepend = (int)$this->_params->moduleParams->get('depend_category');
        $categoryId  = null;
        if ($isCatDepend) {
            $categoryId = $this->app->jbrequest->getSystem('category');
        }

        return JBModelValues::model()->getParamsValues(
            $this->_jbprice->identifier,
            $this->_identifier,
            $this->_params->get('item_type', null),
            $this->_params->get('item_application_id', null),
            $categoryId,
            -1
        );
    }
}
