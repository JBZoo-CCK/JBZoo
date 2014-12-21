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


$tabsId = uniqid('jbzoo-tabs-');

echo $this->renderPosition('title', array('style' => 'jbtitle'));
echo $this->renderPosition('subtitle', array('style' => 'jbsubtitle'));
echo $this->renderPosition('likes', array('style' => 'jbblock', 'class' => 'align-left'));
echo $this->renderPosition('rating', array('style' => 'jbblock', 'class' => 'uk-align-right'));

?>
<div class="uk-clearfix"></div>

<div class="item-body uk-panel uk-panel-box uk-article-divider">

    <div class="uk-grid block-divider" data-uk-grid-margin>
        <?php if ($this->checkPosition('image')) : ?>
            <div class="uk-width-medium-1-2 item-image">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('price')) : ?>
            <div class="uk-width-medium-1-2 item-price-position">
                <?php echo $this->renderPosition('price'); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('anons')) : ?>
        <div class="item-anons">
            <?php echo $this->renderPosition('anons'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('meta')) : ?>
        <hr/>
        <ul class="item-meta uk-list">
            <?php echo $this->renderPosition('meta', array('style' => 'jbblock', 'labelTag' => 'strong', 'tag' => 'li')); ?>
        </ul>
    <?php endif; ?>
</div>

<?php echo $this->renderPosition('social', array('style' => 'jbblock')); ?>

<div class="uk-clearfix"></div>

<?php if ($this->checkPosition('tab-text')
    || $this->checkPosition('tab-gallery')
    || $this->checkPosition('tab-properties')
    || $this->checkPosition('tab-reviews')
    || $this->checkPosition('tab-comments')
) :

    $positionParams = array(
        'style'    => 'jbblock',
        'tag'      => 'div',
        'labelTag' => 'h3',
        'clear'    => true
    );

    ?>
    <div class="tab-container uk-panel uk-panel-box uk-article-divider">
        <ul class="uk-tab" data-uk-tab="{connect:'#<?php echo $tabsId; ?>'}">
            <?php if ($this->checkPosition('tab-text')) : ?>
                <li><a href="#tab-text"><?php echo JText::_('JBZOO_ITEM_TAB_DESCRIPTION'); ?></a></li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-properties')) : ?>
                <li><a href="#tab-properties"><?php echo JText::_('JBZOO_ITEM_TAB_PROPS'); ?></a></li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-gallery')) : ?>
                <li><a href="#tab-gallery"><?php echo JText::_('JBZOO_ITEM_TAB_GALLERY'); ?></a></li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-reviews')) : ?>
                <li><a href="#tab-reviews"><?php echo JText::_('JBZOO_ITEM_TAB_REVIEWS'); ?></a></li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-comments')) : ?>
                <li>
                    <a href="#tab-comments"><?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                        <span class="uk-badge uk-badge-notification uk-badge-primary"><?php echo $item->getCommentsCount(); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <ul id="<?php echo $tabsId; ?>" class="uk-switcher uk-margin">
            <?php if ($this->checkPosition('tab-text')) : ?>
                <li id="tab-text">
                    <?php echo $this->renderPosition('tab-text', $positionParams); ?>
                    <div class="clear clr"></div>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-properties')) : ?>
                <li id="tab-properties">
                    <table class="uk-table uk-table-hover uk-table-striped">
                        <?php echo $this->renderPosition('tab-properties', array('style' => 'jbtable', 'tooltip' => 1)); ?>
                    </table>
                    <div class="clear clr"></div>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-gallery')) : ?>
                <li id="tab-gallery">
                    <?php echo $this->renderPosition('tab-gallery', $positionParams); ?>
                    <div class="clear clr"></div>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-reviews')) : ?>
                <li id="tab-reviews">
                    <?php echo $this->renderPosition('tab-reviews', $positionParams); ?>
                    <div class="clear clr"></div>
                </li>
            <?php endif; ?>

            <?php if ($this->checkPosition('tab-comments')) : ?>
                <li id="tab-comments"><?php echo $this->renderPosition('tab-comments', $positionParams); ?></li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<?php echo $this->renderPosition('related', array('style' => 'jbblock', 'labelTag' => 'h2', 'clear' => true)); ?>
