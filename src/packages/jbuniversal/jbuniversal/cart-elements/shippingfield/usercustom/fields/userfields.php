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


$jCustom          = $parent->element->getCustomFields();
$jWhiteList       = array('name', 'username', 'email', 'registerDate', 'lastvisitDate');
$jProperties      = array_keys($this->app->jbuser->getFields());
$jUserFieldList   = (array)array_intersect($jWhiteList, $jProperties);
$jUserFieldList   = array_combine($jUserFieldList, $jUserFieldList);
$jUserFieldLabel  = JText::_('User main fields');
$customFieldLabel = JText::_('User custom fields');

$userFields = array($jUserFieldLabel => $jUserFieldList);

foreach ($jCustom as $field) {
    $userFields[$customFieldLabel][$field] = JText::_($field);
}

echo $this->app->jbhtml->selectGrouped($userFields, $control_name . '[user_field]', null, $value);