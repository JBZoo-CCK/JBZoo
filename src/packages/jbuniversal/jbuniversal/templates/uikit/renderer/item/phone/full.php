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

$tabsId = $this->app->jbstring->getId('tabs');
$align = $this->app->jbitem->getMediaAlign($item, $layout);

if ($this->checkPosition('title')) : ?>
    <h1 class="item-title uk-h1"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php $positionParams = ['style' => 'jbblock', 'tag' => 'div', 'labelTag' => 'h3', 'clear' => true]; ?>
<div class="tab-container uk-panel uk-panel-box uk-article-divider">
    <ul class="uk-tab" data-uk-tab="{connect:'#<?php echo $tabsId; ?>'}">
        <li><a href="#tab-text">
                <i class="uk-icon-info"></i>
                <?php echo JText::_('JBZOO_ITEM_TAB_DESCRIPTION'); ?></a>
        </li>

        <?php if ($this->checkPosition('properties')) : ?>
            <li><a href="#tab-properties">
                    <i class="uk-icon-list"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_PROPS'); ?></a>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li><a href="#tab-gallery">
                    <i class="uk-icon-image"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_GALLERY'); ?></a></li>
        <?php endif; ?>

        <?php if ($this->checkPosition('reviews')) : ?>
            <li><a href="#tab-reviews">
                    <i class="uk-icon-video-camera"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_REVIEWS'); ?></a></li>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <li>
                <a href="#tab-comments">
                    <i class="uk-icon-comment"></i>
                    <?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                    <span class="uk-badge uk-badge-notification uk-badge-primary">
                    <?php echo $item->getCommentsCount(); ?>
                </span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <ul class="uk-switcher uk-margin" id="<?php echo $tabsId; ?>">
        <li id="tab-text">

            <?php if ($this->checkPosition('image')) : ?>
                <div class="item-middle-container uk-width-medium-1-2 item-image uk-align-<?php echo $align; ?>">
                    <?php echo $this->renderPosition('image'); ?>

                    <?php if ($this->checkPosition('tools')) { ?>
                        <?php echo $this->renderPosition('tools'); ?>
                    <?php } ?>

                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('price')) : ?>
                <div class="item-middle-container uk-width-medium-1-2 item-price uk-align-left">
                    <?php echo $this->renderPosition('price'); ?>
                </div>
            <?php endif; ?>

            <?php echo JBZOO_CLR; ?>
            <h3>Подробное описание</h3>
            <?php echo $this->renderPosition('text', $positionParams); ?>
            <?php echo JBZOO_CLR; ?>
        </li>

        <?php if ($this->checkPosition('properties')) : ?>
            <li id="tab-properties">
                <table class="uk-table uk-table-hover uk-table-striped">
                    <?php echo $this->renderPosition('properties', ['style' => 'jbtable', 'tooltip' => 1]); ?>
                </table>
                <?php echo JBZOO_CLR; ?>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('gallery')) : ?>
            <li id="tab-gallery">
                <?php echo $this->renderPosition('gallery', $positionParams); ?>
                <?php echo JBZOO_CLR; ?>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('reviews')) : ?>
            <li id="tab-reviews">
                <?php echo $this->renderPosition('reviews', $positionParams); ?>
                <?php echo JBZOO_CLR; ?>
            </li>
        <?php endif; ?>

        <?php if ($this->checkPosition('comments')) : ?>
            <li id="tab-comments">
                <?php echo $this->renderPosition('comments', $positionParams); ?>
            </li>
        <?php endif; ?>
    </ul>

    <?php echo $this->renderPosition('social'); ?>
</div>

<hr />
<?php echo $this->renderPosition('related', ['style' => 'jbblock', 'labelTag' => 'h2', 'clear' => true]); ?>
