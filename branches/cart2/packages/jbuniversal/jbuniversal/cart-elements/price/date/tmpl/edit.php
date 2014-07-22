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

$variant = (int)$params->get('basic', 0) ? '' : '-variant';
$unique  = $this->app->jbstring->getId('calendar-');

if ($value = $this->getValue('_date', '')) {
    try {

        $value = $this->app->html->_('date', $value, $this->app->date->format($format), $this->app->date->getOffset());

    } catch (Exception $e) {
    }
}
echo $this->app->html->_('zoo.calendar', $value, $this->getName('_date'), $unique, array(
    'class' => $this->app->jbstring->getId('calendar-element-')
), true);



