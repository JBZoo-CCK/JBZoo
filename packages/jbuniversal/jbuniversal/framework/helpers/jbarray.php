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
 * Class JBArrayHelper
 */
class JBArrayHelper extends AppHelper
{
    /**
     * Convert values to names
     * @param array $options
     * @param array $values
     * @return array
     */
    public function valuesToName(array $options, array $values)
    {
        $result = array();

        foreach ($values as $value) {
            if (isset($options[$value])) {
                $result[] = $options[$value]['name'];
            }
        }

        return $result;
    }

    /**
     * Group array by key
     * @param array $array
     * @param string $key
     * @param string $value
     * @return array
     */
    public function groupByKey($array, $key = 'id', $value = null)
    {
        if (!is_array($array)) {
            return array();
        }

        $result = array();

        foreach ($array as $item) {

            if (is_array($item)) {

                if (isset($item[$key])) {
                    if ($value) {
                        $result[$item[$key]] = $item[$value];
                    } else {
                        $result[$item[$key]][] = $item;
                    }
                }

            } else if (is_object($item)) {

                if (isset($item->$key)) {

                    if ($value) {
                        $result[$item->$key] = $item->$value;
                    } else {
                        $result[$item->$key][] = $item;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Add cell to assoc array
     * @param array $array
     * @param string $key
     * @param mixed $val
     * @return array
     */
    function unshiftAssoc(array $array, $key, $val)
    {
        $array       = array_reverse($array, true);
        $array[$key] = $val;
        $array       = array_reverse($array, true);

        return $array;
    }


    /**
     * Get one field from array of arrays (array of objects)
     * @param array $options
     * @param string $fieldName
     * @return array
     */
    public function getField($options, $fieldName = 'id')
    {
        $result = array();

        if (!empty($options) && is_array($options)) {
            foreach ($options as $option) {
                if (is_array($option)) {
                    $result[] = $option[$fieldName];

                } else if (is_object($option)) {
                    $result[] = $option->$fieldName;
                }
            }
        }

        return $result;
    }

    /**
     * Recursive array mapping
     * @param Closure $function
     * @param array $array
     * @return array
     */
    public function mapRecursive($function, $array)
    {
        $resArray = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $resArray[$key] = $this->mapRecursive($function, $value);
            } else {
                $resArray[$key] = call_user_func($function, $value);
            }
        }

        return $resArray;
    }

    /**
     * @param $array
     * @param $orderArray
     * @return array
     */
    public function sortByArray(array $array, array $orderArray)
    {
        $ordered = array();

        foreach ($orderArray as $key) {

            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
            }

        }

        return $ordered + $array;
    }

    /**
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public function addToEachKey(array $array, $prefix)
    {
        $result = array();
        foreach ($array as $key => $item) {
            $result[$prefix . $key] = $item;
        }

        return $result;
    }

    /**
     * @param $needle
     * @param $haystack
     * @return bool|int|string
     */
    public function recursiveSearch($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            $currentKey = $key;

            if ($needle === $value OR (is_array($value) && $this->recursiveSearch($needle, $value) !== false)) {
                return $currentKey;
            }
        }

        return false;
    }

    /**
     * Convert assoc array to comment style
     * @param array $data
     * @return string
     */
    public function toFormatedString(array $data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result[] = $key . ": \t " . $value;
        }

        return implode("\n", $result);
    }

}
