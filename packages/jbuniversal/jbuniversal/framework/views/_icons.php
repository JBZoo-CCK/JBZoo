<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
