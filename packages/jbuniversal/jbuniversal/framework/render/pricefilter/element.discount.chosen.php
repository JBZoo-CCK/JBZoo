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
 * Class JBPriceFilterElementDiscountChosen
 */
class JBPriceFilterElementDiscountChosen extends JBPriceFilterElementDiscount
{

    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $options = $this->_getValues();

        return $this->html->selectChosen(
            $this->_createOptionsList($options),
            $this->_getName('discount'),
            $this->_attrs,
            $this->_value['discount'],
            $this->_getId('discount')
        );
    }

}
