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
 * Class JBModelValues
 */
class JBModelValues extends JBModel
{
    /**
     * Create and return self instance
     * @return JBModelValues
     */
    public static function model()
    {
        return new self();
    }

    /**
     * @param       $elementId
     * @param       $itemType
     * @param       $applicationId
     * @param array $filter
     * @return array|JObject|null
     */
    public function getPropsValues($elementId, $itemType, $applicationId, array $filter = array())
    {
        $this->app->jbdebug->mark('model::' . $elementId . '::start');

        $cacheHash = md5(serialize(func_get_args()));
        $cacheKey  = 'get-props-values';
        if (!($result = $this->_jbcache->get($cacheHash, $cacheKey))) {

            $identifier = $this->_jbtables->getFieldName($elementId, 's');
            $tableName  = $this->_jbtables->getIndexTable($itemType);

            $select = $this->_getItemSelect($itemType, $applicationId)
                ->innerJoin($tableName . ' AS tIndex ON tIndex.item_id = tItem.id')
                ->clear('select')
                ->select('tIndex.' . $identifier . ' AS value')
                ->select('tIndex.' . $identifier . ' AS text')
                ->select('COUNT(tIndex.' . $identifier . ') AS count')
                ->where('tIndex.' . $identifier . ' <> ""')
                ->where('tIndex.' . $identifier . ' IS NOT NULL')
                ->group('tIndex.' . $identifier);

            $columns = $this->_jbtables->getFields($this->_jbtables->getIndexTable($itemType));

            // simple filter (if defined)
            foreach ($filter as $filterIdentifier => $filterValue) {
                // TODO add filter by range and dates
                if (!is_array($filterValue)) {

                    if ($filterIdentifier == '_itemname') {
                        $select->where('tItem.name = ?', $filterValue);

                    } else if ($filterIdentifier == '_itemcategory') {
                        $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM
                            . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
                        $select->where('tCategoryItem.category_id = ?', $filterValue);

                    } else {
                        $filterIdentifier = $this->_jbtables->getFieldName($filterIdentifier, 's');
                        if (in_array($filterIdentifier, $columns, true)) {
                            $select->where('tIndex.' . $filterIdentifier . ' = ?', $filterValue);
                        }

                    }

                }
            }

            $result = $this->fetchAll($select, true);

            // mysql can not sort both by num and string
            if (!empty($result)) {
                $result = $this->_sortValues($result, $elementId);
            }

            $this->_jbcache->set($cacheHash, $result, $cacheKey);
        }

        $this->app->jbdebug->mark('model::' . $elementId . '::end');

        return $result;
    }

    /**
     * @param      $element_id
     * @param      $param_id
     * @param      $itemType
     * @param      $applicationId
     * @param int  $variant
     * @param int  $catId
     * @return array|JObject
     */
    public function getParamsValues($element_id, $param_id, $itemType, $applicationId, $catId = null, $variant = null)
    {
        $select = $this->_getItemSelect($itemType, $applicationId)
            ->clear('select')
            ->innerJoin(ZOO_TABLE_JBZOO_SKU . ' AS tSku ON tSku.item_id = tItem.id')
            ->select('tSku.value_s as value, tSku.value_s as text')
            ->select('COUNT(DISTINCT tSku.item_id) AS count')
            ->where('tSku.element_id = ?', $element_id)
            ->where('tSku.param_id = ? ', $param_id)
            ->group('tSku.value_s');
        if ($variant !== null) {
            $select->where('tSku.variant = ?', $variant);
        }

        if ($catId) {
            $ids = $this->getSubCategories($catId);
            $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
            $select->where('tCategoryItem.category_id IN (' . implode(',', $ids) . ')');
        }
        $values = $this->fetchAll($select, true);

        return $values;
    }

    /**
     * @param $data
     * @param $identifier
     * @return array
     */
    public function _sortValues($data, $identifier)
    {
        $elements = $this->app->jbentity->getItemTypesData(false);
        if (isset($elements[$identifier])
            && in_array($elements[$identifier]['type'], array('radio', 'checkbox', 'select'))
        ) {

            $optionList = array();
            foreach ($elements[$identifier]['option'] as $item) {
                $optionList[] = $item['name'];
            }

            // order data by $optionList
            $ordered = array();
            foreach ($optionList as $newIndex => $key) {

                $index = false;
                foreach ($data as $oldIndex => $dataItem) {
                    if (strtolower(trim($dataItem['value'])) == strtolower(trim($key))) {
                        $index = $oldIndex;
                    }
                }

                if ($index !== false) {
                    $ordered[$newIndex] = $data[$index];
                }
            }

            $data = $ordered;

        } else {
            usort($data, array('JBModelValues', "sortByText"));
        }

        return $data;
    }

    /**
     * Sort by text field
     * @param $a
     * @param $b
     * @return int
     */
    static public function sortByText($a, $b)
    {
        $aNum = (float)$a['text'];
        $bNum = (float)$b['text'];

        if ($aNum == $bNum) {
            return strcmp($a['text'], $b['text']);
        }

        return ($aNum < $bNum) ? -1 : 1;
    }

