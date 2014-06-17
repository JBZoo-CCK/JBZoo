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
<?php echo $this->renderPosition('rating', array('style' => 'block')); ?>

<?php if ($this->checkPosition('image')) : ?>
    <div class="span3 item-image align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>

<div class="product-props">

    <p><i><?php echo $this->renderPosition('subtitle', array('style' => 'block')); ?></i></p>

    <?php if ($this->checkPosition('properties')) : ?>
        <ul>
            <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
        </ul>
    <?php endif; ?>
    <?php if ($this->checkPosition('favourite')) : ?>
        <?php echo $this->renderPosition('favourite'); ?>
    <?php endif; ?>
</div>
<div class="clear clr"></div>

<div class="product-buttons span12 price-value-position">
    <?php echo $this->renderPosition('price-value'); ?>
</div>

<?php if ($this->checkPosition('price')) : ?>
    <div class="product-buttons span12">
        <?php echo $this->renderPosition('price'); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
    <div class="clear clr"></div>
    <?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
<?php endif; ?>

<div class="clear clr"></div>
