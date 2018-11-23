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
    <div class="uk-width-2-10">&nbsp;</div>
    <div class="uk-width-6-10">
        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_INDEX'); ?></h2>

        <?php echo $this->partial('icons', [
            'items' => [
                [
                    'name' => 'JBZOO_ICON_INDEX_CART',
                    'icon' => 'cart.png',
                    'link' => ['controller' => 'jbcart', 'task' => 'index'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_IMPORT',
                    'icon' => 'import.png',
                    'link' => ['controller' => 'jbimport', 'task' => 'index'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_EXPORT',
                    'icon' => 'export.png',
                    'link' => ['controller' => 'jbexport', 'task' => 'index'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_TOOLS',
                    'icon' => 'tools.png',
                    'link' => ['controller' => 'jbtools', 'task' => 'index'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_CONFIG',
                    'icon' => 'config.png',
                    'link' => ['controller' => 'jbconfig', 'task' => 'index'],
                ],
                [
                    'name' => 'JBZOO_ICON_INDEX_INFO',
                    'icon' => 'jbzoo.png',
                    'link' => ['controller' => 'jbinfo', 'task' => 'index'],
                ],
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
    <div class="uk-width-2-10">&nbsp;</div>
</div>
