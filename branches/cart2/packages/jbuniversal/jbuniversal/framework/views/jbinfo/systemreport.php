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

$urlAction = $this->app->jbrouter->admin(array('controller' => 'jbinfo', 'task' => 'systemReport'));

?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">
        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_INFO_SYSTEMREPORT') ?></h2>

        <p><?php echo JText::_('JBZOO_SYSTEM_REPORT_DESC') ?></p>

        <strong><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED'); ?></strong>:

        <ul>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_PHPINFO'); ?></li>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_VERSION'); ?></li>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_FILESYSTEM'); ?></li>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_CHECKMODIFIED'); ?></li>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_PHPLIBS'); ?></li>
            <li><?php echo JText::_('JBZOO_SYSTEM_REPORT_INCLUDED_SERVER'); ?></li>
        </ul>

        <form action="<?php echo $urlAction; ?>" method="post">
            <input type="submit" class="uk-button uk-button-primary jbbutton-create-report"
                   style="display: inline-block;" value="<?php echo JText::_('JBZOO_SYSTEM_REPORT_CREATE'); ?>" />
        </form>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

