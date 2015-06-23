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

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<div class="uk-panel uk-panel-box uk-clearfix">
    <div class="uk-grid">
        <div class="uk-width-medium-1-2">
            <?php if ($this->checkPosition('image')) : ?>
                <div class="item-image uk-divider">
                    <?php echo $this->renderPosition('image'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('meta')) : ?>
                <div class="item-metadata">
                    <ul class="uk-list">
                        <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('buttons')) : ?>
                <div class="item-buttons uk-clearfix">
                    <?php echo $this->renderPosition('buttons', array('style' => 'block')); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($this->checkPosition('price')) : ?>
            <div class="uk-width-medium-1-2">
                <div class="item-price">
                    <?php echo $this->renderPosition('price'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('social')) : ?>
        <div class="uk-grid item-social">
            <div class="uk-width-medium-1-1">
                <?php echo $this->renderPosition('social', array('style' => 'block')); ?>
            </div>
        </div>
    <?php endif; ?>
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

        <?php if ($this->checkPosition('comments')) : ?>
            <li>
                <a href="#tab-comments">
                    <?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                    <span class="badge"><?php echo $item->getCommentsCount(); ?></span>
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
            <table class="uk-table uk-table-hover uk-table-striped">
                <?php echo $this->renderPosition('properties', array(
                    'tooltip' => true,
                    'style'   => 'jbtable',
                )); ?>
            </table>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li id="tab-gallery">
                <?php echo $this->renderPosition('gallery', array(
                    'labelTag' => 'h4',
                    'style'    => 'jbblock',
                )); ?>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <li id="tab-comments">
                <?php echo $this->renderPosition('comments'); ?>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php if ($this->checkPosition('related')) : ?>
    <div class="uk-grid item-related">
        <div class="uk-width-medium-1-1">
            <?php echo $this->renderPosition('related', array(
                'labelTag' => 'h4',
                'style'    => 'jbblock',
            )); ?>
        </div>
    </div>
<?php endif; ?>