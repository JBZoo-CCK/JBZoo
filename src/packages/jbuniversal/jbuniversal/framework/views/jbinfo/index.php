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
        <div class="manager-info" id="adminForm">
            <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_INFO_INDEX'); ?></h2>

            <img class="application-image" src="<?php echo $this->image; ?>" alt="application-image" />

            <div class="application-details">

                <h3><?php echo $this->metadata->get('name'); ?></h3>

                <div><?php echo $this->metadata->get('description'); ?></div>
                <p>&nbsp;</p>

                <p><strong>Version:</strong>
                    <?php echo $this->metadata->get('version'); ?>
                    - <?php echo $this->metadata->get('creationdate'); ?></p>

                <ul>
                    <li><strong>Author:</strong> <?php echo $this->metadata->get('author'); ?></li>
                    <li><strong>E-Mail:</strong> <a href="mailto:<?php echo $this->metadata->get('authorEmail'); ?>"
                                                    target="_blank"><?php echo $this->metadata->get('authorEmail'); ?></a>
                    </li>
                    <li><strong>Website:</strong> <a href="<?php echo $this->metadata->get('authorUrl'); ?>"
                                                     target="_blank"><?php echo $this->metadata->get('authorUrl'); ?></a>
                    </li>
                    <li><strong>Copyright:</strong> <?php echo $this->metadata->get('copyright'); ?></li>
                    <li><strong>License:</strong> <?php echo $this->metadata->get('license'); ?></li>
                </ul>
            </div>
        </div>

        <?php echo JBZOO_CLR; ?>
        <hr>

        <?php echo $this->partial('icons', [
            'items' => [
                [
                    'name' => 'JBZOO_ICON_INDEX_PERFORMANCE',
                    'icon' => 'performance.png',
                    'link' => ['controller' => 'jbinfo', 'task' => 'performance'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_SUPPORT',
                    'icon' => 'support.png',
                    'url'  => 'http://forum.jbzoo.com/',
                ],
            ]
        ]); ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>
