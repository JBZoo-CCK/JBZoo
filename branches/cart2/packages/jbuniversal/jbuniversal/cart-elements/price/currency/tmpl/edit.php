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

if (count($currencyList) == 1) {
    reset($currencyList);
    $currency = current($currencyList);
    echo $currency, $this->app->jbhtml->hidden($this->getControlName('value'), $currency, 'class="basic-currency"');
} else {
    echo $this->app->jbhtml->select($currencyList, $this->getControlName('value'), 'class="basic-currency"', $this->getValue('value'));
}