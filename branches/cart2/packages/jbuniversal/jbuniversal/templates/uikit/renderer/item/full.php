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


$align  = $this->app->jbitem->getMediaAlign($item, $layout);
$tabsId = $this->app->jbstring->getId('tabs');

?>

<div class="uk-panel uk-panel-box uk-clearfix">
    <?php if ($this->checkPosition('title')) : ?>
        <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
    <?php endif; ?>

    <div class="uk-grid">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="uk-width-medium-1-2">
                <div class="item-image">
                    <?php echo $this->renderPosition('image'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('meta')) : ?>
            <div class="uk-width-medium-1-2">
                <div class="item-metadata">
                    <ul class="uk-list">
                        <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="tab-container uk-article-divider">
    <ul class="uk-tab" data-uk-tab="{connect:'#<?php echo $tabsId; ?>'}">
        <?php if ($this->checkPosition('text')) : ?>
            <li>
                <a href="#tab-text">
                    <i class="uk-icon-info"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_DESCRIPTION'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <li>
                <a href="#tab-properties">
                    <i class="uk-icon-list"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_PROPS'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li>
                <a href="#tab-gallery">
                    <i class="uk-icon-image"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_GALLERY'); ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <ul class="uk-switcher uk-margin" id="<?php echo $tabsId; ?>">
        <?php if ($this->checkPosition('text')) : ?>
            <li id="tab-text">
                <div class="item-text">
                    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
                </div>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <ul class="item-properties">
                <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
            </ul>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li id="tab-gallery">
                <?php echo $this->renderPosition('gallery'); ?>
            </li>
        <?php endif; ?>
    </ul>
</div>