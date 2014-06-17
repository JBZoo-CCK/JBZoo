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

    <table class="jsTableSorter tablesorter zebra">
        <caption><?php echo JText::_('JBZOO_TMPL_TABLE_CAPTION'); ?></caption>

        <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Code</th>
            <th>Form</th>
            <th>Quality</th>
            <th>Color</th>
            <th>Size</th>
            <th>Width</th>
        </tr>
        </thead>

        <tbody>
            <?php
            foreach ($vars['objects'] as $object) :
                echo $object;
            endforeach;
            ?>
        </tbody>

    </table>

    <script type="text/javascript">
        jQuery(function ($) {
            $('.jsTableSorter').tablesorter({});
        });
    </script>

<?php endif;
