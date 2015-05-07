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
        $values  = (array)$this->_getDbValues();
        $default = array(
            '1' => array(
                'text'  => JText::_('JBZOO_FILTER_JBPRICE_BALANCE_AVAILABLE'),
                'value' => 1
            )
        );

        foreach ($values as $key => $value) {
            if (isset($default[$value['value']])) {
                $default[$value['value']]['count'] = $value['count'];
            }
        }

        return $default;
    }

    /**
     * Get data from db index table by element identifier
     * @return array
     */
    protected function _getDbValues()
    {
        return JBModelValues::model()->getParamsValues(
            $this->_jbprice->identifier,
            $this->_identifier,
            $this->_params->get('item_type', null),
            $this->_params->get('item_application_id', null),
            '-1'
        );
    }
}
