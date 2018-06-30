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

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_CLEANDB'); ?></h2>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_DESC_1'); ?></p>

        <p><em><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_DESC_2'); ?></em></p>

        <h4><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_TITLE'); ?></h4>

        <ul>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_1'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_2'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_3'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_4'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CLEANDB_5'); ?></li>
        </ul>

        <?php echo $this->app->jbform->render('cleandb', array(
            'action'     => $this->app->jbrouter->admin(),
            'submit'     => JText::_('JBZOO_FORM_CHECK'),
            'showSubmit' => true,
        ));
        ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
