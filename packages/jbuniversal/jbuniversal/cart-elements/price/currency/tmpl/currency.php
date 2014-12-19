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

if (count($list) > 1) {
    $currencies = array_intersect_key((array)$this->_jbmoney->getData(), array_flip($list));

    echo $this->app->jbhtml->currencyToggle($currencies, $default, array(
        'target'      => $this->parentSelector(),
        'showDefault' => true
    ));
}