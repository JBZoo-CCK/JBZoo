<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
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
            $this->_createOptionsList($options),
            $this->_getName('balance'),
            $this->_attrs,
            $this->_value['balance'],
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
        return array(
            array(
                'text'  => JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER'),
                'value' => -2
            ),
            array(
                'text'  => JText::_('JBZOO_JBPRICE_AVAILABLE'),
                'value' => -1
            ),
            array(
                'text'  => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE'),
                'value' => 0
            ),
        );
    }

}
