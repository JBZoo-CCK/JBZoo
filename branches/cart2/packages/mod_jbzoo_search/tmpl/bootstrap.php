<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$modHelper->app->jbassets->css('jbassets:css/bootstrap.min.css');

$formAttrs = array(
    'id'     => $modHelper->getModuleId(),
    'name'   => $modHelper->getModuleId(),
    'method' => 'get',
    'action' => JRoute::_('index.php?Itemid=' . $modHelper->getMenuId()),
    'class'  => array(
        'jsFilter',
        'jbfilter',
        'jbfilter-' . $itemLayout,
    ),
);

?>

<div class="jbzoo jbfilter-wrapper">

    <form <?php echo $modHelper->attrs($formAttrs); ?>>
        <?php
        echo $modHelper->partial('_fields');

        echo '<div class="jbfilter-static">';
        echo $modHelper->partial('_pages');
        echo $modHelper->partial('_order');
        echo $modHelper->partial('_logic');
        echo '</div>';

        echo $modHelper->partial('_buttons', array('btnClass' => 'btn btn-default'));
        echo $modHelper->partial('_hidden');
        ?>
    </form>

</div>

