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
 * Class JBModel
 */
Class JBModel
{
    /**
     * @var string
     */
    protected $_dbNull = null;

    /**
     * @var string
     */
    protected $_dbNow = null;

    /**
     * @var JDatabaseMySQLi
     */
    protected $_db = null;

    /**
     * @var DatabaseHelper
     */
    protected $_dbHelper = null;

    /**
     * @var JBTablesHelper
     */
    protected $_jbtables = null;

    /**
     * @var JBCacheHelper
     */
    protected $_jbcache = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->app = App::getInstance('zoo');

        $this->_db       = JFactory::getDbo();
        $this->_dbHelper = $this->app->database;
        $this->_jbtables = $this->app->jbtables;
        $this->_jbcache  = $this->app->jbcache;

        $this->_dbNow  = $this->_db->quote($this->app->date->create()->toSql());
        $this->_dbNull = $this->_db->quote($this->_db->getNullDate());
    }

    /**
     * Create and return self instance
     * @return JBModel
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Get database query object
     * @return JBDatabaseQuery
     */
    protected function _getSelect()
    {
        $select = new JBDatabaseQuery($this->_db);
        return $select;
    }

    /**
     * Fetch one row
     * @param JBDatabaseQuery $select
     * @param bool            $toArray
     * @return JObject
     */
    public function fetchRow(JBDatabaseQuery $select, $toArray = false)
    {
        return $this->_query($select, true, $toArray);
    }

    /**
     * Fetch all query result
     * @param JBDatabaseQuery $select
     * @param bool            $toArray
     * @return array|JObject
     */
    public function fetchAll(JBDatabaseQuery $select, $toArray = false)
    {
        return $this->_query($select, false, $toArray);
    }

    /**
     * @param JBDatabaseQuery $select
     * @return mixed
     */
    public function fetchList(JBDatabaseQuery $select)
    {
        $selectSql = (string)$select;
        $this->app->jbdebug->sql($selectSql);

        $this->_db->setQuery($selectSql);
        $rows   = $this->_db->loadRowList();
        $result = $this->_groupBy($rows, '0');

        return $result;
    }

    /**
     * Simple query to database
     * @param string $select
     * @return mixed
     */
    protected function sqlQuery($select)
    {
        return $this->_db->setQuery((string)$select)->execute();
    }

    /**
     * Query to database
     * @param JBDatabaseQuery $select
     * @param bool            $isOne
     * @param bool            $toArray
     * @return mixed
     */
    protected function _query(JBDatabaseQuery $select, $isOne = false, $toArray = false)
    {
        //jbdump::sql($select);

        $selectSql = (string)$select;
        $this->app->jbdebug->sql($selectSql);
        $this->_db->setQuery($selectSql);

        if (!$toArray) {
            if ((boolean)$isOne) {
                $result = $this->_db->loadObject();
            } else {
                $result = $this->_db->loadObjectList();
            }

        } else {

            if ((boolean)$isOne) {
                $result = $this->_db->loadAssoc();
            } else {
                $result = $this->_db->loadAssocList();
            }
        }

        return $result;
    }

    /**
     * Get database query object for item
     * @param null|string     $type
     * @param null|string|int $applicationId
     * @param bool            $isSearchable
     * @return JBDatabaseQuery
     */
    protected function _getItemSelect($type = null, $applicationId = null, $isSearchable = true)
    {
        $select = $this->_getSelect()
            ->select('tItem.*')
            ->from(ZOO_TABLE_ITEM . ' AS tItem')
            ->where('tItem.' . $this->app->user->getDBAccessString())
            ->where('tItem.state = ?', 1)
            ->where('(tItem.publish_up = ' . $this->_dbNull . ' OR tItem.publish_up <= ' . $this->_dbNow . ')')
            ->where('(tItem.publish_down = ' . $this->_dbNull . ' OR tItem.publish_down >= ' . $this->_dbNow . ')');

        if ($isSearchable) {
            $select->where('tItem.searchable = ?', 1);
        }

        if (is_array($type)) {
            $select->where('tItem.type IN ("' . implode('", "', $type) . '")');

        } elseif (is_string($type)) {
            $select->where('tItem.type = ?', $type);
        }

        if ((int)$applicationId) {
            $select->where('tItem.application_id = ?', (int)$applicationId);
        }

        return $select;
    }

    /**
     * Get zoo items by IDs
     * @param array  $ids
     * @param string $order
     * @return array
     */
    public function getZooItemsByIds($ids, $order = null)
    {
        $ids = array_filter($ids);
        if (empty($ids)) {
            return array();
        }

        $conditions = array(
            'id IN (' . implode(',', $ids) . ')',
        );

        $order  = $this->app->jborder->get($order);
        $result = $this->app->table->item->all(compact('conditions', 'order'));

        $this->app->jbdebug->mark('model::getZooItemsByIds');

        return $result;
    }

    /**
     * Set internal mysql value
     * TODO remove this hack
     */
    protected function _setBigSelects()
    {
        $this->_db->setQuery('SET SQL_BIG_SELECTS = 1')->execute();
    }

    /**
     * Group array by key
     * @param array  $rows
     * @param string $key
     * @return array
     */
    protected function _groupBy($rows, $key = 'id')
    {
        $result = array();

        if (!empty($rows)) {
            foreach ($rows as $row) {

                if (is_array($row)) {
                    $value = $row[$key];
                } else if (is_object($row)) {
                    $value = $row->$key;
                } else {
                    $value = $row;
                }

                $result[$value] = $value;
            }
        }

        return $result;
    }

    /**
     * Trancate table
     * @param $tableName
     * @return mixed
     */
    protected function trancate($tableName)
    {
        return $this->_dbHelper->query('TRUNCATE `' . $tableName . '`;');
    }

    /**
     * Quote string
     * @param $vars
     * @return string
     */
    protected function _quote($vars)
    {
        if (is_array($vars)) {
            foreach ($vars as $rowKey => $rowItem) {
                $vars[$rowKey] = $this->_quote($rowItem);
            }
        } else {
            $vars = $this->_db->quote($vars);
        }

        return $vars;
    }

    /**
     * Multi insert
     * @param array  $data
     * @param string $table
     * @return mixed
     */
    protected function _multiInsert($data, $table)
    {
        if (empty($data)) {
            return false;
        }

        $keys = array_keys(current($data));

        foreach ($keys as $num => $key) {
            $keys[$num] = '`' . $key . '`';
        }

        $valueTitles = '(' . implode(', ', $keys) . ")\n";

        $preValues = array();
        foreach ($data as $values) {
            foreach ($values as $key => $value) {
                $values[$key] = is_null($value) ? 'NULL' : $this->_quote($value);
            }

            $preValues[] = "(" . implode(", ", $values) . ")\n";
        }

        $insertedValues = implode(",\n", $preValues);

        $query = 'INSERT INTO ' . $table . ' ' . $valueTitles . ' VALUES ' . $insertedValues;

        return $this->_dbHelper->query($query);
    }

    /**
     * Insert data
     * @param $data
     * @param $table
     * @return mixed
     */
    protected function _insert($data, $table)
    {
        $result = $this->_multiInsert(array($data), $table);

        if ($result) {
            return $this->_db->insertid();
        }

        return 0;
    }

    /**
     * @param        $data
     * @param        $table
     * @param null   $keyId
     * @param string $keyField
     * @return bool|mixed
     */
    protected function _update($data, $table, $keyId = null, $keyField = 'id')
    {
        if (empty($data)) {
            return false;
        }

        $keyId = ($keyId) ? $keyId : $data['id'];

        if (isset($data[$keyField])) {
            unset($data[$keyField]);
        }

        $sql = $this->_getSelect()
            ->update($table)
            ->where($keyField . ' = ?', $keyId);

        foreach ($data as $key => $value) {
            $value = is_null($value) ? 'NULL' : $this->_quote($value);
            $sql->set('`' . $key . '` = ' . $value);
        }

        return $this->_dbHelper->query((string)$sql);
    }

    /**
     * Separate values by spaces
     * @param $value
     * @return array
     */
    protected function _separateValue($value)
    {
        $values = explode(' ', $value);

        foreach ($values as $key => $value) {

            $value = JString::trim($value);
            if (JString::strlen($value)) {
                $values[$key] = $value;
            } else {
                unset($values[$key]);
            }
        }

        return $values;
    }

    /**
     * Build where like conditions from strings with spaces
     * @param string $value
     * @param string $fieldName
     * @return string
     */
    protected function _buildLikeBySpaces($value, $fieldName)
    {
        $values = $this->_separateValue($value);

        foreach ($values as $key => $value) {
            $values[$key] = $this->_db->quote('%' . $value . '%');
        }

        return '(' . $fieldName . ' LIKE ' . implode(' AND ' . $fieldName . ' LIKE ', $values) . ' )';
    }

    /**
     * Render explain table
     * Function ported from Joomla debug plugin
     * @param JBDatabaseQuery $select
     * @return null|string
     */
    protected function _explain(JBDatabaseQuery $select)
    {
        if (!(class_exists('jbdump') || JDEBUG)) {
            return null;
        }

        $table = $this->app->database->queryAssocList('EXPLAIN ' . $select->__toString());

        if (!$table) {
            return null;
        }

        $html = array();

        $html[] = '<table class="table" style="width:1600px"><tr>';
        foreach (array_keys($table[0]) as $k) {
            $html[] = '<th>' . htmlspecialchars($k) . '</th>';
        }
        $html[] = '</tr>';

        foreach ($table as $tr) {
            $html[] = '<tr>';

            foreach ($tr as $k => $td) {
                if ($td === null) {
                    $td = 'NULL';
                }

                if ($k == 'Error') {
                    $html[] = '<td class="dbg-warning">' . htmlspecialchars($td);

                } elseif ($k == 'key') {
                    if ($td === 'NULL') {
                        $html[] = '<td><strong style="color:#f00;">NO_INDEX</strong>';
                    } else {
                        $html[] = '<td><strong>' . htmlspecialchars($td) . '</strong>';
                    }
                } elseif ($k == 'Extra') {
                    $htmlTd = htmlspecialchars($td);
                    $htmlTd = preg_replace('/([^;]) /', '\1&nbsp;', $htmlTd);

                    $htmlTdWithWarnings = str_replace(
                        'Using&nbsp;filesort',
                        '<strong style="color:#f00;">USE_FILESORT</strong>',
                        $htmlTd
                    );

                    $html[] = '<td>' . $htmlTdWithWarnings;

                } elseif ($k == 'possible_keys') {
                    $td     = str_replace(',', ",\n", $td);
                    $html[] = '<td>' . htmlspecialchars($td);

                } else {
                    $html[] = '<td>' . htmlspecialchars($td);
                }

                $html[] = '</td>';
            }

            $html[] = '</tr>';
        }

        $html[] = '</table>';

        $result = implode(PHP_EOL, $html);

        jbdump::sql($select);
        dump($result, 0, 'Explain::html');
    }

}
