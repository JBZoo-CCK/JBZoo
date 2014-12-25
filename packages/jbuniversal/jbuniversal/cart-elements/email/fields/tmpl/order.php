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

if (!empty($fields)) {
    foreach ($fields as $id) {
        if ($element = $this->_order->getFieldElement($id)) {
            $name  = $element->config->get('name');
            $value = $element->get('value');

            echo '<strong>' . $name . ': </strong>' . $value . "\n";
        }
    }
}
