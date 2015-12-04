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
 * Class JBModelFilter
 */
class JBModelFilter extends JBModel
{

    /**
     * Create and return self instance
     * @return JBModelFilter
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Filter search
     * @param array  $elements
     * @param string $logic
     * @param bool   $type
     * @param int    $appId
     * @param bool   $exact
     * @param int    $offset
     * @param int    $limit
     * @param array  $order
     * @return array|JObject
     */
    public function search(
        $elements = array(),
        $logic = 'AND',
        $type = false,
        $appId = 0,
        $exact = false,
        $offset = 0,
        $limit = 20,
        $order = array()
    )
    {
        $this->app->jbdebug->mark('filter::model::filter-start');

        $cacheHash  = md5(serialize(func_get_args()));
        $cacheGroup = 'filter';
        $result     = $this->app->jbcache->get($cacheHash, $cacheGroup);

        $this->_setBigSelects();

        if (empty($result)) {
            // get seach select
            $select = $this->_getSearchSelect($elements, $logic, $type, $appId, $exact);

            // search page conditions
            $select->limit($limit, $offset);
            $this->_addOrder($select, $order, $type);

            //jbdump::sql($select);
            //$this->_explain($select);

            // query
            $rows   = $this->fetchAll($select, true);
            $result = $this->_groupBy($rows, 'id');

            // save to cache
            $this->app->jbcache->set($cacheHash, $result, $cacheGroup);
        }

        $this->app->jbdebug->mark('filter::model::filter-request');

        $items = $this->getZooItemsByIds($result);
        $items = $this->app->jbarray->sortByArray($items, $result);

        $this->app->jbdebug->mark('filter::model::filter-finish');

        return $items;
    }

    /**
     * Get count for pagination
     * @param array  $elements
     * @param string $logic
     * @param bool   $type
     * @param int    $appId
     * @param bool   $exact
     * @return mixed
     */
    public function searchCount(
        $elements = array(),
        $logic = 'AND',
        $type = false,
        $appId = 0,
        $exact = false
    )
    {
        $this->app->jbdebug->mark('filter::model::searchCount-start');

        $cacheHash = md5(serialize(func_get_args()));
        $cacheKey  = 'filter-count';
        $result    = $this->app->jbcache->get($cacheHash, $cacheKey);

        if (empty($result)) {

            $select = $this->_getSearchSelect($elements, $logic, $type, $appId, $exact);
            $select
                ->clear('select')
                ->select('count(DISTINCT tItem.id) as count');

            $result = $this->fetchRow($select)->count;

            $this->app->jbcache->set($cacheHash, $result, $cacheKey);
        }

        $this->app->jbdebug->mark('filter::model::searchCount-finish');

        return (int)$result;
    }

    /**
     * Create sql query for search items in database
     * @param array  $elements
     * @param string $logic
     * @param bool   $itemType
     * @param int    $appId
     * @param bool   $exact
     * @return JBDatabaseQuery
     */
    protected function _getSearchSelect(
        $elements = array(),
        $logic = 'AND',
        $itemType = false,
        $appId = 0,
        $exact = false
    )
    {
        $logic     = strtoupper(trim($logic));
        $tableName = $this->_jbtables->getIndexTable($itemType);

        $select = $this->_getItemSelect($itemType, $appId)
            ->clear('select')
            ->select('DISTINCT tItem.id as id')
            ->leftJoin($tableName . ' AS tIndex ON tIndex.item_id = tItem.id');

        $where = array();

        $tableZooIndexIncluded = false;

        if (count($elements) > 0) {
            $i = 0;

            foreach ($elements as $elementId => $value) {
                $i++;

                $isRange = $this->_isRange($value);

                $elementSearch = $this->app->jbentity->getElementModel($elementId, $itemType, $appId, $isRange);

                if (empty($elementSearch)) {
                    continue;
                }

                // check excluded user types
                if (in_array($elementSearch->getElementType(), array('textarea'))) {

                    if (!$tableZooIndexIncluded) {
                        $select->innerJoin(ZOO_TABLE_SEARCH . ' AS tZooIndex ON tZooIndex.item_id = tItem.id');
                    }
                }

                if ($logic == 'AND') {
                    $tmpConds = $elementSearch->conditionAND($select, $elementId, $value, $i, $exact);

                    if (is_array($tmpConds)) {
                        $where = array_merge($tmpConds, $where);
                    }

                } else {
                    $tmpConds = $elementSearch->conditionOR($select, $elementId, $value, $i, $exact);

                    if (is_array($tmpConds)) {
                        $where = array_merge($tmpConds, $where);
                    }
                }

            }

            $where = array_filter($where);

            if (!empty($where)) {

                if ($logic == 'AND') {
                    $select->where('(' . implode(' AND ', $where) . ')');
                } else {
                    $select->where('(' . implode(' OR ', $where) . ')');
                }
            }

        }

        return $select;
    }

