<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Oganov Alexander <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$attributes = array(
    'title' => $label,
    'class' => array(
        'jbcolor',
        'jbcolor-alphabetic',
        (!$isFile ? 'dye-' . $color : '')
    )
);

$spanAttrs = $this->app->jbhtml->buildAttrs($attributes);

echo '<span ' . $spanAttrs . '>'.$label.'</span>';
