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
 * Class JBPriceFilterElementJQueryUI
 */
class JBPriceFilterElementJQueryUI extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $values = $this->_getValues();
        $this->_attrs['id'] = $this->_getId(time(), true);

        return $this->_html->buttonsJqueryUI(
            $this->_createOptionsList($values),
            $this->_getName(true, $this->_isMultiple),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * @param null $type
     * @return array
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
