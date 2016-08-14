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


/**
 * Class JBAdminMenu
 */
class JBAdminMenu
{
    /**
     *  Add admin menu to Joomla 3.6+
     */
    public static function render()
    {
        if (version_compare(JVERSION, '3.6', '<')) {
            return false;
        }

        $menu = new JAdminCssMenu();
        $list = JBZoo::init()->getAdminMenu();
        if (!$list) {
            return false;
        }

        $menu->addChild(new JBZooJMenuNode('JBZoo Admin', '#', null), true);

        foreach ($list as $parentKey => $parentItem) {

            $target     = isset($parentItem['target']) ? '_blank' : null;
            $className  = 'class:' . $parentKey . ' ' . $parentKey . '-item';
            $parentName = $className . ' parent-link';

            if (isset($parentItem['children']) && count($parentItem['children'])) {

                $menu->addChild(new JBZooJMenuNode($parentItem['name'], $parentItem['url'], $parentName, false, $target), true);
                foreach ((array)$parentItem['children'] as $childItem) {
                    if ($childItem === 'divider') {
                        $menu->addSeparator();
                    } else {
                        $menu->addChild(new JBZooJMenuNode($childItem['name'], $childItem['url'], $className));
                    }
                }

                $menu->getParent();

            } else {
                if ($parentItem === 'divider') {
                    $menu->addSeparator();
                } else {
                    $menu->addChild(new JBZooJMenuNode($parentItem['name'], $parentItem['url'], $parentName, false, $target));
                }
            }
        }

        $menu->getParent();

        ob_start();
        $menu->renderMenu('jbzoo-adminmenu-root');
        $menuHtml = "<!-- JBZoo AdminMenu -->\n" . ob_get_contents() . "\n<!-- /JBZoo AdminMenu -->";
        ob_end_clean();


        $menuHtml = JString::str_ireplace(
            "<ul id=\"jbzoo-adminmenu-root\" >\n<li class=\"dropdown\">",
            "<li class=\"dropdown\" id=\"jbzoo-adminmenu\">",
            $menuHtml
        );

        $app  = JFactory::getApplication();
        $body = $app->getBody();
        $body = JString::str_ireplace(
            "</ul>\n<ul id=\"nav-empty\" class=\"dropdown-menu nav-empty hidden-phone\"></ul>",
            $menuHtml,
            $body
        );

        $app->setBody($body);
    }
}

if (file_exists(JPATH_ADMINISTRATOR . '/modules/mod_menu/menu.php')) {

    require_once JPATH_ADMINISTRATOR . '/modules/mod_menu/menu.php';

    if (class_exists('JMenuNode')) {
        class JBZooJMenuNode extends JMenuNode
        {
            /**
             * @inheritdoc
             */
            public function __construct($title, $link = null, $class = null, $active = false, $target = null, $titleicon = null)
            {
                $this->title  = $titleicon ? $title . $titleicon : $title;
                $this->link   = JFilterOutput::ampReplace($link);
                $this->class  = $class;
                $this->active = $active;

                $this->id = null;

                if (!empty($link) && $link !== '#') {
                    $uri    = new JUri($link);
                    $params = $uri->getQuery(true);
                    $parts  = array();

                    foreach ($params as $value) {
                        $parts[] = str_replace(array('.', '_'), '-', $value);
                    }

                    $this->id = $this->_multiImplode('-', $parts);
                }

                $this->target = $target;
            }

            protected function _multiImplode($glue, $array)
            {
                $ret = '';

                foreach ($array as $item) {
                    if (is_array($item)) {
                        $ret .= $this->_multiImplode($glue, $item) . $glue;
                    } else {
                        $ret .= $item . $glue;
                    }
                }

                $ret = substr($ret, 0, 0 - strlen($glue));

                return $ret;
            }
        }
    }
}

