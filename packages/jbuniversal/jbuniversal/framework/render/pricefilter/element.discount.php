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
 * Class JBPriceFilterElementDiscount
 */
class JBPriceFilterElementDiscount extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $this->_isMultiple = false;
        $options = $this->_getValues();

        return $this->_html->radio(
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
        $values  = (array)$this->_getDbValues();
        $default = array(
            '1' => array(
                'text'  => JText::_('JBZOO_FILTER_JBPRICE_SALE_CHECKBOX'),
                'value' => 1
            ),
            '0' => array(
                'text'  => JText::_('JBZOO_FILTER_JBPRICE_SALE_NO'),
                'value' => 0
            )
        );

        foreach ($values as $key => $value) {
            if (isset($default[$value['text']])) {
                $values[$key]['text']  = $default[$value['text']]['text'];
                $values[$key]['value'] = $default[$value['text']]['value'];
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
