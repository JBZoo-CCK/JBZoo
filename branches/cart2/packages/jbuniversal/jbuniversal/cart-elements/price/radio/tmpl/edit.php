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

if (count($options)) {
    $value  = null;
    $radio  = array();
    $value  = $this->getValue($this->identifier);
    $jbhtml = $this->app->jbhtml;

    foreach ($options as $key => $option) {

        $radio[] = $this->app->html->_('select.option', $this->app->string->sluggify($option['value']), $option['name']);
    }


    echo $this->app->html->_('select.radiolist', $radio, $this->getName(), null, 'value', 'text', $value['value']);

}