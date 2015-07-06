<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$align      = $this->app->jbitem->getMediaAlign($item, $layout);
$tabsId     = $this->app->jbstring->getId('tabs');
$bootstrap = $this->app->jbbootstrap;
$rowClass   = $bootstrap->getRowClass();
?>

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<div class="well clearfix">
    <div class="<?php echo $rowClass; ?>">
        <div class="<?php echo $bootstrap->gridClass(6); ?>">
            <?php if ($this->checkPosition('image')) : ?>
                <div class="item-image">
                    <?php echo $this->renderPosition('image'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('meta')) : ?>
                <div class="<?php echo $rowClass; ?> item-metadata">
                    <div class="<?php echo $bootstrap->gridClass(12); ?>">
                        <ul class="unstyled">
                            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('buttons')) : ?>
                <div class="<?php echo $rowClass; ?> item-buttons">
                    <div class="<?php echo $bootstrap->gridClass(12); ?>">
                        <?php echo $this->renderPosition('buttons', array('style' => 'block')); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($this->checkPosition('price')) : ?>
            <div class="<?php echo $bootstrap->gridClass(6); ?>">
                <div class="item-price">
                    <?php echo $this->renderPosition('price'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('social')) : ?>
        <div class="<?php echo $rowClass; ?> item-social">
            <div class="<?php echo $bootstrap->gridClass(12); ?>">
                <?php echo $this->renderPosition('social', array('style' => 'block')); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="item-tabs">
    <ul id="<?php echo $tabsId; ?>" class="nav nav-tabs">
        <?php if ($this->checkPosition('text')) : ?>
            <li class="active">
                <a href="#item-desc" id="desc-tab" data-toggle="tab">
                    <?php echo JText::_('JBZOO_ITEM_TAB_DESCRIPTION'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <li>
                <a href="#item-prop" id="prop-tab" data-toggle="tab">
                    <?php echo JText::_('JBZOO_ITEM_TAB_PROPS'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li>
                <a href="#item-gallery" id="gallery-tab" data-toggle="tab">
                    <?php echo JText::_('JBZOO_ITEM_TAB_GALLERY'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <li>
                <a href="#item-comments" id="comments-tab" data-toggle="tab">
                    <?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                    <span class="badge"><?php echo $item->getCommentsCount(); ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div id="<?php echo $tabsId; ?>Content" class="tab-content">
        <?php if ($this->checkPosition('text')) : ?>
            <div class="tab-pane fade active in" id="item-desc">
                <div class="item-text">
                    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <div class="tab-pane fade" id="item-prop">
                <table class="table table-hover">
                    <?php echo $this->renderPosition('properties', array(
                        'tooltip' => true,
                        'style'   => 'jbtable',
                    )); ?>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <div class="tab-pane fade" id="item-gallery">
                <?php echo $this->renderPosition('gallery', array(
                    'labelTag' => 'h4',
                    'style'    => 'jbblock',
                )); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <div class="tab-pane fade" id="item-comments">
                <?php echo $this->renderPosition('comments'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($this->checkPosition('related')) : ?>
    <div class="<?php echo $rowClass; ?> item-related">
        <div class="<?php echo $bootstrap->gridClass(12); ?>">
            <?php echo $this->renderPosition('related', array(
                'labelTag' => 'h4',
                'style'    => 'jbblock',
            )); ?>
        </div>
    </div>
<?php endif; ?>