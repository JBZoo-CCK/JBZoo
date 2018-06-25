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

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_IMPORT_CATEGORIES'); ?></h2>

        <h4><?php echo JText::_('JBZOO_IMPORT_NOTICE_TITLE'); ?></h4>
        <ul>
            <li><?php echo JText::_('JBZOO_IMPORT_NOTICE_1'); ?></li>
            <li><?php echo JText::_('JBZOO_IMPORT_NOTICE_2'); ?></li>
            <li><?php echo JText::_('JBZOO_IMPORT_NOTICE_3'); ?></li>
            <li><?php echo JText::_('JBZOO_IMPORT_NOTICE_4'); ?></li>
        </ul>

        <?php
        echo $this->app->jbform->render('import_categories', array(
            'action'     => $this->app->jbrouter->admin(array('task' => 'assign')),
            'showSubmit' => true,
            'submit'     => JText::_('JBZOO_FORM_NEXT')
        ), $this->importParams);
        ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
