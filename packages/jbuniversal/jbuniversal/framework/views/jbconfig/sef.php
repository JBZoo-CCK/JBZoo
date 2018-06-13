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

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_CONFIG_SEF'); ?></h2>

        <p><?php echo JText::_('JBZOO_SEF_DESCRIPTION'); ?></p>

        <p><strong><?php echo JText::_('JBZOO_SEF_ATTENTION'); ?></strong></p>
        <ul>
            <li><?php echo JText::_('JBZOO_SEF_ATTENTION_1'); ?></li>
            <li><?php echo JText::_('JBZOO_SEF_ATTENTION_2'); ?></li>
        </ul>
        <p>&nbsp;</p>

        <?php echo $this->app->jbform->render('config_sef', array(), $this->configData); ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
