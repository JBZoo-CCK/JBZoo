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
 * Class JBTablesHelper
 */
class JBTablesHelper extends AppHelper
{
    /**
     * @var DatabaseHelper
     */
    protected $_db = null;

    /**
     * Hack for item save
     * @var array
     */
    static $_elementsBeforeSave = array();

    /**
     * Prefix for all index tables
     * @var string
     */
    protected $_indexTablePrefix = '#__zoo_jbzoo_index_';

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_db = $this->app->database;
    }

    /**
     * Get table list
     * @param bool $force
     * @return mixed
     */
    public function getTableList($force = false)
    {
        static $tableList;

        if (!isset($tableList) || $force) {
            $tableList = $this->app->database->queryResultArray('SHOW TABLES');
        }

        return $tableList;
    }

    /**
     * @param $tableName
     */
    public function dropTable($tableName)
    {
        $this->app->database->query('DROP TABLE IF EXISTS `' . $tableName . '`');
    }

    /**
     * Check is table cretaed in database
     * @param $tableName
     * @param $force bool
     * @return bool
     */
    public function isTableExists($tableName, $force = false)
    {
        $config = new JConfig();

        $tableList = $this->getTableList($force);
        $tableName = trim(strtolower(str_replace('#__', $config->dbprefix, $tableName)));

        return in_array($tableName, $tableList, true);
    }

    /**
     * Check and create favorite table
     * @param bool $force
     */
    public function checkFavorite($force = false)
    {
        static $checked;

        if (!isset($checked) || $force) {

            $this->createTable(ZOO_TABLE_JBZOO_FAVORITE, array(
                '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
                '`user_id` INT(11) NOT NULL',
                '`item_id` INT(11) NOT NULL',
                '`date` DATETIME NOT NULL'
            ), array(
                'PRIMARY KEY (`id`)',
                'UNIQUE KEY `user_id_item_id` (`user_id`,`item_id`)',
                'KEY `user_id` (`user_id`)'
            ));

        }

        $checked = true;
    }

    /**
     * Check and create SKU table
     * @param bool $force
     */
    public function checkSku($force = false)
    {
        static $checked;

        if (!isset($checked) || $force) {

            $this->createTable(ZOO_TABLE_JBZOO_SKU, array(
                '`item_id` INT(11) NOT NULL',
                '`element_id` VARCHAR(50) NOT NULL',
                '`sku` VARCHAR(100) NOT NULL',
                '`type` INT(11) NOT NULL',
                '`is_new` TINYINT(4) NOT NULL DEFAULT \'0\'',
                '`is_hit` TINYINT(4) NOT NULL DEFAULT \'0\'',
                '`is_sale` TINYINT(4) NOT NULL DEFAULT \'0\'',
                '`price` FLOAT NOT NULL',
                '`total` FLOAT NOT NULL',
                '`currency` VARCHAR(20) NOT NULL',
                '`balance` INT(11) NOT NULL DEFAULT \'0\'',
                '`hash` VARCHAR(150) NULL DEFAULT NULL',
                '`params` TEXT NULL'
            ), array(
                'INDEX `hash` (`hash`)',
                'INDEX `item_id` (`item_id`)',
                'INDEX `type` (`type`)',
                'INDEX `element_id` (`element_id`)',
                'INDEX `price` (`price`)',
                'INDEX `total` (`total`)',
                'INDEX `sku` (`sku`)',
                'INDEX `is_new` (`is_new`)',
                'INDEX `is_hit` (`is_hit`)',
                'INDEX `is_sale` (`is_sale`)'
            ));
        }

        $checked = true;
    }

    /**
     * Check and create config table
     */
    public function checkConfig()
    {
        static $checked;

        if (!isset($checked)) {

            $this->createTable(ZOO_TABLE_JBZOO_CONFIG, array(
                '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
                '`group` VARCHAR(50) NULL DEFAULT NULL',
                '`key` VARCHAR(50) NULL DEFAULT NULL',
                '`value` TEXT NULL',
                '`type` VARCHAR(10) NULL DEFAULT \'string\''
            ), array(
                'PRIMARY KEY (`id`)',
                'UNIQUE INDEX `group_key` (`group`, `key`)',
                'INDEX `group` (`group`)',
                'INDEX `key` (`key`)'
            ));
        }

        $checked = true;
    }

    /**
     * Drop & create indexes table
     */
    public function createIndexes()
    {
        $types = $this->app->jbtype->getSimpleList();

        if (!empty($types)) {
            foreach ($types as $type => $typeName) {
                $this->createIndexTable($type);
            }
        }
    }

    /**
     * @param $type
     */
    public function createIndexTable($type)
    {
        if (empty($type)) {
            return;
        }

        $props = $this->_getTableProps($type);

        $tableName = $this->getIndexTable($type);
        $this->dropTable($tableName);

        $this->createTable($tableName, $props['fields'], $props['indexes']);
    }

    /**
     * Get name of index table
     * @param string $itemType
     * @return string
     */
    public function getIndexTable($itemType)
    {
        $itemType = str_replace('-', '_', $itemType);
        $itemType = strtolower(preg_replace('#[^0-9a-z\_]#ius', '', $itemType));
        $itemType = $this->_indexTablePrefix . $itemType;

        if (strlen($itemType) > 50) {
            $itemType = substr($itemType, 0, 50);
        }

        return $itemType;
    }

    /**
     * Get field name
     * @param $systemName
     * @param $fieldName
     * @return mixed
     */
    public function getFieldName($systemName, $fieldName = 's')
    {
        return 'e_' . strtolower(preg_replace('#[^0-9a-z]#ius', '', $systemName)) . '_' . $fieldName;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    public function _sortIndexKeys($a, $b)
    {
        if (strpos($a, '_') === 0) {
            return -1;
        } else if (strpos($b, '_') === 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param $tableName
     * @param array $tblFields
     * @param array $tblIndex
     */
    public function createTable($tableName, array $tblFields, array $tblIndex)
    {
        $params = array_merge($tblFields, $tblIndex);

        if (empty($params)) {
            return;
        }

        $sql   = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '`';
        $sql[] = '(' . implode(",\n ", $params) . ')';
        $sql[] = 'COLLATE=\'utf8_general_ci\' ENGINE=MyISAM;';

        $sqlString = implode(' ', $sql);

        $this->_db->query($sqlString);
    }

    /**
     * @param $type
     * @return array
     */
    protected function _getTableProps($type)
    {
        $elements = $this->_getCurrentTypeElements($type);

        $indexModel         = JBModelSearchindex::model();
        $stdIndexFields     = $indexModel->getStdIndexFields();
        $excludedIndexTypes = $indexModel->getExcludeTypes();

        if (empty($elements) || !is_array($elements)) {
            $elements = array();
        }

        $elements = array_merge(array(
            '_itemcategory'  => array('type' => '_itemcategory'),
            '_itemfrontpage' => array('type' => '_itemfrontpage'),
            '_itemtag'       => array('type' => '_itemtag'),
        ), $elements);

        uksort($elements, array('JBTablesHelper', '_sortIndexKeys'));

        $fields = array();
        foreach ($elements as $elementId => $element) {
            if (strpos($elementId, '_') === 0 && !in_array($elementId, $stdIndexFields, true)) {
                continue;
            }

            if (in_array($element['type'], $excludedIndexTypes, true)) {
                continue;
            }

            $fields[] = $elementId;
        }

        // std fileds and indexes
        $tblFields = array('`item_id` INT(11) UNSIGNED NOT NULL');
        $tblIndex  = array('INDEX `item_id` (`item_id`)');

        foreach ($fields as $field) {

            // only for frontpage mark (for perfomance optimization)
            if ($field == '_itemfrontpage') {
                $tblFields[] = '`' . $this->getFieldName($field, 'n') . '` TINYINT(1) NULL DEFAULT \'0\'';
                $tblIndex[]  = 'INDEX `' . $this->getFieldName($field, 'n') . '` (`' . $this->getFieldName($field, 'n') . '`)';
                continue;
            }

            // add fields
            $tblFields[] = '`' . $this->getFieldName($field, 's') . '` VARCHAR(50) NULL DEFAULT NULL COLLATE \'utf8_general_ci\'';
            $tblFields[] = '`' . $this->getFieldName($field, 'n') . '` DOUBLE NULL DEFAULT NULL';
            $tblFields[] = '`' . $this->getFieldName($field, 'd') . '` DATETIME NULL DEFAULT NULL';

            // add indexes
            if (count($tblIndex) < 63) {
                $tblIndex[] = 'INDEX `' . $this->getFieldName($field, 's') . '` (`' . $this->getFieldName($field, 's') . '`)';
                $tblIndex[] = 'INDEX `' . $this->getFieldName($field, 'n') . '` (`' . $this->getFieldName($field, 'n') . '`)';
                if (!in_array($field, $stdIndexFields, true)) {
                    $tblIndex[] = 'INDEX `' . $this->getFieldName($field, 'd') . '` (`' . $this->getFieldName($field, 'd') . '`)';
                }
            }
        }

        return array('fields' => $tblFields, 'indexes' => $tblIndex);
    }

    /**
     * @param Type $itemType
     */
    public function checkTypeBeforeSave($itemType)
    {
        $elements = $this->_getCurrentTypeElements($itemType->id);
        $elements = is_null($elements) ? array() : $elements;

        self::$_elementsBeforeSave = array_keys($elements);
    }

    /**
     * @param Type $itemType
     */
    public function checkTypeAfterSave($itemType)
    {
        $elements = $this->_getCurrentTypeElements($itemType->id);
        $elements = is_null($elements) ? array() : $elements;

        $elementsAfterSave = array_keys($elements);

        $tableName = $this->getIndexTable($itemType->id);
        if (!$this->isTableExists($tableName)) {
            $this->createIndexTable($itemType->id);
        }

        $removed = array_diff(self::$_elementsBeforeSave, $elementsAfterSave);
        if (!empty($removed)) {
            $this->_removeFields($tableName, $removed);
        }

        $added = array_diff($elementsAfterSave, self::$_elementsBeforeSave);
        if (!empty($added)) {
            $this->_addFields($tableName, $added, $itemType->id);
        }
    }


    /**
     * @param $type
     * @return array
     */
    public function _getCurrentTypeElements($type)
    {
        if ($path = $this->app->path->path('jbapp:types/' . $type . '.config')) {
            $data = json_decode($this->app->jbfile->read($path), true);

            return $data['elements'];
        }

        return null;
    }

    /**
     * Drop indexes and fields from table
     * @param $tableName
     * @param array $fields
     */
    public function _removeFields($tableName, array $fields)
    {
        if (empty($fields)) {
            return;
        }

        $types          = array('s', 'n', 'd');
        $currentIndexes = $this->getIndexes($tableName);
        $currentFields  = $this->getFields($tableName);

        $drop = array();
        foreach ($fields as $field) {

            foreach ($types as $type) {
                $filedName = $this->getFieldName($field, $type);

                if (in_array($filedName, $currentFields, true)) {
                    $drop[] = 'DROP COLUMN `' . $filedName . '`';
                }

                if (in_array($filedName, $currentIndexes, true)) {
                    $drop[] = 'DROP INDEX `' . $filedName . '`';
                }
            }
        }

        if (!empty($drop)) {
            $sql = 'ALTER TABLE `' . $tableName . "`\n " . implode(",\n ", $drop);
            $this->app->database->query($sql);
        }
    }

    /**
     * Add indexes and fields to table
     * @param $tableName
     * @param array $fields
     * @param $itemType
     */
    public function _addFields($tableName, array $fields, $itemType)
    {
        if (empty($fields)) {
            return;
        }

        $indexModel         = JBModelSearchindex::model();
        $stdIndexFields     = $indexModel->getStdIndexFields();
        $excludedIndexTypes = $indexModel->getExcludeTypes();
        $elements           = $this->_getCurrentTypeElements($itemType);

        $types          = array('s', 'n', 'd');
        $currentIndexes = $this->getIndexes($tableName);
        $currentFields  = $this->getFields($tableName);

        $indexCount = count($currentIndexes);

        $add = array();
        foreach ($fields as $field) {

            if ((strpos($field, '_') === 0 && !in_array($field, $stdIndexFields, true)) ||
                in_array($elements[$field]['type'], $excludedIndexTypes, true)
            ) {
                continue;
            }

            foreach ($types as $type) {


                $filedName = $this->getFieldName($field, $type);

                if (!in_array($filedName, $currentFields, true)) {

                    if ($type == 's') {
                        $add[] = 'ADD COLUMN `' . $filedName . '` VARCHAR(50) NULL DEFAULT NULL COLLATE \'utf8_general_ci\'';
                    }

                    if ($type == 'n') {
                        $add[] = 'ADD COLUMN `' . $filedName . '` DOUBLE NULL DEFAULT NULL';
                    }

                    if ($type == 'd') {
                        $add[] = 'ADD COLUMN `' . $filedName . '` DATETIME NULL DEFAULT NULL';
                    }

                }

                if (!in_array($filedName, $currentIndexes, true) && $indexCount < 63) {
                    $indexCount++;
                    $add[] = 'ADD INDEX `' . $filedName . '` (`' . $filedName . '`)';
                }
            }
        }

        if (!empty($add)) {
            $sql = 'ALTER TABLE `' . $tableName . "`\n " . implode(",\n ", $add);
            $this->app->database->query($sql);
        }
    }

    /**
     * @param $table
     * @return array
     */
    public function getIndexes($table)
    {
        static $result;

        if (!isset($result)) {
            $indexes = $this->app->database->queryAssocList('SHOW INDEX FROM ' . $table);

            $result = array();
            foreach ($indexes as $index) {
                $result[] = $index['Key_name'];
            }

            $result = array_unique($result);
        }

        return $result;
    }

    /**
     * @param $table
     * @return array
     */
    public function getFields($table)
    {
        static $result;

        if (!isset($result)) {
            $result = array();
        }

        if (!isset($result[$table])) {
            $indexes = $this->app->database->queryAssocList('DESCRIBE ' . $table);

            $result[$table] = array();
            if (!empty($indexes)) {
                foreach ($indexes as $index) {
                    $result[$table][] = $index['Field'];
                }
            }
        }

        return $result[$table];
    }

    /**
     * Drop all index tables
     */
    public function dropAllIndex()
    {
        $allTables = $this->getTableList();

        foreach ($allTables as $table) {
            if (preg_match('#_zoo_jbzoo_index#', $table)) {
                $this->dropTable($table);
            }
        }

    }
}
