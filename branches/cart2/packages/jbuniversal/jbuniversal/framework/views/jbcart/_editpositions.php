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
echo $this->partial('orderlist');
echo JBZOO_CLR;

?>

<form class="jbzoo-assign-elements assign-elements jsAssignElements" action="index.php" method="post" name="adminForm"
      id="adminForm" accept-charset="utf-8">
    
    <?php echo $this->app->jbhtml->hiddens(array(
        'option'     => 'com_zoo',
        'controller' => 'jbcart',
        'task'       => $task,
        'layout'     => array('value' => $this->layout, 'class' => 'jsLayout'),
        'element'    => array('value' => $this->element, 'class' => 'jsElement'),
        'group'      => $groupKey,
        'redirect'   => $redirectUrl,
        '_token'     => '_token',
    )); ?>
    
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

    <?php echo JBZOO_CLR; ?>

</form>

<?php echo $this->app->jbassets->widget('.jsAssignElements', 'JBZooEditPositions', array(
    'isElementTmpl'     => $this->isElementTmpl,
    'urlAddElement'     => $this->app->jbrouter->admin(array('task' => 'addElement')),
    'textEmptyPosition' => JText::_('JBZOO_ADMIN_POSITIONS_EMPTY_POSITION'),
    'textNoElements'    => JText::_('JBZOO_ADMIN_POSITIONS_NO_ELEMENTS'),
    'textElementRemove' => JText::_('JBZOO_ADMIN_POSITIONS_REMOVE'),
), true); ?>
