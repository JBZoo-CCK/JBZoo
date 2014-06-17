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

<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('date')) : ?>
    <p class="jbzoo-date"><?php echo $this->renderPosition('date'); ?></p>
<?php endif; ?>


<?php if ($this->checkPosition('title')) : ?>
    <span class="item-title"><?php echo $this->renderPosition('title'); ?></span>
<?php endif; ?>


<div class="clear clr"></div>
