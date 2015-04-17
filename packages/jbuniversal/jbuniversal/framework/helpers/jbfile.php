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
 * Class JBFileHelper
 */
class JBFileHelper extends AppHelper
{
    /**
     * Read custom data from file
     * @param string $path
     * @param bool   $safeMode
     * @return null|string
     */
    public function read($path, $safeMode = false)
    {
        $path = JPath::clean($path);

        if (JFile::exists($path)) {

            if ($safeMode) {
                $handle   = fopen($path, "rb");
                $contents = fread($handle, filesize($path));
                fclose($handle);
            } else {
                $contents = file_get_contents($path);
            }

            return $contents;
        }

        return null;
    }

    /**
     * @param $file
     * @param $data
     * @return bool
     */
    public function save($file, $data)
    {
        $dir = dirname($file);
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }

        return JFile::write($file, $data);
    }

    /**
     * Quickest way for getting first file line
     * @param string $file
     * @return string
     */
    public function firstLine($file)
    {
        $cacheRes  = fopen($file, 'r');
        $firstLine = fgets($cacheRes);
        fclose($cacheRes);

        return $firstLine;
    }
}
