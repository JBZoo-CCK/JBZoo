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

$elemId = $this->app->jbstring->getId('jbadvert');

$toggleList = array(
    0 => JText::_('JBZOO_NO'),
    1 => JText::_('JBZOO_YES'),
    2 => JText::_('JBZOO_JBADVERT_EDIT_IS_MODIFIED_EXEC'),
);

//$mode = $this->_getMode();
//if ($mode == ElementJBAdvert::MODE_CATEGORY) {
//    unset($toggleList[2]);
//}

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