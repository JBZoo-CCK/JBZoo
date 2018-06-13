<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
