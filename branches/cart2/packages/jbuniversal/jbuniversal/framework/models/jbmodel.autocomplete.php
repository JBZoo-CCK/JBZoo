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
 * Class JBModelAutoComplete
 */
Class JBModelAutoComplete extends JBModel
{

    /**
     * Create and return self instance
     * @return JBModelAutocomplete
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Autocomplete query
     * @param string $query
     * @param string $identifier
     * @param null|string $itemType
     * @param null|int $applicationId
     * @param int $limit
     * @return JObject
     */
    public function field($query, $identifier, $itemType = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $identifier = $this->_jbtables->getFieldName($identifier, 's');
        $tableName  = $this->_jbtables->getIndexTable($itemType);

        $select = $this->_getItemSelect($itemType, $applicationId)
            ->innerJoin($tableName . ' AS tIndex ON tIndex.item_id = tItem.id')
            ->clear('select')

            ->select('tIndex.' . $identifier . ' AS value')

            ->where($this->_buildLikeBySpaces($query, 'tIndex.' . $identifier . ''))
            ->where('tIndex.' . $identifier . ' <> ""')
            ->where('tIndex.' . $identifier . ' IS NOT NULL')
            ->where('tIndex.' . $identifier . ' <> ?', JBModelElementJBImage::IMAGE_EXISTS)
            ->where('tIndex.' . $identifier . ' <> ?', JBModelElementJBImage::IMAGE_NO_EXISTS)
            ->group('tIndex.' . $identifier)
            ->order('tIndex.' . $identifier . ' ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * Autocomplete by item name
     * @param string $query
     * @param null|string $type
     * @param null|string $applicationId
     * @param int $limit
     * @return null|array
     */
    public function name($query, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this->_getItemSelect($type, $applicationId)
            ->clear('select')
            ->select(array('tItem.name AS value', 'tItem.id'))
            ->where($this->_buildLikeBySpaces($query, 'tItem.name'))
            ->group('tItem.name')
            ->order('tItem.name')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * Autocomplete by item tags
     * @param string $query
     * @param null|string $type
     * @param null|string $applicationId
     * @param int $limit
     * @return null|array
     */
    public function tag($query, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this->_getItemSelect($type, $applicationId)
            ->clear('select')
            ->select(array('tTag.name AS value'))
            ->innerJoin(ZOO_TABLE_TAG . ' AS tTag ON tTag.item_id = tItem.id')
            ->where($this->_buildLikeBySpaces($query, 'tTag.name'))
            ->group('tTag.name')
            ->order('tTag.name ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * Autocomplete for item
     * @param $query
     * @param $element
     * @param null $type
     * @param null $applicationId
     * @param int $limit
     * @return array
     */
    public function textarea($query, $element, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this->_getItemSelect($type, $applicationId)
            ->clear('select')
            ->select(array('tZooIndex.value AS value'))
            ->innerJoin(ZOO_TABLE_SEARCH . ' AS tZooIndex ON tZooIndex.item_id = tItem.id')
            ->where($this->_buildLikeBySpaces($query, 'tZooIndex.value'))
            ->where('tZooIndex.element_id = ?', $element)
            ->group('tZooIndex.value')
            ->order('tZooIndex.value ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * Autocomplete by authors
     * @param string $query
     * @param null|string $type
     * @param null|string $applicationId
     * @param int $limit
     * @return null|array
     */
    public function author($query, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this->_getSelect()
            ->select(array('tUsers.name AS value'))
            ->from(ZOO_TABLE_ITEM . ' AS tItem')
            ->innerJoin('#__users AS tUsers ON tUsers.id = tItem.created_by')
            ->where('tItem.application_id = ?', (int)$applicationId)
            ->where('tItem.type = ?', $type)
            ->where($this->_buildLikeBySpaces($query, 'tUsers.name'))
            ->group('tUsers.name')
            ->order('tUsers.name ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * AutoComplete query for item SKU
     * @param string      $query
     * @param string      $element_id
     * @param string      $param_id
     * @param null|string $type
     * @param null|int    $applicationId
     * @param int         $limit
     * @return JObject
     */
    public function priceElement($query, $element_id, $param_id, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this
            ->_getSelect()
            ->clear()
            ->select('tSku.value_s as value, tSku.value_s as value, tSku.value_s as id')
            ->from(ZOO_TABLE_JBZOO_SKU  . ' AS tSku')
            ->innerJoin(ZOO_TABLE_ITEM . ' AS tItem ON tSku.item_id = tItem.id')
            ->where('tItem.application_id = ?', (int)$applicationId)
            ->where('tItem.type = ?', $type)
            ->where($this->_buildLikeBySpaces($query, 'tSku.value_s'))
            ->where('tSku.element_id = ?', $element_id)
            ->where('tSku.param_id = ?', $param_id)
            ->group('tSku.value_s')
            ->order('tSku.value_s ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }

    /**
     * @param $query
     * @param null $type
     * @param null $applicationId
     * @param int $limit
     * @return array|JObject
     */
    public function comments($query, $type = null, $applicationId = null, $limit = 10)
    {
        if (empty($query)) {
            return array();
        }

        $select = $this->_getSelect()
            ->select(array('content AS value'))
            ->from(ZOO_TABLE_COMMENT . '  AS tComm')
            ->innerJoin(ZOO_TABLE_ITEM . ' AS tItem ON tItem.id = tComm.item_id')
            ->where('tItem.application_id = ?', (int)$applicationId)
            ->where('tItem.type = ?', $type)
            ->where($this->_buildLikeBySpaces($query, 'tComm.content'))
            ->group('tComm.item_id')
            ->order('tComm.item_id ASC')
            ->limit($limit);

        return $this->fetchAll($select);
    }
}
