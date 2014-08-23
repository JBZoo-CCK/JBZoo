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

$jbhtml  = $this->app->jbhtml;
$variant = (int)$params->get('basic', 0) ? '' : '-variant';
$attr    = array(
    'class'       => 'discount' . $variant . '-input',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => 'Sku'
);

echo $jbhtml->text($this->getControlName('_sku'), $this->getValue('_sku', 0), $attr);


