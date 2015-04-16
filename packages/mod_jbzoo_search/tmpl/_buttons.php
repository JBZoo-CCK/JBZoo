<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$btnClass = isset($btnClass) ? $btnClass : 'jbbutton';

$submitShow = (int)$params->get('button_submit_show', 1);
$resetShow  = (int)$params->get('button_reset_show', 1);

if ($submitShow || $resetShow) {

    echo '<div class="jbfilter-row jbfilter-buttons">';

    if ($submitShow) {
        $attrs = array(
            'type'  => 'submit',
            'name'  => 'send-form',
            'value' => JText::_('JBZOO_BUTTON_SUBMIT'),
            'class' => array(
                'jsSubmit',
                $btnClass
            ),
        );

        echo '<input ' . $modHelper->attrs($attrs) . ' /> ';
    }

    if ($resetShow) {
        $attrs = array(
            'type'  => 'reset',
            'name'  => 'reset-form',
            'value' => JText::_('JBZOO_BUTTON_RESET'),
            'class' => array(
                'jsReset',
                $btnClass
            ),
        );

        echo '<input ' . $modHelper->attrs($attrs) . ' /> ';
    }

    echo JBZOO_CLR;
    echo '</div>';
}
