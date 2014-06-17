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
 * Class JBFilterElementNameSelectChosen
 */
class JBFilterElementNameSelectChosen extends JBFilterElementNameSelect
{

    /**
     * Render HTML
     * @return string
     */
    function html()
    {
        $values = $this->_getValues();

        return $this->app->jbhtml->selectChosen(
            $this->_createOptionsList($values),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId(),
            false,
            array('placeholder' => $this->_getPlaceholder())
        );
    }

}