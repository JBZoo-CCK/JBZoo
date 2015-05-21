<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$elemId = $this->app->jbstring->getId('jbadvert');

$toggleList = array(
    0 => JText::_('JBZOO_NO'),
    1 => JText::_('JBZOO_YES'),
    2 => JText::_('JBZOO_JBADVERT_EDIT_IS_MODIFIED_EXEC'),
);

$mode = $this->_getMode();
if ($mode == ElementJBAdvert::MODE_CATEGORY) {
    unset($toggleList[2]);
}

$toggler = $this->app->jbhtml->radio($toggleList, $this->getControlName('is_modified'), array(), $this->get('is_modified', 0));

echo '<div class="jbzoo"><div id="' . $elemId . '" class="jbadvert-edit">';
echo $this->app->jbhtml->dataList(array(
    'JBZOO_JBADVERT_EDIT_IS_MODIFIED' => $toggler,
    'JBZOO_JBADVERT_EDIT_PRICE'       => JBCart::val($this->get('price', 0)),
    'JBZOO_JBADVERT_EDIT_ORDER'       => $this->_getRelatedOrder(true),
    'JBZOO_JBADVERT_EDIT_MODIFIED'    => $this->_getLastModified(),
    'JBZOO_JBADVERT_EDIT_MODE'        => JText::_('JBZOO_JBADVERT_MODE_' . $this->_getMode()),
    'JBZOO_JBADVERT_EDIT_PARAMS'      => $this->_renderModifierParams(),
));
echo '</div></div>';

$this->loadAssets();
$this->app->jbassets->widget('#' . $elemId, 'JBZoo.JBAdvert', array(
    'text_exec_alert' => JText::_('JBZOO_JBADVERT_EXEC_ALERT')
));