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

$this->app->jbdebug->mark('layout::subcategories::start');

if ((int)$this->getView()->application->params->get('global.config.column_heightfix', 0)) {
    $this->app->jbassets->heightFix('.subcategory-column');
}

// remove empty categories
if (!$vars['params']->get('template.subcategory_empty', 0)) {

    $objects = array();
    foreach ($vars['objects'] as $category) {
        if ($category->itemCount()) {
            $objects[] = $category;
        }
    }

} else {
    $objects = $vars['objects'];
}


echo $this->columns('subcategory', $objects);


$this->app->jbdebug->mark('layout::subcategories::finish');