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
 * Class JBTablesHelper
 */
class JBTablesHelper extends AppHelper
{
    // IMPORTANT! not more then 63
    const INDEX_LIMIT = '50';

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
     * @return $this
     */
    public function dropTable($tableName)
    {
        $this->_query('DROP TABLE IF EXISTS `' . $tableName . '`');

        return $this;
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
        $tableName = trim(str_replace('#__', $config->dbprefix, $tableName));

        return in_array($tableName, $tableList, true);
    }

    /**
     * Check and create favorite table
     * @param bool $force
     * @return $this
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

        return $this;
    }

    /**
     * Check and create SKU table
     * @param bool $force
     * @return $this
     */
    public function checkSku($force = false)
    {
        static $checked;

        if (!isset($checked) || $force) {
            $this->createTable(ZOO_TABLE_JBZOO_SKU, array(
                //'`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT', // bug with prevNext + category sorting
                '`item_id` INT(11) UNSIGNED NOT NULL',
                '`element_id` VARCHAR(36) NOT NULL',
                '`param_id` VARCHAR(36) NOT NULL',
                '`value_s` VARCHAR(150) NOT NULL COLLATE \'utf8_general_ci\'',
                '`value_n` DOUBLE NOT NULL',
                '`value_d` DATETIME',
                '`variant` INT(11) NOT NULL'
            ), array(
                //'PRIMARY KEY (`id`)',
                'INDEX `item_id` (`item_id`)',
                'INDEX `element_id` (`element_id`)',
                'INDEX `param_id` (`param_id`)',
                'INDEX `value_s` (`value_s`)',
                'INDEX `value_n` (`value_n`)',
                'INDEX `value_d` (`value_d`)',
                'INDEX `variant` (`variant`)'
            ));
        }

        $checked = true;

        return $this;
    }

    /**
     * Check and create config table
     * @param bool $checkStructure
     * @return $this
     */
    public function checkConfig($checkStructure = false)
    {
        static $checked;

        $groupLength = 150;
        $keyLength   = 100;
        $tableName   = ZOO_TABLE_JBZOO_CONFIG;

        if (!isset($checked)) {

            $this->createTable($tableName, array(
                //'`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
                '`group` VARCHAR(' . $groupLength . ') NULL DEFAULT NULL',
                '`key` VARCHAR(' . $keyLength . ') NULL DEFAULT NULL',
                '`value` TEXT NULL',
                '`type` VARCHAR(10) NULL DEFAULT \'string\''
            ), array(
                //'PRIMARY KEY (`id`)',
                'UNIQUE INDEX `group_key` (`group`, `key`)',
                'INDEX `group` (`group`)',
                'INDEX `key` (`key`)'
            ));
            $checked = true;
        }

        if ($checkStructure && $fields = $this->getTableInfo($tableName)) {

            if (isset($fields['id'])) { // remove old field
                $sql = 'ALTER TABLE `' . $tableName . '` DROP COLUMN `id`;';
                $this->_query($sql);
            }

            // check group index length
            if (isset($fields['group']) && strtolower($fields['group']['Type']) != 'varchar(' . $groupLength . ')') {
                $sql = 'ALTER TABLE `' . $tableName . '` CHANGE COLUMN `group` `group` VARCHAR(' . $groupLength . ') NULL DEFAULT NULL FIRST;';
                $this->_query($sql);
            }

            // check key index length
            if (isset($fields['key']) && strtolower($fields['key']['Type']) != 'varchar(' . $keyLength . ')') {
                $sql = 'ALTER TABLE `' . $tableName . '` CHANGE COLUMN `key` `key` VARCHAR(' . $keyLength . ') NULL DEFAULT NULL AFTER `group`;';
                $this->_query($sql);
            }
        }

        return $this;
    }

    /**
     * Check and create favorite table
     * @param bool $force
     * @return $this
     */
    public function checkOrder($force = false)
    {
        static $checked;

        if (!isset($checked) || $force) {

            $this->createTable(ZOO_TABLE_JBZOO_ORDER, array(
                '`id` INT(11) NOT NULL AUTO_INCREMENT',
                '`status` VARCHAR(100) NULL DEFAULT \'0\'',
                '`status_payment` VARCHAR(100) NULL DEFAULT \'0\'',
                '`status_shipping` VARCHAR(100) NULL DEFAULT \'0\'',
                '`created` DATETIME NULL DEFAULT NULL',
                '`created_by` INT(11) NULL DEFAULT NULL',
                '`modified` DATETIME NULL DEFAULT NULL',
                '`total` FLOAT NULL DEFAULT NULL',
                '`items` TEXT NULL',
                '`fields` TEXT NULL',
                '`shipping` TEXT NULL',
                '`shippingfields` TEXT NULL',
                '`modifiers` TEXT NULL',
                '`payment` TEXT NULL',
                '`params` TEXT NULL',
                '`comment` TEXT NULL',
            ), array(
                'PRIMARY KEY (`id`)',
                'INDEX `status` (`status`)',
                'INDEX `status_payment` (`status_payment`)',
                'INDEX `status_shipping` (`status_shipping`)',
                'INDEX `created` (`created`)',
                'INDEX `created_by` (`created_by`)',
                'INDEX `modified` (`modified`)',
                'INDEX `total` (`total`)',
                'FULLTEXT INDEX `comment` (`comment`)',
            ));
        }

        $checked = true;

        return $this;
    }

