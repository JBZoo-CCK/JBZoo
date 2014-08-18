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

$layoutList = isset($layoutList) ? $layoutList : array();
$layout = $this->app->jbrequest->get('layout', 'index');
$jbprice = $this->app->jbrequest->get('jbprice');

$fields = array(
    'option'     => $this->app->jbrequest->get('option'),
    'controller' => $this->app->jbrequest->getCtrl(),
    'task'       => $this->app->jbrequest->get('task'),
);
?>

<?php if (!empty($layoutList) || !empty($priceList)) : ?>
    <form action="index.php" method="get">

        <?php foreach ($fields as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        } ?>

        <?php if (!empty($layoutList)) : ?>
            <label for="layout-list-select">
                <?php echo JText::_('JBZOO_ADMIN_POSITIONS_LAYOUT_LIST'); ?>
            </label>

            <?php echo $this->app->jbhtml->select($layoutList, 'layout', '', $layout, 'layout-list-select'); ?>

        <?php endif; ?>

        <?php if (!empty($priceList)) :
            echo $this->partial('priceslist', array(
                'priceList' => $priceList
            ));
        endif; ?>

    </form>

    <script type="text/javascript">
        jQuery(function ($) {

            var $select = $('#layout-list-select').change(function () {
                var $select = $(this);
                $('.jsAssignElements .jsLayout').val($select.val());
                $select.closest('form').submit();
            });

            $('.jsAssignElements .jsLayout').val($select.val());

            var $select = $('#jbprice-list-select').change(function () {
                var $select = $(this);

                $('.jsAssignElements .jsJBprice').val($select.val());
                $select.closest('form').submit();
            });

            $('.jsAssignElements .jsJBprice').val($select.val());
        });
    </script>

<?php endif; ?>