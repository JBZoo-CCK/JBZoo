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


/**
 * Class JBBookmarksHelper
 */
class JBBookmarksHelper extends AppHelper
{

    /**
     * Get all available bookmarks
     * @return array $bookmarks
     */
    public function getBookmarkList()
    {
        // init var
        $bookmarks = array();

        // load xml file
        if ($xml = $this->app->path->path('jbconfig:jbbookmarks.xml')) {
            $xml = @simplexml_load_file($xml);
            foreach ($xml as $bookmark => $list) {
                $bookmarks[(string)$list->attributes()->type] = array(
                    'label' => (string)$list->attributes()->name,
                    'link'  => (string)$list->link,
                    'click' => (string)$list->click
                );
            }
        }

        return $bookmarks;
    }
}