    /**
     * Drop & create indexes table
     * @return $this
     */
    public function createIndexes()
    {
        $types = $this->app->jbtype->getSimpleList();

        if (!empty($types)) {
            foreach ($types as $type => $typeName) {
                $this->createIndexTable($type);
            }
        }

        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function createIndexTable($type)
    {
        if (empty($type)) {
            return $this;
        }

        $props     = $this->_getTableProps($type);
        $tableName = $this->getIndexTable($type);

        return $this
            ->dropTable($tableName)
            ->createTable($tableName, $props['fields'], $props['indexes']);
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
     * @param       $tableName
     * @param array $tblFields
     * @param array $tblIndex
     * @return $this
     */
    public function createTable($tableName, array $tblFields, array $tblIndex)
    {
        $params = array_merge($tblFields, $tblIndex);

        if (empty($params)) {
            return $this;
        }

        $sql   = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '`';
        $sql[] = '(' . implode(",\n ", $params) . ')';
        $sql[] = 'COLLATE=\'utf8_general_ci\' ENGINE=MyISAM;';

        $sqlString = implode(' ', $sql);

        $this->_db->query($sqlString);

        return $this;
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
            '_itemname'      => array('type' => '_itemname'),
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

            // add fields
            $tblFields[] = '`' . $this->getFieldName($field, 's') . '` VARCHAR(50) NULL DEFAULT NULL COLLATE \'utf8_general_ci\'';
            $tblFields[] = '`' . $this->getFieldName($field, 'n') . '` DOUBLE NOT NULL DEFAULT \'0\'';
            $tblFields[] = '`' . $this->getFieldName($field, 'd') . '` DATETIME NULL DEFAULT NULL';

            // add indexes
            if (count($tblIndex) < self::INDEX_LIMIT) {
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
        self::$_elementsBeforeSave = array();

        if ($itemType->id) {
            $elements = $this->_getCurrentTypeElements($itemType->id);
            $elements = is_null($elements) ? array() : $elements;

            self::$_elementsBeforeSave = array_keys($elements);
        }
    }

    /**
     * @param Type $itemType
     */
    public function checkTypeAfterSave($itemType)
    {
        if (empty($itemType->id)) {
            return;
        }

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
     * @param       $tableName
     * @param array $fields
     */
    public function _removeFields($tableName, array $fields)
    {
        if (empty($fields)) {
            return;
        }

        $types          = array('s', 'n', 'd');
        $currentIndexes = $this->getIndexes($tableName);
        $currentFields  = $this->getFields($tableName, true);

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
            $this->_query($sql);
        }
    }

    /**
     * Add indexes and fields to table
     * @param       $tableName
     * @param array $fields
     * @param       $itemType
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
        $currentFields  = $this->getFields($tableName, true);

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
                        $add[] = 'ADD COLUMN `' . $filedName . '` DOUBLE NOT NULL DEFAULT \'0\'';
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
            $this->_query($sql);
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
    public function getFields($table, $noCache = false)
    {
        static $result;

        if (!isset($result)) {
            $result = array();
        }

        if ($noCache && isset($result[$table])) {
            unset($result[$table]);
        }

        if (!isset($result[$table]) && $this->isTableExists($table, true)) {
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
     * @return $this
     */
    public function dropAllIndex()
    {
        $allTables = $this->getTableList();

        foreach ($allTables as $table) {
            if (preg_match('#_zoo_jbzoo_index#', $table)) {
                $this->dropTable($table);
            }
        }

        return $this;
    }

    /**
     * Drop all sku tables
     * @return $this
     */
    public function dropAllSku()
    {
        $this->dropTable(ZOO_TABLE_JBZOO_SKU);
        return $this;
    }

    /**
     * @param $table
     * @return null
     */
    public function getTableInfo($table)
    {

        if ($this->isTableExists($table, true)) {
            if ($fields = $this->app->database->queryAssocList('DESCRIBE ' . $table)) {

                $retult = array();
                foreach ($fields as $field) {
                    $retult[$field['Field']] = $field;
                }

                return $retult;
            }
        }

        return null;
    }

    /**
     * @param $sql
     * @return mixed
     */
    protected function _query($sql)
    {
        $result = $this->app->database->query((string)$sql);
        return $result;
    }
}
