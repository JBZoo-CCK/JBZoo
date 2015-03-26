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
 * Class JBPriceFilterElementCheckbox
 */
class JBPriceFilterElementCheckbox extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $this->_isMultiple = true;

        $values = $this->_getValues();
        if (is_array($this->_value) && count($this->_value) > 1) {
            $this->_value = JArrayHelper::getColumn($this->_value, 'id');
        }

        return $this->html->checkbox(
            $this->_createOptionsList($values),
            $this->_getName('id', ''),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * Get DB values
     * @param null $type
     * @return array|mixed|null
     */
    protected function _getValues($type = null)
    {
        return $this->_getDbValues();
    }
}