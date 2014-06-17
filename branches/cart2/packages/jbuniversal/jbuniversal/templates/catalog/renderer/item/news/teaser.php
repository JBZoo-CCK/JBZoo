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
    <h3 class="title"><?php echo $this->renderPosition('title'); ?></h3>
<?php endif; ?>


<?php if ($this->checkPosition('image')) : ?>
    <div class="image align-<?php echo $imageAlign; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('date')) : ?>
    <div class="date"><?php echo $this->renderPosition('date'); ?></div>
<?php endif; ?>


<?php if ($this->checkPosition('anons')) : ?>
    <div class="anons"><?php echo $this->renderPosition('anons'); ?></div>
<?php endif; ?>


<?php if ($this->checkPosition('category')) : ?>
    <div class="categories-list"><?php echo $this->renderPosition('category'); ?></div>
<?php endif; ?>

<div class="clr"></div>
