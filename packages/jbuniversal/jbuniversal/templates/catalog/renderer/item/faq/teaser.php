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
    <h4 class="title"><?php echo $this->renderPosition('title'); ?></h4>
<?php endif; ?>


<?php if ($this->checkPosition('properties')) : ?>
    <ul class="properties">
        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>


<?php if ($this->checkPosition('image')) : ?>
    <div class="image">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('text')) : ?>
    <p><?php echo $this->renderPosition('text'); ?></p>
<?php endif; ?>


<?php if ($this->checkPosition('meta')) : ?>
    <ul class="meta">
        <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>

<div class="clear clr"></div>
