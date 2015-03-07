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

        return $this->html->buttonsJQueryUI(
            $options,
            $this->_getName(null, 'id'),
            $this->_attrs,
            $this->_value,
            $this->_getId('balance')
        );
    }

    /**
     * Get DB values
     *
     * @param null $type
     *
     * @return array
     */
    protected function _getValues($type = null)
    {
        $default = array(
            '-2' => JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER'),
            '-1' => JText::_('JBZOO_JBPRICE_AVAILABLE'),
            '0'  => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE')
        );

        return $default;
    }

}
