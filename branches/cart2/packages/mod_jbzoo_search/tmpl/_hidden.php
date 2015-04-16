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


echo $modHelper->renderHidden(array(
    'exact'      => $params->get('exact', 0),
    'controller' => 'search',
    'option'     => 'com_zoo',
    'task'       => 'filter',
    'type'       => array('value' => $modHelper->getType(), 'class' => 'jsItemType'),
    'app_id'     => array('value' => $modHelper->getAppId(), 'class' => 'jsApplicationId'),
    'Itemid'     => $modHelper->getMenuId(),
));
