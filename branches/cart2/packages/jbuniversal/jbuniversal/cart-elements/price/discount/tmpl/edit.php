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

$app    = $this->app;
$jbhtml = $app->jbhtml;

$currencyList = $app->jbmoney->getCurrencyList();
$currencyList = $app->jbarray->unshiftAssoc($currencyList, '%', '%');

$variant = (int)$params->get('basic', 0) ? '' : '-variant';
$value   = $this->getValue('_discount');

echo $jbhtml->text($this->getName('value'), $value['value'], array(
    'class'       => 'discount' . $variant . '-input',
    'size'        => "60",
    'maxlength'   => "255",
    'placeholder' => 'discount'
));

echo $jbhtml->select($currencyList, $this->getName('currency'), array(
    'class' => 'discount-currency' . $variant
), $value['currency']);


