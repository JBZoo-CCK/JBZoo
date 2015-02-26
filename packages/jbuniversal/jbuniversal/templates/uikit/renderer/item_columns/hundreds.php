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

$this->app->jbassets->tablesorter();

if ($vars['count']) : ?>

    <table class="jsTableSorter uk-table uk-table-hover">
        <caption><?php echo JText::_('JBZOO_TMPL_TABLE_CAPTION'); ?></caption>

        <thead>
        <tr>
            <th class="sku-header">#</th>
            <th class="value-header"><?php echo JText::_('JBZOO_ELEMENT_CORE_VALUE'); ?></th>
            <th class="description-header"><?php echo JText::_('JBZOO_ELEMENT_CORE_DESCRIPTION'); ?></th>
            <th class="color-header"><?php echo JText::_('JBZOO_ITEM_COLUMNS_COLOR'); ?></th>
            <th class="balance-header"><?php echo JText::_('JBZOO_ELEMENT_CORE_BALANCE'); ?></th>
            <th class="buttons-header"></th>
        </tr>
        </thead>

        <tbody>
        <?php echo implode("\n", $vars['objects']); ?>
        </tbody>

    </table>

    <?php echo $this->app->jbassets->widget('.jsTableSorter', 'tablesorter', array(), true);
endif;
