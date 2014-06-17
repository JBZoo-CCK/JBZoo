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

if ((int)$this->getView()->application->params->get('global.config.column_heightfix', 0)) {
    $this->app->jbassets->heightFix();
}

// remove empty categories
if (!$vars['params']->get('template.subcategory_empty', 0)) {
    $objects = array();
    foreach ($vars['objects'] as $category) {
        if ($category->itemCount()) { //
            $objects[] = $category;
        }
    }

} else {
    $objects = $vars['objects'];

}

if ($this->app->request->get('view', false) == 'frontpage') {
    echo '<h2 class="jbzoo-subtitle">' . JText::_('JBZOO_CATEGORIES') . '</h2>';
}

echo $this->columns('subcategory', $objects);
