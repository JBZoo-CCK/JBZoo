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

$data = (array)$this->data();
//unset($data['rate']);
//echo '<pre>', print_r($data, true), '</pre>';

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_PAYMENT_DATE'      => $this->get('payment_date'),
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_PAYMENT_SYSTEM'    => $this->get('payment_system'),
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_ADDITIONAL_STATUS' => $this->get('additional_status'),
));
