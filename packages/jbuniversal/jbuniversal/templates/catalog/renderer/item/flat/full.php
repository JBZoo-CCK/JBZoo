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

$tabsId = uniqid('jbzoo-tabs-');

?>
<?php $map = JString::trim($this->renderPosition('map')); ?>
<?php if ($this->checkPosition('title')) : ?>
    <h1 class="title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>
<div class="clearfix top-info">
    <?php if ($this->checkPosition('gallery')) : ?>
        <div class="full-gallery">
            <?php echo $this->renderPosition('gallery'); ?>
        </div>
    <?php endif; ?>
    <div class="features">
        <?php if ($this->checkPosition('properties')) : ?>
            <div class="price-flat clearfix">

                <?php echo $this->renderPosition('price'); ?>
            </div>
            <ul class="properties">
                <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
            </ul>
            <div class="favourite">
                <?php echo $this->renderPosition('favourite'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="clear clr"></div>

<div id="<?php echo $tabsId; ?>">
    <ul>
        <li><a href="#tab-overview">Overview</a></li>
        <li><a href="#tab-agent">Listing Agent</a></li>
        <?php if ($map) : ?>
            <li><a href="<?php echo JUri::getInstance()->toString(); ?>#tab-location">Location</a></li><?php endif; ?>
        <li><a href="#tab-comments">Comments</a></li>
    </ul>

    <div id="tab-overview">
        <div class="full-info-realty">
            <?php if ($this->checkPosition('text')) : ?>
                <div class="text-property description-block">
                    <h3>Description</h3>
                    <?php echo $this->renderPosition('text'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('related')) : ?>
                <h3>More Similar Properties</h3>
                <div class="related-property">
                    <?php echo $this->renderPosition('related'); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="clear clr"></div>
    </div>

    <div id="tab-agent">
        <div class="agent-description description-block">
            <?php echo $this->renderPosition('agent'); ?>
        </div>
        <div class="clear clr"></div>
    </div>

    <?php if ($map) : ?>
        <div id="tab-location">
            <?php echo $this->renderPosition('map'); ?>
            <div class="clear clr"></div>
        </div>
    <?php endif; ?>

    <div id="tab-comments">
        <div class="comments-property">
            <?php echo $this->renderPosition('comments'); ?>
        </div>
        <div class="clear clr"></div>
    </div>
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