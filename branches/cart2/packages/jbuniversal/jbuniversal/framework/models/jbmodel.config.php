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
 * Class JBModelConfig
 */
class JBModelConfig extends JBModel
{
    const TYPE_BOOL   = 'bool';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_DATA   = 'data';

    /**
     * @var JSONData
     */
    protected $_configs = null;

    /**
     * Create and return self instance
     * @return JBModelConfig
     */
    public static function model()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        $this->app->jbtables->checkConfig();
        $this->_init();
    }

    /**
     * @param        $key
     * @param null   $default
     * @param string $group
     * @return mixed
     */
    public function get($key, $default = null, $group = 'default')
    {
        $group = $this->_clean($group);
        $group = empty($group) ? 'default' : $group;
        $key   = $this->_clean($key);

        return $this->_configs->get($group . '.' . $key, $default);
    }

    /**
     * @param string $origGroup
     * @param null   $default
     * @return JSONData
     */
    public function getGroup($origGroup, $default = null)
    {
        static $cache = array(); // try to speedup it!

        if (!isset($cache[$origGroup])) { // TODO experimental
            $group = $this->_clean($origGroup);
            $group = empty($group) ? 'default' : $group;
            $group = $group . '.';

            $result = array();
            foreach ($this->_configs as $key => $value) {
                if (strpos($key, $group) === 0) {
                    $result[str_replace($group, '', $key)] = $value;
                }
            }

            if (empty($result)) {
                $cache[$origGroup] = $this->app->data->create($default);
            } else {
                $cache[$origGroup] = $this->app->data->create($result);
            }
        }

        return $cache[$origGroup];
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $group
     * @param string $type
     * @return AppData
     */
    public function set($key, $value, $group = 'default', $type = null)
    {
        $group = $this->_clean($group);
        $group = empty($group) ? 'default' : $group;
        $key   = $this->_clean($key);
        $type  = empty($type) ? $this->_getType($value) : $type;

        // create sql replace query
        $sql = $this->_getSelect()
            ->replace(ZOO_TABLE_JBZOO_CONFIG)
            ->values('group', $group)
            ->values('key', $key)
            ->values('value', $this->_prepareValue($value, $type))
            ->values('type', $type);

        // insert new data
        $this->sqlQuery($sql);

        // update data in memory
        if ($type == self::TYPE_DATA) {
            $value = $this->app->data->create($value);
        }

        $this->_configs->set($group . '.' . $key, $value);
    }

    /**
     * @param $group
     * @param $values
     */
    public function setGroup($group, $values)
    {
        $values = (array)$values;

        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $this->set($key, $value, $group);
            }
        } else {
            $this->removeGroup($group);
        }
    }


    /**
     * Remove group from DB
     * @param string $group
     * @return bool
     */
    public function removeGroup($group)
    {
        $group = $this->_clean($group);
        if (empty($group)) {
            return false;
        }

        $sql = $this->_getSelect()
            ->delete(ZOO_TABLE_JBZOO_CONFIG)
            ->where('`group` = ?', $group);
        $this->sqlQuery($sql);

        $this->_init();

        return true;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->_configs;
    }

    /**
     * Clear price string
     * @param $value
     * @return mixed|string
     */
    public function clearNumberValue($value)
    {
        $value = (string)$value;
        $value = JString::trim($value);
        $value = preg_replace('#[^0-9\,\.\-\+]#ius', '', $value);

        if (preg_match('#^([\+\-]{0,1})([0-9\.\,]*)$#ius', $value, $matches)) {
            $value = str_replace(',', '.', $matches[2]);
            return $matches[1] . (float)$value;
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        static $currency;

        if (!isset($currency)) {
            $currency = $this->getGroup('cart.config')->get('default_currency', 'eur');
        }

        return $currency;
    }

    /**
     * Init all configs
     */
    protected function _init()
    {
        $select = $this->_getSelect()
            ->from(ZOO_TABLE_JBZOO_CONFIG)
            ->clear('select')
            ->select(array(
                'CONCAT_WS(".", `group`, `key`) as group_key',
                '`value`',
                '`type`',
            ))
            ->limit(1000);

        $rows = $this->fetchAll($select);

        $configs = array();
        foreach ($rows as $row) {
            $configs[$row->group_key] = $this->_convertToType($row->value, $row->type);
        }

        $this->_configs = $this->app->data->create($configs);
    }

    /**
     * Convert data to type
     * @param        $value
     * @param string $type
     * @return bool|float
     */
    protected function _convertToType($value, $type = self::TYPE_STRING)
    {
        if ($type == self::TYPE_NUMBER) {
            return (float)$this->clearNumberValue($value);

        } else if ($type == self::TYPE_BOOL) {
            return (bool)trim($value);

        } else if ($type == self::TYPE_DATA) {
            return json_decode($value, true);
        }

        // default as string
        return $value;
    }

    /**
     * @param        $value
     * @param string $type
     * @return float|int|string
     */
    protected function _prepareValue($value, $type = self::TYPE_STRING)
    {
        if (empty($type)) {
            $type = $this->_getType($value);
        }

        if ($type == self::TYPE_NUMBER) {
            return (float)$this->app->jbmoney->clearValue($value);

        } else if ($type == self::TYPE_BOOL) {
            return (int)trim($value);

        } else if ($type == self::TYPE_DATA) {
            $value = (array)$value;
            $data  = $this->app->data->create($value);

            return $data->__toString();
        }

        // default as string
        return (string)$value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function _getType($value)
    {
        if (is_array($value) || is_object($value)) {
            return self::TYPE_DATA;

        } else if (is_numeric($value)) {
            return self::TYPE_NUMBER;

        } else if (is_bool($value)) {
            return self::TYPE_BOOL;
        }

        return self::TYPE_STRING;
    }

    /**
     * @param $word
     * @return string
     */
    protected function _clean($word)
    {
        $word = trim($word);
        $word = trim($word, '.');
        $word = strtolower($word);

        return $word;
    }

}
