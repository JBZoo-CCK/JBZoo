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
 * Class JBPathHelper
 */
class JBPathHelper extends AppHelper
{

    public function getInfo($path)
    {
        if (JFile::exists($path) || JFolder::exists($path)) {
            return array(
                'path'        => $path,
                'realpath'    => realpath($path),
                'owner/group' => filegroup($path) . '/' . fileowner($path),
                'permissions' => $this->getPerms($path),
                'is_readable' => is_readable($path),
                'is_writable' => is_writable($path),
            );
        }

        return 'No exists';
    }

    /**
     * Gets file permissions
     * @param $filename
     * @return string
     */
    public function getPerms($filename)
    {
        $perms = fileperms($filename);

        if (($perms & 0xC000) == 0xC000) { // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) { // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) { // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) { // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) { // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe
            $info = 'p';
        } else { // Unknown
            $info = 'u';
        }

        // owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

        // group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

        // other
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    /**
     * Return system path
     * @param string $type
     * @param string $addRelpath
     * @return null|string
     */
    public function sysPath($type, $addRelpath = null)
    {
        $result = null;

        if ($type == 'tmp') {
            $result = $this->app->path->path('tmp:');

        } else if ($type == 'cache') {
            $result = JPATH_ROOT . '/cache';
        }

        if ($addRelpath) {
            $result = $result . DS . $addRelpath;
        }

        $result = JPath::clean($result);

        return $result;
    }

}