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
    <h5 class="item-title"><?php echo $this->renderPosition('title'); ?></h5>
<?php endif; ?>


<?php if ($this->checkPosition('properties')) : ?>
    <ul class="item-properties">
        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>


<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image pull-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('text')) : ?>
    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
<?php endif; ?>


<?php if ($this->checkPosition('meta')) : ?>
    <ul class="item-metadata">
        <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>

<div class="clearfix"></div>
