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

$app  = $this->app;
$html = $app->jbhtml;

$currencyList = $app->jbmoney->getCurrencyList();
$currencyList = $app->jbarray->unshiftAssoc($currencyList, '%', '%');

$variant = $this->config->get('_variant') ? '' : '-variant';

$value    = $this->getValue('value');
$currency = $this->getValue('currency');

echo $html->text($this->getControlName('value'), $value ? $value : 0, array(
    'class'       => 'discount' . $variant . '-input',
    'size'        => "60",
    'maxlength'   => "255",
    'placeholder' => 'скидка'
));

echo $html->select($currencyList, $this->getControlName('currency'), array(
    'class' => 'discount-currency' . $variant
), $currency);


