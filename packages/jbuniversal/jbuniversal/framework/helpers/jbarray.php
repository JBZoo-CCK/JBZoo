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
     * @param array  $array
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
     * @param array  $array
     * @param string $key
     * @param mixed  $val
     * @return array
     */
    public function unshiftAssoc(array $array, $key, $val)
    {
        $array       = array_reverse($array, true);
        $array[$key] = $val;
        $array       = array_reverse($array, true);

        return $array;
    }


    /**
     * Get one field from array of arrays (array of objects)
     * @param array  $options
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
     * Returns the values of a specified column in an array.
     * The input array should be multidimensional or an array of objects.
     *
     * For example,
     *
     * ~~~
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = JBArrayHelper::getColumn($array, 'id');
     * // the result is: ['123', '345']
     *
     * // using anonymous function
     * $result = ArrayHelper::getColumn($array, function ($element) {
     *     return $element['id'];
     * });
     * ~~~
     *
     * @param array $array
     * @param string|\Closure $name
     * @param boolean $keepKeys whether to maintain the array keys. If false, the resulting array
     * will be re-indexed with integers.
     * @return array the list of column values
     */
    public function getColumn($array, $name, $keepKeys = true)
    {
        $result = array();
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = $this->getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = $this->getValue($element, $name);
            }
        }

        return $result;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = JBArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = JBArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = JBArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = JBArrayHelper::getValue($users, 'address.street');
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure $key key name of the array element, or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     */
    public function getValue($array, $key, $default = null)
    {
        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = $this->getValue($array, substr($key, 0, $pos), $default);
            $key   = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    /**
     * Indexes an array according to a specified key.
     * The input array should be multidimensional or an array of objects.
     *
     * The key can be a key name of the sub-array, a property name of object, or an anonymous
     * function which returns the key value given an array element.
     *
     * If a key value is null, the corresponding array element will be discarded and not put in the result.
     *
     * For example,
     *
     * ~~~
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = JBArrayHelper::index($array, 'id');
     * // the result is:
     * // [
     * //     '123' => ['id' => '123', 'data' => 'abc'],
     * //     '345' => ['id' => '345', 'data' => 'def'],
     * // ]
     *
     * // using anonymous function
     * $result = JBArrayHelper::index($array, function ($element) {
     *     return $element['id'];
     * });
     * ~~~
     *
     * @param array $array the array that needs to be indexed
     * @param string|\Closure $key the column name or anonymous function whose result will be used to index the array
     * @return array the indexed array
     */
    public function index($array, $key)
    {
        $result = array();
        foreach ($array as $element) {
            $value          = $this->getValue($element, $key);
            $result[$value] = $element;
        }

        return $result;
    }

    /**
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     * Optionally, one can further group the map according to a grouping field `$group`.
     *
     * For example,
     *
     * ~~~
     * $array = [
     *     ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
     *     ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
     *     ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
     * ];
     *
     * $result = JBArrayHelper::map($array, 'id', 'name');
     * // the result is:
     * // [
     * //     '123' => 'aaa',
     * //     '124' => 'bbb',
     * //     '345' => 'ccc',
     * // ]
     *
     * $result = JBArrayHelper::map($array, 'id', 'name', 'class');
     * // the result is:
     * // [
     * //     'x' => [
     * //         '123' => 'aaa',
     * //         '124' => 'bbb',
     * //     ],
     * //     'y' => [
     * //         '345' => 'ccc',
     * //     ],
     * // ]
     * ~~~
     *
     * @param array $array
     * @param string|\Closure $from
     * @param string|\Closure $to
     * @param string|\Closure $group
     * @return array
     */
    public function map($array, $from, $to, $group = null)
    {
        $result = array();
        foreach ($array as $element) {
            $key   = $this->getValue($element, $from);
            $value = $this->getValue($element, $to);
            if ($group !== null) {
                $result[$this->getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Recursive array mapping
     * @param Closure $function
     * @param array   $array
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
     * @param array  $array
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

        return implode(PHP_EOL, $result);
    }

    /**
     * @param $haystack
     * @return mixed
     */
    public function cleanJson($haystack)
    {
        $haystack = (array)$haystack;

        foreach ($haystack as $key => $value) {

            if (is_array($value)) {
                $haystack[$key] = $this->cleanJson($haystack[$key]);
            }

            if ($haystack[$key] === '' || is_null($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

}
