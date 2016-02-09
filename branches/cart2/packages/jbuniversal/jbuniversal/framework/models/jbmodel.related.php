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
 * Class JBModelRelated
 */
class JBModelRelated extends JBModel
{

    /**
     * Create and return self instance
     * @return JBModelRelated
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Get auto related items
     * @param Item     $item
     * @param JSONData $config
     * @param JSONData $params
     * @return array
     */
    public function getRelated(Item $item, $config, $params)
    {
        $this->app->jbdebug->mark('model::getRelated::start');

        $cacheHash = md5(serialize((array)$params) . '||' . serialize((array)$config) . '||itemid-' . $item->id);
        $cacheKey  = 'related-items';

        if (!($result = $this->app->jbcache->get($cacheHash, $cacheKey))) {

            $searchMethod = (int)$params->get('search-method', 2);

            $data   = $this->_getSearchData($item, $params);
            $rows   = $this->_getFromDatabase($item, $data, $searchMethod, $params);
            $result = $this->_groupBy($rows, 'id');

            if (empty($result)) {
                $result[] = '-1'; // only for cache empty result
            }

            $this->app->jbcache->set($cacheHash, $result, $cacheKey);
        }

        $this->app->jbdebug->mark('model::getRelated::loadItems');


        // create item objects and order
        $order = $params->get('order', 'relevant');
        if ($order == 'relevant') {
            $result = $this->getZooItemsByIds($result);

        } elseif ($order == 'random') {
            $result = $this->getZooItemsByIds($result);
            shuffle($result); // if items are cached

        } else {
            $result = $this->getZooItemsByIds($result, $order);
        }


        $this->app->jbdebug->mark('model::getRelated::end');

        return $result;
    }

    /**
     * Get all item from database
     * @param Item  $item
     * @param Array $data
     * @param Int   $searchMethod
     * @param Array $params
     * @return array
     */
    protected function _getFromDatabase(Item $item, $data, $searchMethod, $params)
    {
        $selects = array();

        $itemType  = $item->getType()->id;
        $tableName = $this->_jbtables->getIndexTable($itemType, 'str');
        $columns   = $this->_jbtables->getFields($this->_jbtables->getIndexTable($item->getType()->id));

        $columns = array_merge($columns, array(
            $this->_jbtables->getFieldName('_itemname'),
            $this->_jbtables->getFieldName('_itemtype'),
            $this->_jbtables->getFieldName('_itemcategory'),
            $this->_jbtables->getFieldName('_itemfrontpage'),
            $this->_jbtables->getFieldName('_itemauthor'),
        ));

        foreach ($data as $elementId => $elemValues) {

            $tableFieldName = false;

            // no empty values
            $elemValues = $this->_toCleanArray((array)$elemValues, $searchMethod);
            if (is_null($elemValues)) {
                continue;
            }

            // check exists fields
            if (!in_array($elementId, $columns, true)) {
                continue;
            }

            // create empty SQL query
            $select = $this->_getItemSelect(null, null, 0)
                ->clear('select')
                ->select('tItem.id AS id')
                ->where('tItem.id <> ?', $item->id);


            // set application
            $appList = $params->get('check_app', 0);
            if (is_numeric($appList) && $appList) {
                $select->where('tItem.application_id = ?', (int)$item->application_id);

            } else if (is_array($appList)) {
                $select->where('tItem.application_id IN (' . implode(',', $appList) . ')');
            }


            // set item type
            if ((int)$params->get('check_type', 0)) {

                $typeKey     = $this->_jbtables->getFieldName('_itemtype');
                $customTypes = isset($data[$typeKey]) ? $data[$typeKey] : array();
                $customTypes = $this->_toCleanArray((array)$customTypes);

                if (!empty($customTypes)) {
                    $select->where('tItem.type IN (' . implode(',', $customTypes) . ')');
                } else {
                    $select->where('tItem.type = ?', $item->getType()->id);
                }
            }


            if ($elementId == $this->_jbtables->getFieldName('_itemname')) {
                $tableFieldName = 'tItem.name';


            } else if ($elementId == $this->_jbtables->getFieldName('_itemtype')) {
                $tableFieldName = 'tItem.type';


            } else if ($elementId == $this->_jbtables->getFieldName('_itemtag')) {
                $select->leftJoin(ZOO_TABLE_TAG . ' AS tTag ON tTag.item_id = tItem.id');
                $tableFieldName = 'tTag.name';


            } else if ($elementId == $this->_jbtables->getFieldName('_itemauthor')) {
                $tableFieldName = 'tItem.created_by';


            } else if ($elementId == $this->_jbtables->getFieldName('_itemcategory')) {
                $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');

                $elemValues = (array)$elemValues;

                $cleanValue = (int)str_replace("'", '', $elemValues[0]);
                if ($cleanValue) {
                    $tableFieldName = 'tCategoryItem.category_id';
                } else {
                    $select->leftJoin(ZOO_TABLE_CATEGORY . ' AS tCategory ON tCategoryItem.category_id = tCategory.id');
                    $tableFieldName = 'tCategory.name';
                }


            } else if ($elementId == $this->_jbtables->getFieldName('_itemfrontpage')) {
                $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
                $select->where('tCategoryItem.category_id = ?', '0');


            } else {
                $select->leftJoin($tableName . ' AS tIndex ON tIndex.item_id = tItem.id');
                $tableFieldName = 'tIndex.' . $elementId;
            }

            if ($tableFieldName) {
                $conds = array();
                foreach ($elemValues as $elemValue) {
                    if ($searchMethod == 1 || is_numeric($elemValue)) {
                        $conds[] = $tableFieldName . ' = ' . $this->_quote($elemValue);
                    } else {
                        if (is_array($elemValue)) {
                            foreach($elemValue as $elemValueOne) {
                                $conds[] = $this->_buildLikeBySpaces($elemValueOne, $tableFieldName);
                            }
                        } else {
                            $conds[] = $this->_buildLikeBySpaces($elemValue, $tableFieldName);
                        }
                    }
                }

                if (!empty($conds)) {
                    $select->where('(' . implode(' OR ', $conds) . ')');
                }
            }

            $selects[] = $select->__toString();
        }

        if (!empty($selects)) {

            $union = '(' . implode(') UNION ALL (', $selects) . ')';

            $allSelect = $this->_getSelect()
                ->select('tAll.id')
                ->select('COUNT(tAll.id) AS count')
                ->from('(' . $union . ') AS tAll')
                ->group('tAll.id')
                ->limit(500); // for optimizations only

            $order = $params->get('order', 'relevant');
            if ($order == 'random') {
                $allSelect->order('RAND()');

            } elseif ($order == 'relevant') {
                $allSelect->order('count DESC');
            }

            $relevant = (int)$params->get('relevant', 5);
            if ($relevant > 0) {
                $allSelect->having('count >= ?', $relevant);
            }

            // clean query for optimization
            $db = JFactory::getDbo();
            $db->setQuery($allSelect);
            $rows = (array)$db->loadAssocList();


            // post limit logic for nice realty results
            $limit = (int)$params->get('count', 4);
            if ($limit > 0 && count($rows) > 0) {
                $rows = array_slice($rows, 0, $limit);
            }


            return $rows;
        }

        return array();
    }

