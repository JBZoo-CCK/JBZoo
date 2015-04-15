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

$zoo = App::getInstance('zoo');

$zoo->jbassets->uikit(false, true); // TODO get gradient option from catalog configs

$formAttrs = array(
    'id'     => $filterHelper->getFormId(),
    'method' => 'get',
    'action' => JRoute::_('index.php?Itemid=' . $filterHelper->getMenuId()),
    'name'   => $filterHelper->getFormId(),
    'class'  => array(
        'jsFilter',
        'jbfilter',
        'jbfilter-' . $itemLayout,
        'uk-form',
    ),
);

?>

<div class="jbzoo jbfilter-wrapper">

    <form <?php echo $zoo->jbhtml->buildAttrs($formAttrs); ?>>
        <?php
        echo $filterHelper->partial('_fields');

        echo '<div class="jbfilter-static">';
        echo $filterHelper->partial('_pages');
        echo $filterHelper->partial('_order');
        echo $filterHelper->partial('_logic');
        echo '</div>';

        echo $filterHelper->partial('_buttons', array('btnClass' => 'uk-button'));
        echo $filterHelper->partial('_hidden');
        ?>
    </form>

</div>