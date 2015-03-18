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

$list = $this->_getTypeList();

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_NEWPOST_DELIVERYTYPE_ID_VALUE' => $list[$this->get('deliveryType_id')],
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_REGION_VALUE'          => $this->get('region'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_RECIPIENTCITY_VALUE'   => $this->get('recipientCity'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_WAREHOUSE_VALUE'       => $this->get('warehouse'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_STREET_VALUE'          => $this->get('street'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_FLOOR_COUNT_VALUE'     => $this->get('floor_count'),
));
