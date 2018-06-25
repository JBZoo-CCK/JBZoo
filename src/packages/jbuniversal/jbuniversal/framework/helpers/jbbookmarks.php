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