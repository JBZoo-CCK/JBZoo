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
<div class="clear" style="margin-top:36px;"></div>
<div class="jbfooter">
    <hr>

    <span class="footer-copyrights">
        &copy; 2011 - <?php echo date('Y'); ?>
        <?php echo JText::_('JBZOO_ADMIN_WEBSITE'); ?>
    </span>

    <span class="footer-version-row">Joomla: <strong><?php echo $this->app->jbversion->joomla(); ?></strong></span>
    <span class="footer-version-row">JBZoo: <strong><?php echo $this->app->jbversion->jbzoo(); ?></strong></span>
    <span class="footer-version-row">Zoo: <strong><?php echo $this->app->jbversion->zoo(); ?></strong></span>

    <?php if ($widgetKit = $this->app->jbversion->widgetkit()) : ?>
        <span class="footer-version-row">WidgetKit: <strong><?php echo $widgetKit; ?></strong></span>
    <?php endif; ?>
</div>