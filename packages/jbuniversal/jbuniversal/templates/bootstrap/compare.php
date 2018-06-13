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

$this->app->jbdebug->mark('template::compare::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_COMPARE_ITEMS'));
$this->app->jbwrapper->start();

?><h1 class="title"><?php echo JText::_('JBZOO_COMPARE_ITEMS'); ?></h1><?php

if (!empty($this->items)) {
    // items
    echo '<div class="jbcompare-wrapper uk-panel uk-panel-box">';
    echo $this->app->jblayout->render('compare', $this->items);
    echo '</div>';

} else {
    echo '<p>' . JText::_('JBZOO_COMPARE_ITEMS_NOT_FOUND') . '</p>';
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::compare::finish');
