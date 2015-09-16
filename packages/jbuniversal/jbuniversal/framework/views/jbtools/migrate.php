<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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
