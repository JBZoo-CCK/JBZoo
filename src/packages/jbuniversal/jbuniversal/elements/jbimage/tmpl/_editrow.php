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

?>
<div>

    <div class="row">
        <?php echo $this->app->html->_('control.text', $this->getControlName('file'), $this->get('file'), 'class="jbimage-select" size="60" style="width:200px;margin-right:5px;" title="' . JText::_('File') . '"'); ?>
    </div>

    <div class="more-options">

        <div class="trigger">
            <div>
                <div class="link button"><?php echo JText::_('Link'); ?></div>
                <div class="title button"><?php echo JText::_('Title'); ?></div>
            </div>
        </div>

        <div class="title options">

            <div class="row">
                <?php echo $this->app->html->_('control.text', $this->getControlName('title'), $this->get('title'), 'maxlength="255" title="' . JText::_('Title') . '" placeholder="' . JText::_('Title') . '"'); ?>
            </div>

        </div>

        <div class="link options">

            <div class="row">
                <?php echo $this->app->html->_('control.text', $this->getControlName('link'), $this->get('link'), 'size="60" maxlength="255" title="' . JText::_('Link') . '" placeholder="' . JText::_('Link') . '"'); ?>
            </div>

            <div class="row">
                <strong><?php echo JText::_('New window'); ?></strong>
                <?php echo $this->app->html->_('select.booleanlist', $this->getControlName('target'), $this->get('target'), $this->get('target')); ?>
            </div>

            <div class="row">
                <?php echo $this->app->html->_('control.text', $this->getControlName('rel'), $this->get('rel'), 'size="60" maxlength="255" title="' . JText::_('Rel') . '" placeholder="' . JText::_('Rel') . '"'); ?>
            </div>

        </div>
    </div>
</div>
