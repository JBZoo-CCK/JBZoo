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
        return $this->_html->checkbox(
            $this->_createOptionsList($values),
            $this->_getName(true, true),
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
        $values = $this->_getDbValues();
        if (!empty($values)) {
            $values = $this->_sortByArray($values);
        }

        return $values;
    }
}