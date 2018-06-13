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

echo $modHelper->renderHidden([
    'exact'      => $params->get('exact', 0),
    'controller' => 'search',
    'option'     => 'com_zoo',
    'task'       => 'filter',
    'type'       => ['value' => $modHelper->getType(), 'class' => 'jsItemType'],
    'app_id'     => ['value' => $modHelper->getAppId(), 'class' => 'jsApplicationId'],
    'Itemid'     => $modHelper->getMenuId(),
]);
