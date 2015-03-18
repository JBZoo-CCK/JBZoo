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

$typeList = $this->_getTypePostList();

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_TYPE' => $typeList[$this->get('typePost')],
    'JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_ZIP'  => $this->get('postOfficeId'),
));
