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

?>

<div class="uk-grid">
    <div class="uk-width-2-10">&nbsp;</div>
    <div class="uk-width-6-10">
        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_INDEX'); ?></h2>

        <?php echo $this->partial('icons', array('items' => array(
            array(
                'name' => 'JBZOO_ICON_INDEX_CART',
                'icon' => 'cart.png',
                'link' => array('controller' => 'jbcart', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_IMPORT',
                'icon' => 'import.png',
                'link' => array('controller' => 'jbimport', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_EXPORT',
                'icon' => 'export.png',
                'link' => array('controller' => 'jbexport', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_TOOLS',
                'icon' => 'tools.png',
                'link' => array('controller' => 'jbtools', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_CONFIG',
                'icon' => 'config.png',
                'link' => array('controller' => 'jbconfig', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_INFO',
                'icon' => 'jbzoo.png',
                'link' => array('controller' => 'jbinfo', 'task' => 'index'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_PERFORMANCE',
                'icon' => 'performance.png',
                'link' => array('controller' => 'jbinfo', 'task' => 'performance'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_LICENCE',
                'icon' => 'licence.png',
                'link' => array('controller' => 'jbinfo', 'task' => 'licence'),
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_SUPPORT',
                'icon' => 'support.png',
                'url'  => 'http://forum.jbzoo.com/',
            ),
            array(
                'name' => 'JBZOO_ICON_INDEX_CLIENTAREA',
                'icon' => 'clientarea.png',
                'url'  => 'http://server.jbzoo.com/',
            )
        ))); ?>

        <?php echo $this->partial('footer'); ?>

    </div>
    <div class="uk-width-2-10">&nbsp;</div>
</div>
