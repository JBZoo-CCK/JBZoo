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

$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>

<?php if ($this->checkPosition('title')) : ?>
    <h4 class="item-title"><?php echo $this->renderPosition('title'); ?></h4>
<?php endif; ?>

<div class="uk-clearfix">
    <?php if ($this->checkPosition('image')) : ?>
        <div class="item-image uk-align-<?php echo $align; ?>">
            <?php echo $this->renderPosition('image'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="item-properties">
            <ul class="uk-list">
                <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php if ($this->checkPosition('price')) : ?>
    <div class="item-price uk-divider">
        <?php echo $this->renderPosition('price', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('text')) : ?>
    <div class="item-text uk-divider">
        <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('quick-view')) : ?>
    <div class="item-quick-view uk-divider">
        <?php echo $this->renderPosition('quick-view', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('buttons')) : ?>
    <div class="item-buttons uk-divider">
        <?php echo $this->renderPosition('buttons'); ?>
    </div>
<?php endif; ?>