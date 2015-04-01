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
 * Class JBPriceFilterElementBalanceChosen
 */
class JBPriceFilterElementBalanceChosen extends JBPriceFilterElementBalance
{

    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $options = $this->_getValues();

        return $this->_html->selectChosen(
            $options,
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId('balance')
        );
    }

}
