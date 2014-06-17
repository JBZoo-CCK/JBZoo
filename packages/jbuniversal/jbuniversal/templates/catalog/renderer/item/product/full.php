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


$align = $this->app->jbitem->getMediaAlign($item, $layout);
$tabsId = uniqid('jbzoo-tabs-');

echo $this->renderPosition('title', array('style' => 'jbtitle'));
echo $this->renderPosition('subtitle', array('style' => 'jbsubtitle'));
echo $this->renderPosition('likes', array('style' => 'jbblock', 'class' => 'align-left'));
echo $this->renderPosition('rating', array('style' => 'jbblock', 'class' => 'align-right'));

?>
<div class="clear clr"></div>

<div class="rborder item-body">

    <?php if ($this->checkPosition('image')) : ?>
        <div class="item-image align-<?php echo $align; ?>"><?php echo $this->renderPosition('image'); ?></div>
    <?php endif; ?>

    <?php if ($this->checkPosition('price')) : ?>
        <div class="item-price-position">
            <?php echo $this->renderPosition('price'); ?>
        </div>
    <?php endif; ?>

    <div class="clear clr"></div>

    <?php if ($this->checkPosition('anons')) : ?>
        <div class="item-anons">
            <?php echo $this->renderPosition('anons'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('meta')) : ?>
        <hr/>
        <div class="item-meta">
            <?php echo $this->renderPosition('meta', array('style' => 'jbblock', 'labelTag' => 'strong', 'tag' => 'p')); ?>
        </div>
    <?php endif; ?>
    <div class="clear clr"></div>
</div>

<?php echo $this->renderPosition('social', array('style' => 'jbblock')); ?>

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
    <div id="<?php echo $tabsId; ?>" class="rborder">
        <ul>
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
                <li><a href="#tab-comments"><?php echo JText::_('JBZOO_ITEM_TAB_COMMENTS'); ?>
                        (<?php echo $item->getCommentsCount(); ?>)</a>
                </li>
            <?php endif; ?>
        </ul>

        <?php if ($this->checkPosition('tab-text')) : ?>
            <div id="tab-text">
                <?php echo $this->renderPosition('tab-text', $positionParams); ?>
                <div class="clear clr"></div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('tab-gallery')) : ?>
            <div id="tab-gallery">
                <?php echo $this->renderPosition('tab-gallery', $positionParams); ?>
                <div class="clear clr"></div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('tab-properties')) : ?>
            <div id="tab-properties">
                <table class="jbtable">
                    <?php echo $this->renderPosition('tab-properties', array('style' => 'jbtable', 'tooltip' => 1)); ?>
                </table>
                <div class="clear clr"></div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('tab-reviews')) : ?>
            <div id="tab-reviews">
                <?php echo $this->renderPosition('tab-reviews', $positionParams); ?>
                <div class="clear clr"></div>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('tab-comments')) : ?>
            <div id="tab-comments"><?php echo $this->renderPosition('tab-comments', $positionParams); ?></div>
        <?php endif; ?>
    </div>

    <?php $this->app->jbassets->tabs(); ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $('#<?php echo $tabsId;?>').JBZooTabs({
                onTabShow: function (index) {
                    var map = $('.googlemaps > div:first');
                    if (map.length) {
                        map.data('Googlemaps').refresh();
                    }
                }
            });
        });
    </script>
<?php endif; ?>

<?php echo $this->renderPosition('related', array('style' => 'jbblock', 'labelTag' => 'h2', 'clear' => true)); ?>
