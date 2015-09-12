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
 * Class JBCSVCategory
 */
class JBCSVCategory
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @var Category
     */
    protected $_category = null;

    /**
     * Constructor
     * @param Category $category
     */
    function __construct(Category $category)
    {
        $this->app       = App::getInstance('zoo');
        $this->_category = $category;
    }

    /**
     * Export data to CSV cell
     * @return string
     */
    public function toCSV()
    {
        return null;
    }

    /**
     * Import data from CSV cell
     * @param $value
     * @return null
     */
    public function fromCSV($value)
    {
        return $this->_category;
    }


    /**
     * Get bool value from CSV
     * @param string $value
     * @return int
     */
    protected function _getBool($value)
    {
        $value = JString::strtolower(JString::trim($value));

        if (in_array($value, array('1', 'y', 'yes', 'on'))) {
            return 1;
        }

        if ((int)$value >= 1) {
            return 1;
        }

        return 0;
    }

    /**
     * Get int value
     * @param string $value
     * @return int
     */
    protected function _getInt($value)
    {
        return (int)$this->_getString($value);
    }

    /**
     * Get clean string
     * @param $value
     * @return string
     */
    protected function _getString($value)
    {
        return JString::trim($value);
    }

    /**
     * Get alias string
     * @param $value
     * @return string
     */
    protected function _getAlias($value)
    {
        return $this->app->string->sluggify($value, false);
    }

    /**
     * Get date from string
     * @param string $value
     * @param null   $default
     * @return string
     */
    protected function _getDate($value, $default = null)
    {
        if ($time = strtotime($this->_getString($value))) {
            return date('Y-m-d H:i:s', $time);
        }

        return $default;
    }


    /**
     * Pack data from string
     * @param      $data
     * @param bool $nullElement
     * @return string
     */
    protected function _packToLine($data, $nullElement = false)
    {
        $result = array();

        if (!empty($data)) {
            $from = array(':', ';');
            $to   = array('%col%', '%sem%');

            foreach ($data as $key => $value) {
                $key = strtolower($key);
                if ($nullElement) {
                    $result[] = $key . ':' . str_replace($from, $to, $value);
                } else {
                    if (strlen($value) > 0 && $key) {
                        $result[] = $key . ':' . str_replace($from, $to, $value);
                    }
                }
            }
        }

        return implode(';', $result);
    }

    /**
     * Unpack data from string
     * @param $string
     * @return array
     */
    protected function _unpackFromLine($string)
    {
        $result = array();

        if (!empty($string)) {
            $from = array('%col%', '%sem%');
            $to   = array(':', ';');

            $list = explode(';', $string);
            foreach ($list as $item) {
                if (strpos($item, ':')) {
                    list($key, $value) = explode(':', $item);
                    $key          = strtolower($key);
                    $result[$key] = str_replace($from, $to, $value);
                }
            }
        }

        return $result;
    }

}
