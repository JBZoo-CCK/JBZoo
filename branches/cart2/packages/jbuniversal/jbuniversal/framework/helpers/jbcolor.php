<?php

/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JBColorHelper extends AppHelper
{

    /**
     * Get an array colors
     * @param  array  $colors
     * @param  string $path
     * @return array
     */
    public function getColors($colors, $path = '/images/jbcolor')
    {
        if (is_string($colors)) {
            $colors = explode(PHP_EOL, $colors);
        }

        $options = array();
        $default = true;

        if (strpos($path, JUri::root()) === 0) {
            $path = str_ireplace(JUri::root(), '', $path);
        } elseif (strpos($path, 'http') === 0) {
            $default = false;
        }

        $path = $this->_clean($path, '/\\');
        foreach ($colors as $color) {
            $color = $this->_clean($color);
            if (!empty($color)) {

                if (!$hasSeparator = strpos($color, '#')) {
                    continue;
                }

                list($label, $value) = explode('#', $color);
                $value = $this->_clean($value);

                if (empty($value)) {
                    continue;
                }

                if ($this->isFile($value)) {
                    $url     = str_ireplace('\\', '/', $path);
                    $newPath = JPath::clean($path);

                    if (!JFile::exists(JPATH_ROOT . DS . $newPath . DS . $value) && $default) {
                        $value = $this->app->path->url('jbassets:img/icon/noicon.png');
                    } elseif (!$default) {
                        $value = $url . '/' . $value;
                    } else {
                        $value = JUri::root() . $url . '/' . $value;
                    }

                }

                $options[$this->_clean($label)] = $this->_clean($value);
            }
        }

        return $options;
    }

    /**
     * @param $options
     * @return array
     */
    public function parse($options)
    {
        $colors  = explode(PHP_EOL, $options);
        $options = array();

        foreach ($colors as $color) {

            $color = $this->_clean($color);
            if (!empty($color)) {

                if (!$hasSeparator = strpos($color, '#')) {
                    $options[$this->_clean($color)] = '';
                } else {
                    list($label, $value) = explode('#', $color);
                    $options[$this->_clean($label)] = $this->_clean($value);
                }

            }

        }

        return $options;
    }

    /**
     * @param $new
     * @param $options
     * @return string
     */
    public function build($new, $options)
    {
        $result = array();
        $keys   = array_keys($options);
        $val    = '';

        if (!$hasSeparator = strpos($new, '#')) {
            $label = $this->_clean($new);
        } else {
            list($label, $val) = explode('#', $new);
        }

        $label = $this->_clean($label);

        if (empty($label)) {
            return false;
        }
        if (!in_array($label, $keys, true)) {
            $options[$label] = $val;
        }

        foreach ($options as $key => $value) {
            $result[] = $this->clean($key) . '#' . $this->clean($value);
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * Get an array default colors options
     * @param  array $colors
     * @return array
     */
    public function getDefaults($colors)
    {
        $options = array();
        foreach ($colors as $color) {
            $color = $this->_clean($color);

            if (!empty($color)) {
                $options[] = $color;
            }
        }

        return $options;
    }

    /**
     * Check is color or file
     * @param string $str
     * @return boolean
     */
    public function isFile($str)
    {
        $result = preg_match("#\.(?:png|gif|jpg|bmp|ico|jpeg)$#i", $str);
        return $result;
    }

    /**
     * Cleans data
     * @param string|array $data
     * @return string mixed
     */
    public function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$this->_clean($key)] = $this->_clean($value);
            }

            return $data;
        }

        return $this->_clean($data);
    }

    /**
     * @param  string      $str
     * @param  bool|string $charlist
     * @return mixed|string
     */
    private function _clean($str, $charlist = false)
    {
        return $this->app->jbvars->lower($str, false, $charlist);
    }

}