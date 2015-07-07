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
$action  = $this->app->jbrouter->admin(array('task' => 'doStep'));
$form    = $this->app->jbform;
$html    = $this->app->jbhtml;
$options = array_merge($form->getDefaultFormOptions(), array(
    'action' => $action
)); ?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_JBMIGRATE_CONFIG_TITLE'); ?></h2>

        <p><strong><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION'); ?></strong></p>

        <ul>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_1'); ?></li>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_2'); ?></li>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_3'); ?></li>
        </ul>
        <div class="jsProgressBar progress jbadminform"></div>
        <p>&nbsp;</p>

        <form <?php echo $html->buildAttrs($options); ?>>
            <?php
            $id      = $this->app->jbstring->getId('currency-');
            $list    = (array)$this->data->get('currency_list', array());
            $curList = array_combine($list, $list);
            if ($curList) : ?>
                <div class="uk-form-row">
                    <div class="uk-form-label">
                        <label id="<?php echo $id; ?>-lbl" for="<?php echo $id; ?>" class="hasToolTip">
                            <?php echo JText::_('JBZOO_JBMIGRATE_SELECT_CURRENCY'); ?>
                        </label>

                        <div class="description-label">
                            <?php echo JText::_('JBZOO_JBMIGRATE_SELECT_CURRENCY_DESCRIPTION'); ?>
                        </div>
                    </div>
                    <div class="uk-form-controls">
                        <?php echo $html->select($curList, 'jbzooform[currency_list]', array(
                            'id'       => $id,
                            'multiple' => true
                        )); ?>
                    </div>
                </div>
            <?php endif; ?>
            <input type="submit" class="uk-button uk-button-primary"
                   style="display: inline-block;" value="<?php echo JText::_('JBZOO_JBMIGRATE_START_MIGRATE'); ?>"/>
        </form>


        <?php echo $this->partial('footer'); ?>
    </div>
</div>


