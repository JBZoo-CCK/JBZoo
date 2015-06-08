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

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="col-md-6">
            <div class="item-properties">
                <ul class="list-unstyled">
                    <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($this->checkPosition('text')) : ?>
    <div class="item-text row">
        <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('buttons')) : ?>
    <div class="item-buttons clearfix">
        <?php echo $this->renderPosition('buttons'); ?>
    </div>
<?php endif; ?>
