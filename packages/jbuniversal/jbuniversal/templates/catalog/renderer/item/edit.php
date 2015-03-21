<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
    <fieldset class="pos-content creation-form">
        <legend><?php echo $item->getType()->name; ?></legend>
        <?php if ($this->checkPosition('content')) {
            echo $this->renderPosition('content', array('style' => 'submission.block'));
        } ?>
    </fieldset>

<?php if ($this->checkPosition('media')) : ?>
    <fieldset class="pos-media creation-form">
        <legend><?php echo JText::_('Media'); ?></legend>
        <?php echo $this->renderPosition('media', array('style' => 'submission.block')); ?>
    </fieldset>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
    <fieldset class="pos-meta creation-form">
        <legend><?php echo JText::_('Meta'); ?></legend>
        <?php echo $this->renderPosition('meta', array('style' => 'submission.block')); ?>
    </fieldset>
<?php endif; ?>

<?php if ($this->checkPosition('administration')) : ?>
    <fieldset class="pos-administration creation-form">
        <legend><?php echo JText::_('Administration'); ?></legend>
        <?php echo $this->renderPosition('administration', array('style' => 'submission.block')); ?>
    </fieldset>
<?php endif;
