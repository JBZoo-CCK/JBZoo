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

$link = '<a target="_blank" href="'
    . $link . '">'
    . $message . '</a>';
echo '<em>'
    . JText::sprintf('JBZOO_PRICE_EDIT_ERROR_NO_ELEMENTS', $this->app->jbenv->isSite() ? '' : $link)
    . '</em>';