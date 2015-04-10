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

$html = array();
foreach ($data as $optionName => $optionVal) {
    if ($optionName) {
        $className  = $this->app->string->sluggify($optionName);
        $optionName = JString::ucfirst($optionName);
        $html[]     = '<span class="jbprice-option-' . $className . '">' . $optionName . '</span>';
    }
}

echo '<span class="jbprice-option-text">' . implode(', ', $html) . '</span>';
