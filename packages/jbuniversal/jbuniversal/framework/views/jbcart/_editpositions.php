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


// get vars
$layoutList = isset($layoutList) ? $layoutList : array();
$positions = isset($positions) ? $positions : array();
$groupList = isset($groupList) ? $groupList : array();
$dragElements = isset($dragElements) ? $dragElements : array();
$elementsParams = isset($elementsParams) ? $elementsParams : array();
$elementGroup = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;

// add misc
$this->app->html->_('behavior.tooltip');
$this->app->jbtoolbar->save();

?>

<?php echo $this->partial('layoutlist', array(
    'layoutList' => $layoutList
)); ?>

<form class="assign-elements jsAssignElements" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

    <!-- left col -->
    <div class="col col-left width-65" style="margin-right: 24px;">

        <?php echo $this->partial('positions', array(
            'positions'      => $positions,
            'elementGroup'   => $elementGroup,
            'elementsParams' => $elementsParams,
        )); ?>
    </div>


    <!-- right col -->
    <div id="add-element" class="col col-right width-30">
        <?php

        echo $this->partial('addelements', array(
            'groupList' => $groupList,
        ));

        echo $this->partial('dragelements', array(
            'dragElements' => $dragElements,
            'elementGroup' => $elementGroup,
        ));

        ?>
    </div>

    <div class="clear clr"></div>

    <input type="hidden" name="option" value="com_zoo" />
    <input type="hidden" name="controller" value="jbcart" />
    <input type="hidden" name="task" value="savePositions" />
    <input type="hidden" name="layout" value="<?= $this->app->jbrequest->get('layout'); ?>" class="jsLayout" />
    <input type="hidden" name="group" value="<?php echo $this->task; ?>" />
    <input type="hidden" name="redirect"
           value="<?php echo $this->app->jbrouter->admin(array('layout' => $this->app->jbrequest->get('layout'))); ?>" />
    <?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
    jQuery(function ($) {

        $('.jsAssignElements').JBZooEditPositions({
            'urlAddElement'    : '<?php echo $this->app->jbrouter->admin(array('task' => 'addElement'));?>',
            'textEmptyPosition': '<?php echo JText::_('JBZOO_ADMIN_POSITIONS_EMPTY_POSITION');?>',
            'textRemove'       : '<?php echo JText::_('JBZOO_ADMIN_POSITIONS_REMOVE');?>'
        });

    });
</script>
