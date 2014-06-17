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
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>


<?php if ($this->checkPosition('subtitle')) : ?>
    <h2 class="item-subtitle"><?php echo $this->renderPosition('subtitle'); ?></h2>
<?php endif; ?>


<?php if ($this->checkPosition('date')) : ?>
    <strong><?php echo $this->renderPosition('date'); ?></strong>
<?php endif; ?>


<?php if ($this->checkPosition('social')) : ?>
    <div class="align-right"><?php echo $this->renderPosition('social'); ?></div>
<?php endif; ?>
<div class="clear clr"></div>


<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php echo $this->renderPosition('text', array('style' => 'block')); ?>


<?php if ($this->checkPosition('media')) : ?>
    <div class="clear clr"></div>
    <div class="align-center">
        <?php echo $this->renderPosition('media', array('style' => 'block')); ?>
    </div>
<?php endif; ?>


<?php echo $this->renderPosition('links', array('style' => 'block')); ?>


<?php echo $this->renderPosition('author', array('style' => 'block')); ?>


<?php if ($this->checkPosition('meta')) : ?>
    <p><?php echo $this->renderPosition('meta', array('style' => 'pipe')); ?></p>
<?php endif; ?>

<div class="clear clr"></div>
