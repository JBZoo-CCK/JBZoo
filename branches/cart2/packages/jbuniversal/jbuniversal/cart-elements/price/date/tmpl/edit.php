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

$string = $this->app->jbstring;
$unique = $string->getId('calendar-');

if ($value = $this->getValue()) {
    try {
        $value = $this->app->html->_('date',
            $value,
            $this->app->date->format(JBCartElementPriceDate::EDIT_DATE_FORMAT),
            $this->app->date->getOffset()
        );
    } catch (Exception $e) {
    }
}

echo $this->app->html->_('zoo.calendar', $value, $this->getControlName('value'), $unique, array(
    'class' => $string->getId('calendar-element-')
), true);



