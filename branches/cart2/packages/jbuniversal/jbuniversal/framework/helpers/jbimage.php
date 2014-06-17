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
 * Class JBImageHelper
 */
class JBImageHelper extends AppHelper
{

    /**
     * Get image by params
     * @param $name
     * @param $params
     * @return bool|array
     */
    public function get($name, $params)
    {
        if ($image = $params->get('content.' . $name)) {

            $imageHeight = $params->get('content.' . $name . '_height');
            if (!$imageHeight) {
                $imageHeight = $params->get('config.' . $name . '_height');
            }

            $imageWidth = $params->get('content.' . $name . '_width');
            if (!$imageWidth) {
                $imageWidth = $params->get('config.' . $name . '_width');
            }

            return $this->app->html->_('zoo.image', $image, $imageWidth, $imageHeight);
        }

        return false;
    }

    /**
     * Resize image
     * @param string $imagePath
     * @param int $width
     * @param int $height
     * @return object
     */
    public function resize($imagePath, $width = 0, $height = 0)
    {
        if (JFile::exists(JPATH_ROOT . '/' . $imagePath)) {
            $orig = JPath::clean(JPATH_ROOT . '/' . $imagePath);

        } else if (JFile::exists($imagePath)) {
            $orig = JPath::clean($imagePath);

        } else if ($this->isExternal($imagePath)) {
            $orig = $imagePath;

        } else {
            $orig = $this->app->jbimage->placeholder($width, $height);
        }

        // get info
        if (($width == 0 && $height == 0)) {
            $info = $this->getImageInfo($orig);

            $info->origUrl = $orig;
            $info->orig    = $orig;

        } else if ($this->isExternal($orig)) {
            $info = $this->getImageInfo($orig);

            $info->width   = $width;
            $info->height  = $height;
            $info->origUrl = $orig;
            $info->orig    = $orig;

        } else {
            $file = $this->app->zoo->resizeImage($orig, $width, $height);
            $info = $this->getImageInfo($file);

            $info->origUrl = $this->getRelative($file);
            $info->orig    = $orig;
        }

        return $info;
    }

    /**
     * Get placeholder URL
     * @param int $width
     * @param int $height
     * @return null|string
     */
    public function placeholder($width, $height = 0)
    {
        $serviceUrl = 'http://www.placehold.it/';

        $result = null;
        $width  = (int)$width;
        $height = (int)$height;

        if ($width && $height) {
            $result = $serviceUrl . $width . 'x' . $height;

        } elseif ($width) {
            $result = $serviceUrl . $width;

        } elseif ($height) {
            $result = $serviceUrl . $height;
        }

        return $result;
    }

    /**
     * Get image info
     * @param $path
     * @return object|null
     */
    public function getImageInfo($path)
    {
        if ($this->isExternal($path)) {
            return (object)array(
                'width'  => 0,
                'height' => 0,
                'mime'   => 'image/jpg',
                'bits'   => 8,
                'path'   => $path,
                'rel'    => $path,
                'url'    => $path,
            );

        } else if (JFile::exists($path)) {

            $info = getimagesize($path);

            return (object)array(
                'width'  => $info[0],
                'height' => $info[1],
                'mime'   => $info['mime'],
                'bits'   => $info['bits'],
                'path'   => JPath::clean($path),
                'rel'    => $this->getRelative($path),
                'url'    => $this->getUrl($path),
            );
        }

        return (object)array();
    }

    /**
     * Get URL
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        if (!$this->isExternal($path)) {
            return JUri::root() . $this->getRelative($path);
        }

        return $path;
    }

    /**
     * Get relative
     * @param $path
     * @return mixed
     */
    public function getRelative($path)
    {
        if (!$this->isExternal($path)) {
            return str_replace('//', '/', $this->app->path->relative($path));
        }

        return $path;
    }

    /**
     * Is external
     * @param $path
     * @return bool
     */
    public function isExternal($path)
    {
        return strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0;
    }

    /**
     * Convert virtual path to URL
     * @param $path
     * @return bool|string
     */
    public function pathToUrl($path)
    {
        if ($fullPath = JPath::clean($this->app->path->path($path))) {
            return $this->getUrl($fullPath);
        }

        return false;
    }

}
