<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_CONFIG_YML'); ?></h2>

        <p><strong><?php echo JText::_('JBZOO_YML_ATTENTION'); ?></strong></p>

        <ul>
            <li><?php echo JText::_('JBZOO_YML_ATTENTION_1'); ?></li>
            <li><?php echo JText::_('JBZOO_YML_ATTENTION_2'); ?></li>
            <li><?php echo JText::_('JBZOO_YML_ATTENTION_3'); ?></li>
        </ul>
        <p>&nbsp;</p>

        <?php
        echo $this->app->jbform->render('config_yandexyml', array(), $this->configData);
        ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
