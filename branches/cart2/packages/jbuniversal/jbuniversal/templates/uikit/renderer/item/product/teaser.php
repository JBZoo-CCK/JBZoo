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

?>

<?php if ($this->checkPosition('title')) : ?>
    <h3 class="item-title"><?php echo $this->renderPosition('title'); ?></h3>
<?php endif; ?>

<?php if ($this->checkPosition('rating')) : ?>
    <div class="item-rating block-divider">
        <?php echo $this->renderPosition('rating', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image uk-align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('price')) : ?>
    <div class="product-prices uk-clearfix block-divider">
        <?php echo $this->renderPosition('price'); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('properties')) : ?>
    <div class="product-props block-divider">
        <ul class="uk-list uk-list-line">
            <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('favourite')) : ?>
    <div class="product-favorite block-divider">
        <?php echo $this->renderPosition('favourite'); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
    <div class="product-links">
        <?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
    </div>
<?php endif; ?>
