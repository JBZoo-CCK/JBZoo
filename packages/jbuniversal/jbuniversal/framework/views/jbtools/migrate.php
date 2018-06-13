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

$this->app->jbassets->progressBar();

$urlAjax     = $this->app->jbrouter->admin(array('task' => 'doStep'));
$urlPostAjax = $this->app->jbrouter->admin(array('task' => 'lastStep')); ?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_MIGRATE_CONFIG_TITLE'); ?></h2>

        <h4 style="color:#a00;"><?php echo JText::_('JBZOO_MIGRATE_CONFIG_ATTENTION'); ?></h4>
        <ul>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_1'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_2'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_3'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_4'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_5'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_6'); ?></li>
        </ul>

        <h4 style="color:#a00;"><?php echo JText::_('JBZOO_MIGRATE_CONFIG_ATTENTION_2'); ?></h4>
        <ul>

            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_7'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_8'); ?></li>
            <li><?php echo JText::_('JBZOO_MIGRATE_CONFIG_DESC_9'); ?></li>
        </ul>

        <?php echo $this->app->jbform->render('migrate', array(
            'action'     => $this->app->jbrouter->admin(array('task' => 'migrateSteps')),
            'showSubmit' => true,
            'submit'     => JText::_('JBZOO_FORM_NEXT'),
            'method'     => 'post',
        )); ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
