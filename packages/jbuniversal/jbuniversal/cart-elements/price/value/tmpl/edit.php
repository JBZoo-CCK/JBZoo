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

$elId = $this->app->jbstring->getId('value-');

echo $this->app->html->_('control.text', $this->getName('_value'), $this->getValue($this->identifier), array(
    'size'        => '10',
    'maxlength'   => '255',
    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
    'id'          => $elId . '-basic-value',
    'class'       => 'basic-value',
));

if (count($currencyList) == 1) {
    reset($currencyList);
    $currency = current($currencyList);
    echo $currency, $this->app->jbhtml->hidden($this->getName('_currency'), $currency, 'class="basic-currency"');
} else {
    echo $this->app->jbhtml->select($currencyList, $this->getName('_currency'), 'class="basic-currency"', $this->getValue('_currency'));
}
