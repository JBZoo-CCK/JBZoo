<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_EXPORT_ZOOBACKUP'); ?></h2>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_EXPORT_ZOOBACKUP_1'); ?></p>

        <p><em><?php echo JText::_('JBZOO_ADMIN_PAGE_EXPORT_ZOOBACKUP_2'); ?></em></p>

        <?php echo $this->app->jbform->render('export_zoobackup', array(
            'action'     => $this->app->jbrouter->admin(array(
                'controller' => 'manager',
                'task'       => 'dobackup'
            )),
            'showSubmit' => true,
            'submit'     => JText::_('JBZOO_FORM_DOWNLOAD')
        ));
        ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
