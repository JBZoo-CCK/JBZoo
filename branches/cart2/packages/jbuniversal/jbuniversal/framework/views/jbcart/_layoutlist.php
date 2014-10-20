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

$fields = array(
    'option'     => $this->app->jbrequest->get('option'),
    'controller' => $this->app->jbrequest->getCtrl(),
    'task'       => $this->app->jbrequest->get('task'),
);

if ($this->get('layoutList')) : ?>
    <form action="index.php" method="get" class="select-list-form">

        <?php foreach ($fields as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        } ?>

        <input type="hidden" name="element" value="<?php echo $this->element; ?>" class="jsElement"/>

        <label for="layout-list-select"><?php echo JText::_('JBZOO_ADMIN_POSITIONS_LAYOUT_LIST'); ?></label>
        <?php echo $this->app->jbhtml->select($this->get('layoutList'), 'layout', '', $this->layout, 'layout-list-select'); ?>
    </form>

    <script type="text/javascript">
        jQuery(function ($) {

            var $select = $('#layout-list-select').change(function () {
                var $select = $(this);
                $('.jsAssignElements .jsLayout').val($select.val());
                $select.closest('form').submit();
            });

            $('.jsLayout').val($select.val());
        });
    </script>
<?php endif;