    /**
     * Add order to query
     * @param JBDatabaseQuery $select
     * @param array           $order
     * @param string          $itemType
     */
    protected function _addOrder(JBDatabaseQuery $select, $order, $itemType)
    {
        $select->order('tItem.priority DESC');

        if (!empty($order) && is_array($order)) {

            $orders = $order;
            if (isset($order['field'])) {
                $orders = array($order);
            }

            foreach ($orders as $order) {

                $order = $this->app->data->create($order);

                $reverse    = $order->get('reverse');
                $orderParam = $order->get('order');

                if (!empty($reverse)) {

                    if (is_array($reverse) && isset($reverse[0])) {
                        $dir = $reverse[0] == 1 ? 'DESC' : 'ASC';
                    } else {
                        $dir = $reverse == 1 ? 'DESC' : 'ASC';
                    }

                } else if (!empty($orderParam)) {
                    $orderParam = trim(strtoupper($orderParam));
                    $dir        = $orderParam == 'DESC' ? 'DESC' : 'ASC';
                } else {
                    $dir = 'ASC';
                }

                if (!in_array($order->get('mode'), array('s', 'n', 'd'), true)) {
                    $order->set('mode', 's');
                }

                $field = $order->get('field');
                if ($field == 'corename') {
                    $select->order('tItem.name ' . $dir);

                } else if ($field == 'corealias') {
                    $select->order('tItem.alias ' . $dir);

                } else if ($field == 'corepriority') {
                    $select->order('tItem.priority ' . $dir);

                } else if ($field == 'corecreated') {
                    $select->order('tItem.created ' . $dir);

                } else if ($field == 'corehits') {
                    $select->order('tItem.hits ' . $dir);

                } else if ($field == 'coremodified') {
                    $select->order('tItem.modified ' . $dir);

                } else if ($field == 'corepublish_down') {
                    $select->order('tItem.publish_down ' . $dir);

                } else if ($field == 'corepublish_up') {
                    $select->order('tItem.publish_up ' . $dir);

                } else if ($field == 'coreauthor') {
                    $select
                        ->leftJoin('#__users AS tJoomlaUsers ON tItem.created_by = tJoomlaUsers.id')
                        ->order('tJoomlaUsers.name ' . $dir);

                } else if ($field == 'random') {
                    $select->order('RAND()');

                } elseif (strpos($field, '__')) {
                    list ($elementId, $priceId) = explode('__', $field);

                    if (strpos($priceId, '_') === 0 && !in_array($priceId, array('_value', '_sku'))) {
                        continue;
                    }

                    $select
                        ->leftJoin(ZOO_TABLE_JBZOO_SKU . '  AS tSku ON tSku.item_id = tItem.id')
                        ->where('tSku.element_id = ?', $elementId)
                        ->where('tSku.param_id = ?', $priceId)
                        ->where('tSku.variant = ?', -1)
                        ->order('tSku.value_n ' . $dir)
                        ->order('tSku.value_s ' . $dir);

                } else {

                    $fieldName = $this->_jbtables->getFieldName($field, $order->get('mode'));
                    $columns   = $this->_jbtables->getFields($this->_jbtables->getIndexTable($itemType));

                    if (in_array($fieldName, $columns, true)) {
                        $select
                            ->order('tIndex.' . $fieldName . ' ' . $dir)
                            ->where('tIndex.' . $fieldName . ' IS NOT NULL')
                            ->select($fieldName);
                    }
                }
            }
        }
    }

    /**
     * Check is element is range
     * @param $value
     * @return bool
     */
    protected function _isRange($value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (isset($value['range']) || isset($value['range-date'])) {
            return $this->_isRangeRow($value);
        }

        foreach ($value as $val) {
            return $this->_isRangeRow($val);
        }

    }

    /**
     * @param $value
     * @return bool
     */
    private function _isRangeRow($value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (isset($value['range']) || isset($value['range-date'])) {
            return true;
        }

        return false;
    }
}
