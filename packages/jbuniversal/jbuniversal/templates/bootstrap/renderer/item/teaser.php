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

$align     = $this->app->jbitem->getMediaAlign($item, $layout);
$bootstrap = $this->app->jbbootstrap;
$rowClass  = $bootstrap->getRowClass();
?>

<?php if ($this->checkPosition('title')) : ?>
    <h4 class="item-title"><?php echo $this->renderPosition('title'); ?></h4>
<?php endif; ?>

<div class="<?php echo $rowClass; ?>">
    <?php if ($this->checkPosition('image')) : ?>
        <div class="<?php echo $bootstrap->gridClass(12); ?>">
            <div class="item-image pull-<?php echo $align; ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>

            <?php if ($this->checkPosition('properties')) : ?>
                <div class="item-properties">
                    <ul class="unstyled">
                        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($this->checkPosition('price')) : ?>
    <div class="<?php echo $rowClass; ?>">
        <div class="<?php echo $bootstrap->gridClass(6); ?>">
            <div class="item-price">
                <?php echo $this->renderPosition('price', array('style' => 'block')); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('text')) : ?>
    <div class="item-text <?php echo $rowClass; ?>">
        <div class="<?php echo $bootstrap->gridClass(12); ?>">
            <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('quick-view')) : ?>
    <div class="item-quick-view <?php echo $rowClass; ?>">
        <div class="<?php echo $bootstrap->gridClass(12); ?>">
            <?php echo $this->renderPosition('quick-view', array('style' => 'block')); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('buttons')) : ?>
    <div class="<?php echo $rowClass; ?> item-buttons clearfix">
        <div class="<?php echo $bootstrap->gridClass(12); ?>">
            <?php echo $this->renderPosition('buttons'); ?>
        </div>
    </div>
<?php endif; ?>
