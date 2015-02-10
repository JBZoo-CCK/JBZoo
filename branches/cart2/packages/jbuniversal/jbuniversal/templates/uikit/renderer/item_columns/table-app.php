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

echo $this->app->jbjoomla->renderPosition('jbzoo_database_filter');

if ($vars['count']) : ?>

    <table class="jsTableSorter uk-table uk-table-hover">
        <caption><?php echo JText::_('JBZOO_TMPL_TABLE_CAPTION'); ?></caption>

        <thead>
        <tr>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_1'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_2'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_3'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_4'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_5'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_6'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_7'); ?></th>
            <th><?php echo JText::_('JBZOO_TMPL_TABLE_COL_8'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php echo implode(" \n", $vars['objects']); ?>
        </tbody>

    </table>

    <?php echo $this->app->jbassets->widget('.jsTableSorter', 'tablesorter', array(), true); ?>
    

<?php endif;
