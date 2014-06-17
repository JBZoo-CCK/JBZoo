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


$imageAlign = $this->app->jbitem->getMediaAlign($item, $layout);

?>

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>


<?php if ($this->checkPosition('props')) : ?>
    <div class="date"><?php echo $this->renderPosition('props'); ?></div>
<?php endif; ?>


<?php if ($this->checkPosition('image')) : ?>
    <div class="image align-<?php echo $imageAlign; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('text')) : ?>
    <div class="text"><?php echo $this->renderPosition('text'); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
    <ul class="links">
        <?php echo $this->renderPosition('links', array('style' => 'block')); ?>
    </ul>
<?php endif; ?>


<div class="clr"></div>
