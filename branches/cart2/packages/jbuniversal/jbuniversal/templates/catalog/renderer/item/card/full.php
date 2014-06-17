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

echo $this->renderPosition('title', array('style' => 'jbtitle'));

?>

<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>

<?php echo $this->renderPosition('rating'); ?>

<?php if ($this->checkPosition('anons')) : ?>
    <?php echo $this->renderPosition('anons', array('style' => 'block')); ?>
<?php endif; ?>
<div class="clear clr"></div>


<?php if ($this->checkPosition('properties')) : ?>
    <h3><?php echo JText::_('JBZOO_TMPL_COMPANY_INFO'); ?></h3>
    <ul class="item-properties">
        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>


<?php if ($this->checkPosition('text')) : ?>
    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
<?php endif; ?>


<?php if ($this->checkPosition('meta')) : ?>
    <h3><?php echo JText::_('JBZOO_TMPL_COMPANY_META'); ?></h3>
    <ul class="item-metadata">
        <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
    </ul>
<?php endif; ?>

<div class="clear clr"></div>
