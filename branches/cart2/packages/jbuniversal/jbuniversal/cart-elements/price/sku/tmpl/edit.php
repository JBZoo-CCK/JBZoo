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

$jbHTML  = $this->app->jbhtml;
$variant = $this->config->get('_variant') ? '' : '-variant';
$attr    = array(
    'class'       => 'discount' . $variant . '-input',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => 'Sku'
);

echo $jbHTML->text($this->getControlName('value'), $this->getValue(), $attr);
