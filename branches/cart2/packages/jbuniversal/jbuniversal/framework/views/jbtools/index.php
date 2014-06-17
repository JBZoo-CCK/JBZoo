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

<div id="sidebar" class="uk-width-1-6">
    <?php echo $this->partial('navigation'); ?>
</div>

<div class="uk-width-4-6">
    <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_INDEX'); ?></h2>

    <?php echo $this->partial('icons', array('items' => array(
        array(
            'name' => 'JBZOO_ICON_TOOLS_REINDEX',
            'icon' => 'reindex.png',
            'link' => array('task' => 'reindex'),
        ),
        array(
            'name' => 'JBZOO_ICON_TOOLS_REINDEX_ZOO',
            'icon' => 'reindex_zoo.png',
            'link' => array('task' => 'cleandb'),
        ),
        array(
            'name' => 'JBZOO_ICON_TOOLS_FILECHECK_JBZOO',
            'icon' => 'filecheck_jbzoo.png',
            'link' => array('task' => 'checkfiles'),
        ),
        array(
            'name' => 'JBZOO_ICON_TOOLS_FILECHECK_ZOO',
            'icon' => 'filecheck_zoo.png',
            'link' => array('task' => 'checkfileszoo'),
        ),
    ))); ?>

    <?php echo $this->partial('footer'); ?>
</div>
