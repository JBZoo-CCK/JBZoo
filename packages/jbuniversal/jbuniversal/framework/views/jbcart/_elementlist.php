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

$fields = array(
    'option'     => $this->app->jbrequest->get('option'),
    'controller' => $this->app->jbrequest->getCtrl(),
    'task'       => $this->app->jbrequest->get('task'),
);

if ($this->get('elementList')) : ?>
    <form action="index.php" method="get" class="select-list-form">

        <?php foreach ($fields as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        } ?>

        <input type="hidden" name="layout" value="<?php echo $this->layout; ?>" class="jsLayout" />

        <label for="element-list-select"><?php echo JText::_('JBZOO_ADMIN_POSITIONS_ELEMENT_LIST'); ?></label>
        <?php echo $this->app->jbhtml->select($this->get('elementList'), 'element', '', $this->element, 'element-list-select'); ?>

    </form>

    <script type="text/javascript">
        jQuery(function ($) {

            var $select = $('#element-list-select').change(function () {
                var $select = $(this);
                $('.jsAssignElements .jsElement').val($select.val());
                $select.closest('form').submit();
            });

            $('.jsElement').val($select.val());
        });
    </script>

<?php endif;