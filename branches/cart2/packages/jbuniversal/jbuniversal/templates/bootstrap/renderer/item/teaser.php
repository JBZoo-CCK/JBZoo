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

?>

<?php if ($this->checkPosition('title')) : ?>
    <h4 class="item-title"><?php echo $this->renderPosition('title'); ?></h4>
<?php endif; ?>

<div class="row">
    <?php if ($this->checkPosition('image')) : ?>
        <div class="col-md-6">
            <div class="item-image">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        </div>
    <?php endif; ?>

        <div class="col-md-6">
            <?php if ($this->checkPosition('properties')) : ?>
                <div class="item-properties">
                    <ul class="list-unstyled">
                        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('price')) : ?>
                <div class="item-price">
                    <?php echo $this->renderPosition('price', array('style' => 'block')); ?>
                </div>
            <?php endif; ?>
        </div>
</div>

<?php if ($this->checkPosition('text')) : ?>
    <div class="item-text row">
        <div class="col-md-12">
            <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('buttons')) : ?>
    <div class="row item-buttons clearfix">
        <div class="col-md-12">
            <?php echo $this->renderPosition('buttons'); ?>
        </div>
    </div>
<?php endif; ?>
