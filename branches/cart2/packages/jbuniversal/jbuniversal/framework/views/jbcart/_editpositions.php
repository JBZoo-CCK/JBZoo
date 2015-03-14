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


// get vars
$task           = isset($this->saveTask) ? $this->saveTask : 'savePositions';
$positions      = isset($positions) ? $positions : array();
$groupList      = isset($groupList) ? $groupList : array();
$dragElements   = isset($dragElements) ? $dragElements : array();
$elementsParams = isset($elementsParams) ? $elementsParams : array();
$systemElements = isset($systemElements) ? $systemElements : array();
$elementGroup   = isset($elementGroup) ? $elementGroup : JBCartElement::DEFAULT_GROUP;
$groupKey       = isset($this->groupKey) ? $this->groupKey : $this->task;

// add misc
$this->app->html->_('behavior.tooltip');
$this->app->jbtoolbar->save();

// create redirect url
$redirectUrl = $this->app->jbrouter->admin(array('element' => $this->element, 'layout' => $this->layout));

echo $this->partial('elementlist');
echo $this->partial('layoutlist');
echo $this->partial('orderlist'); ?>
<div class="clear clr"></div>

<form class="jbzoo-assign-elements assign-elements jsAssignElements" action="index.php" method="post" name="adminForm"
      id="adminForm" accept-charset="utf-8">

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

        echo $this->partial('systemelements', array(
            'systemElements' => $systemElements,
            'elementGroup'   => 'render',
        ));

        ?>
    </div>

    <div class="clear clr"></div>

    <input type="hidden" name="option" value="com_zoo" />
    <input type="hidden" name="controller" value="jbcart" />
    <input type="hidden" name="task" value="<?php echo $task; ?>" />
    <input type="hidden" name="layout" value="<?php echo $this->layout; ?>" class="jsLayout" />
    <input type="hidden" name="element" value="<?php echo $this->element; ?>" class="jsElement" />
    <input type="hidden" name="group" value="<?php echo $groupKey; ?>" />
    <input type="hidden" name="redirect" value="<?php echo $redirectUrl; ?>" />
    <?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo $this->app->jbassets->widget('.jsAssignElements', 'JBZooEditPositions', array(
    'urlAddElement'     => $this->app->jbrouter->admin(array('task' => 'addElement')),
    'textEmptyPosition' => JText::_('JBZOO_ADMIN_POSITIONS_EMPTY_POSITION'),
    'textNoElements'    => JText::_('JBZOO_ADMIN_POSITIONS_NO_ELEMENTS'),
    'textElementRemove' => JText::_('JBZOO_ADMIN_POSITIONS_REMOVE'),
), true); ?>