    /**
     * Get authors values list
     * @param $applicationId int
     * @return array
     */
    public function getAuthorValues($applicationId)
    {
        $this->app->jbdebug->mark('model::filter::getAuthorValues:start');

        if (!($result = $this->_jbcache->get(func_get_args(), 'get-author-values'))) {

            $select = $this->_getSelect()
                ->select('tItem.created_by AS value, tUsers.name AS text, count(tItem.id) AS count')
                ->from(ZOO_TABLE_ITEM . ' AS tItem')
                ->innerJoin('#__users AS tUsers ON tUsers.id = tItem.created_by')
                ->group('tItem.created_by')
                ->where('tItem.application_id = ?', (int)$applicationId)
                ->order('tUsers.name ASC');

            $result = $this->fetchAll($select, true);

            $this->_jbcache->set(func_get_args(), $result, 'get-author-values');
        }

        $this->app->jbdebug->mark('model::filter::getAuthorValues::end');

        return $result;
    }

    /**
     * Get name values
     * @param int $applicationId
     * @return array
     */
    public function getNameValues($applicationId)
    {
        $this->app->jbdebug->mark('model::filter::getNameValues:start');

        if (!($result = $this->_jbcache->get(func_get_args(), 'get-name-values'))) {

            $select = $this->_getItemSelect(null, $applicationId)
                ->clear('select')
                ->select(array('tItem.id AS value', 'tItem.name AS text', 'count(tItem.id) AS count'))
                ->group('tItem.name')
                ->order('tItem.name ASC');
            $result = $this->fetchAll($select, true);

            $this->_jbcache->set(func_get_args(), $result, 'get-name-values');
        }

        $this->app->jbdebug->mark('model::filter::getNameValues::end');

        return $result;
    }

    /**
     * Get name values
     * @param int  $applicationId
     * @param null $itemType
     * @return array|JObject
     */
    public function getTagValues($applicationId, $itemType = null)
    {
        $this->app->jbdebug->mark('model::filter::getTagValues:start');

        if (!($result = $this->_jbcache->get(func_get_args(), 'get-tag-values'))) {

            $select = $this->_getItemSelect(null, $applicationId)
                ->clear('select')
                ->select(array('tTag.name AS value', 'tTag.name AS text', 'count(tTag.name) AS count'))
                ->innerJoin(ZOO_TABLE_TAG . ' AS tTag ON tTag.item_id = tItem.id')
                ->group('tTag.name')
                ->order('tTag.name ASC');

            if ($itemType) {
                $select->where('tItem.type = ?', $itemType);
            }

            $result = $this->fetchAll($select, true);

            $this->_jbcache->set(func_get_args(), $result, 'get-tag-values');
        }

        $this->app->jbdebug->mark('model::filter::getTagValues::end');

        return $result;
    }

    /**
     * Get min/max range by field in catalog
     * @param string $identifier
     * @param string $itemType
     * @param int    $applicationId
     * @return array|JObject
     */
    public function getRangeByField($identifier, $itemType, $applicationId)
    {
        $this->app->jbdebug->mark('model::filter::getRangeByField:start');

        if (!($result = $this->_jbcache->get(func_get_args(), 'get-range-by-field'))) {
            $identifier = $this->_jbtables->getFieldName($identifier, 'n');
            $tableName  = $this->_jbtables->getIndexTable($itemType);

            $select = $this->_getItemSelect($itemType, $applicationId)
                ->innerJoin($tableName . ' AS tIndex ON tIndex.item_id = tItem.id')
                ->clear('select')
                ->select('MAX(tIndex.' . $identifier . ') AS max, MIN(tIndex.' . $identifier . ') AS min')
                //->where('tIndex.' . $identifier . ' <> ""')
                //->where('tIndex.' . $identifier . ' IS NOT NULL')
            ;

            $result = $this->fetchRow($select);

            $this->_jbcache->set(func_get_args(), $result, 'get-range-by-field');
        }

        $this->app->jbdebug->mark('model::filter::getRangeByField::end');

        return $result;
    }

    /**
     * Get range for price field
     * @param     $identifier
     * @param     $itemType
     * @param     $applicationId
     * @param     $catId
     * @return JObject
     */
    public function getRangeByPrice($identifier, $itemType, $applicationId, $catId = null)
    {
        $this->app->jbdebug->mark('model::filter::getRangeByPrice:start');

        if (!($result = $this->_jbcache->get(func_get_args(), 'get-range-by-price'))) {

            $select = $this->_getItemSelect($itemType, $applicationId)
                ->clear('select')
                ->innerJoin(ZOO_TABLE_JBZOO_SKU . ' AS tSku ON tSku.item_id = tItem.id')
                ->select(array(
                    'MAX(tSku.value_n) AS total_max',
                    'MIN(tSku.value_n) AS total_min'
                ))
                ->where('tSku.element_id = ?', $identifier)
                ->where('tSku.param_id = ?', '_value')
                ->where('tSku.variant = ?', -1);

            if ($catId) {

                $ids = $this->getSubCategories($catId);
                $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
                $select->where('tCategoryItem.category_id IN (' . implode(',', $ids) . ')');
            }

            $result = $this->fetchRow($select);

            $this->_jbcache->set(func_get_args(), $result, 'get-range-by-price');
        }

        $this->app->jbdebug->mark('model::filter::getRangeByPrice::end');

        return $result;
    }

    /**
     * Method used for price elements. Return array of ids.
     * @param int $id Category id used to find children's categories.
     * @return array
     */
    protected function getSubCategories($id)
    {
        $cats   = array($id => $id);
        $select = $this->_getSelect()
            ->select('tCategory.id')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory')
            ->where('tCategory.parent = ?' . $id);

        $subCats = $this->fetchAll($select);
        $subCats = $this->_groupBy($subCats, 'id');

        if ($subCats) {
            $cats = array_merge($cats, $subCats);
        }

        return $cats;
    }
}
