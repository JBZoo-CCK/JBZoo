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


$userField = $this->config->get('user_field', 'name');


JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php'); 
        
$user = JFactory::getUser();

$context = 'com_users.user';
$fieldsarr = array();
$poleuser = $user->id;
$poleuserstd = new \stdClass;
$poleuserstd->id = $poleuser;
$bigProfile = FieldsHelper::getFields($context, $poleuserstd, false);
$properties = array_keys($this->app->jbuser->getFields());
$whiteList = ['name' => $user->name, 'username' => $user->username, 'email' => $user->email, 'registerDate' => $user->registerDate, 'lastvisitDate' => $user->lastvisitDate];

$list = (array)array_intersect($whiteList, $properties);
$list = array_combine($list, $list);

foreach ($bigProfile as $poleProfile) {
    if (!empty($poleProfile->name)) {
        $fieldsarr[$poleProfile->name] = trim($poleProfile->value);
    }
}

$userFields = array_merge($whiteList,$fieldsarr);

$attrs = array(
    'type'  => 'text',
    'name'  => $this->getControlName('value'),
    'id'    => $this->htmlId(true),
    'value' => isset($userFields[$userField]) ? $userFields[$userField] : null,
);

echo '<textarea ' . $this->app->jbhtml->buildAttrs($attrs) . '>' . $value . '</textarea>';
