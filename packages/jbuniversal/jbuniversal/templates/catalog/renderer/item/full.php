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


$align     = $this->app->jbitem->getMediaAlign($item, $layout);
$tabsId    = $this->app->jbstring->getId('tabs');
?>

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

    <div class="rborder jb-box jb-divider-bottom">
        <div class="jb-row clearfix">
            <div class="width50">
                <?php if ($this->checkPosition('image')) : ?>
                    <div class="item-image jb-divider-bottom">
                        <?php echo $this->renderPosition('image'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->checkPosition('meta')) : ?>
                    <div class="item-metadata jb-divider-bottom">
                        <ul class="unstyled">
                            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($this->checkPosition('buttons')) : ?>
                    <div class="item-buttons jb-divider-bottom clearfix">
                        <div class="width100">
                            <?php echo $this->renderPosition('buttons', array('style' => 'block')); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($this->checkPosition('price')) : ?>
                <div class="width50">
                    <div class="item-price">
                        <?php echo $this->renderPosition('price'); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($this->checkPosition('social')) : ?>
            <div class="jb-row item-social last clearfix">
                <div class="width100">
                    <?php echo $this->renderPosition('social', array('style' => 'block')); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="<?php echo $tabsId; ?>" class="item-tabs jb-row">
        <ul class="jb-nav">
            <?php if ($this->checkPosition('text')) : ?>
                <li class="active">
                    <a href="#item-desc" id="desc-tab">
                        <?php echo JText::_('JBZOO_ITEM_TAB_DESCRIPTION'); ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('properties')) : ?>
                <li>
                    <a href="#item-prop" id="prop-tab">
                        <?php echo JText::_('JBZOO_ITEM_TAB_PROPS'); ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('gallery')) : ?>
                <li>
                    <a href="#item-gallery" id="gallery-tab">
                        <?php echo JText::_('JBZOO_ITEM_TAB_GALLERY'); ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('comments')) : ?>
                <li>
                    <a href="#item-comments" id="comments-tab">
                        <?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                        <span class="badge"><?php echo $item->getCommentsCount(); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <?php if ($this->checkPosition('text')) : ?>
            <div class="tab-pane fade active in" id="item-desc">
                <div class="item-text">
                    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <div class="jb-tab-pane" id="item-prop">
                <table class="table table-hover">
                    <?php echo $this->renderPosition('properties', array(
                        'tooltip' => true,
                        'style'   => 'jbtable',
                    )); ?>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <div class="jb-tab-pane" id="item-gallery">
                <?php echo $this->renderPosition('gallery', array(
                    'labelTag' => 'h4',
                    'style'    => 'jbblock',
                )); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <div class="jb-tab-pane" id="item-comments">
                <?php echo $this->renderPosition('comments'); ?>
            </div>
        <?php endif; ?>
    </div>

<?php if ($this->checkPosition('related')) : ?>
    <div class="jb-row item-related">
        <div class="width100">
            <?php echo $this->renderPosition('related', array(
                'labelTag' => 'h4',
                'style'    => 'jbblock',
            )); ?>
        </div>
    </div>
<?php endif;
$this->app->jbassets->tabs();
echo $this->app->jbassets->widget('#' . $tabsId, 'JBZooTabs');