    /**
     * Check is value empty
     * @param $value
     * @return bool
     */
    protected function _isEmpty($value)
    {
        return (empty($value) && ($value !== 0 || $value !== "0"));
    }

    /**
     * Convert data to clean array
     * @param $elemValues
     * @param $searchMethod
     * @return array
     */
    protected function _toCleanArray($elemValues, $searchMethod = 1)
    {
        foreach ($elemValues as $key => $elemValue) {

            if (strpos(JString::trim($elemValue), "\n") !== false) {
                $elemValue = explode("\n", $elemValue);
            }

            if (is_array($elemValue)) {

                foreach ($elemValue as $innerKey => $innerValue) {
                    if ($this->_isEmpty($innerValue)) {
                        unset($elemValue[$innerKey]);
                    }
                }

                $elemValues[$key] = $elemValue;

            } elseif ($this->_isEmpty($elemValue)) {
                unset($elemValues[$key]);
            }
        }

        $elemValues = $this->_quote($elemValues);

        // double escaping hack
        foreach ($elemValues as $key => $elemValue) {
            $elemValues[$key] = JString::trim($elemValue, '\'"');
        }

        return (!empty($elemValues) ? $elemValues : null);
    }


    /**
     * Get search data from item
     * @param Item     $item
     * @param JSONData $params
     * @return array
     */
    protected function _getSearchData(Item $item, $params)
    {
        // get related categories
        $itemCategories = array();

        $checkCategory = (int)$params->get('check_category', 1);
        if ($checkCategory == 1) {
            $itemCategories[] = $item->getPrimaryCategoryId();

        } else if ($checkCategory == 2) {
            $itemCategories = $item->getRelatedCategoryIds();
        }

        // get manualy conditions
        $conds    = array();
        $tmpConds = $params->get('conditions', array());
        foreach ($tmpConds as $cond) {
            if (isset($cond['key']) && !empty($cond['key'])) {

                $key   = preg_replace('#[^0-9a-z\_\-]#i', '', $cond['key']);
                $value = $cond['value'];

                if (strpos($value, '[') !== false && strpos($value, ']') !== false) {
                    $value = json_decode($value, true);
                }

                if (!empty($value) && !empty($key)) {
                    $conds[$key] = $value;
                }
            }
        }

        $tmpResult = array(
            '_itemfrontpage' => (int)in_array(0, $itemCategories),
            '_itemcategory'  => $itemCategories,
            '_itemname'      => $item->name,
            '_itemtag'       => $item->getTags(),
            '_itemauthor'    => $item->created_by,
        );


        $elements = $item->getElements();
        foreach ($elements as $element) {
            if ($data = $element->getSearchData()) {
                $tmpResult[$element->identifier] = JString::trim($data);
            }
        }

        $tmpResult = array_merge($tmpResult, $conds);

        // build result
        $checkedFields   = $params->get('check_fields', array());
        $checkedFields[] = '_itemtype';

        $result = array();
        foreach ($tmpResult as $id => $values) {

            if (count($checkedFields) > 1 && !in_array($id, $checkedFields, true)) {
                continue;
            }

            $result[$this->_jbtables->getFieldName($id)] = $values;
        }

        return $result;
    }

}
