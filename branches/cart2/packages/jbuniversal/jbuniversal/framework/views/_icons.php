<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!isset($items) || empty($items)) {
    return false;
}

?>
<div class="application-list">

    <?php

    $html = array();

    foreach ($items as $item) {

        $img   = '';
        $attrs = '';
        $attrs = '';

        // get name
        $name = JText::_($item['name']);

        // get link
        if (isset($item['link'])) {
            $url = $this->app->jbrouter->admin($item['link']);
        } elseif (isset($item['url'])) {
            $url   = $item['url'];
            $attrs = 'target="_blank"';
        }

        // get icon path
        if (isset($item['icon'])) {
            $img = $this->app->jbimage->pathToUrl('jbassets:img/cpanel/' . $item['icon']);
        } elseif (isset($item['iconPath'])) {
            $img = $item['iconPath'];
        }

        $html[] = '<a ' . $attrs . ' href="' . $url . '" title="' . $name . '">' .
            '<span><img src="' . $img . '" alt="' . $name . '"><p>' .
            $name . '</p></span></a>';
    }

    echo implode("\r ", $html);
    ?>
</div>
