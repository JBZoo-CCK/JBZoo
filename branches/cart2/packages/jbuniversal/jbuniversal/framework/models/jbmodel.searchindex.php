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
 * Class JBModelSearchindex
 */
class JBModelSearchindex extends JBModel
{
    /**
     * @var array
     */
    protected $_stdIndexFields = array(
        '_itemcategory',
        '_itemfrontpage',
        '_itemtag',
    );

    /**
     * @var array
     */
    protected $_excludedIndexTypes = array(
        // JBZoo
        'jbbasketitems',
        'jbcomments',
        'jbcommentsrender',
        'jbfavorite',
        'jbgallery',
        'jbquickview',
        'jbcompare',
        'jbfavorite',
        'jbrelatedauto',
        'jbslidernivo',
        'jbsocial',

        // Zoo
        'addthis',
        'textarea',
        'disqus',
        'email',
        'file',
        'flickr',
        'googlemaps',
        'intensedebate',
        'joomlamodule',
        'link',
        'media',
        'relatedcategories',
        'relateditems',
        'socialbookmarks',
        'socialbuttons',
    );

    /**
     * Create and return self instance
     * @return JBModelSearchindex
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Reindex database
     * @param int $limit
     * @param int $offset
     * @return int
     */
    public function reIndex($limit = 100, $offset = 0)
    {
        $this->app->jbenv->maxPerformance();

        if ($offset == 0) {
            $this->_jbtables->dropAllIndex();
            $this->_jbtables->dropTable(ZOO_TABLE_JBZOO_SKU);

            $this->_jbtables->createIndexes();
            $this->_jbtables->checkSKU(true);
            $this->_jbtables->checkFavorite(true);
        }

        $this->_jbtables->getTableList(true); // force memory cache update

        $select = $this->_getSelect()
            ->select('tItem.id')
            ->from(ZOO_TABLE_ITEM . ' AS tItem')
            ->innerJoin(ZOO_TABLE_APPLICATION . ' AS tApp ON tItem.application_id = tApp.id')
            ->order('tItem.id')
            ->where('tItem.searchable = 1') // only searchable items
            ->where('tApp.application_group = ?', JBZOO_APP_GROUP)
            ->limit($limit, $offset);

        $rows = $this->fetchAll($select, true);

        if (empty($rows)) {
            return 0;
        }

        $ids = $this->_groupBy($rows);

        foreach ($ids as $id) {

            // get item by id
            $item = $this->app->table->item->get($id);
            if (!$item->getType()) {
                continue;
            }

            $itemData = $this->updateByItem($item, true);
            $itemType = $item->getType()->id;

            // compact data
            if (!isset($dataPack[$itemType])) {
                $dataPack[$itemType] = array();
            }

            $dataPack[$itemType][] = $itemData;

            // clear memory
            unset($item);
            $this->app->table->item->unsetObject($id);
        }

        // insert all data
        $totalLines = $this->_multiInsertData($dataPack);

        return $totalLines;
    }

    /**
     * Get total items count
     */
    public function getTotal()
    {
        $select = $this->_getSelect()
            ->select('COUNT(tItem.id) AS count')
            ->from(ZOO_TABLE_ITEM . ' AS tItem');

        $result = $this->fetchRow($select);

        return (int)$result->count;
    }

    /**
     * Update JBZoo index by itemId
     * @param Item $item
     * @param bool $returnDataPack
     * @return int|array
     */
    public function updateByItem(Item $item, $returnDataPack = false)
    {
        if ($item->getApplication()->getGroup() != JBZOO_APP_GROUP) {
            return null;
        }

        // for corrupted database
        if (!$item->getType()) {
            return 0;
        }

        $this->removeById($item);
        JBModelSku::model()->updateItemSku($item);

        $itemPack = array(
            'item_id'  => $item->id,
            'max_deep' => 0,
            'data'     => array(),
        );

        $rows = $this->_parseStdData($item);

        $elements = $item->getElements();
        foreach ($elements as $element) {
            $rows[$element->identifier] = $element->getSearchData();
        }

        foreach ($rows as $elementId => $row) {
            $rowData = $this->_valuesByTypes($row, $elementId);

            // find max deep vars level
            if (!empty($rowData)) {
                $max = max(count($rowData['s']), count($rowData['n']), count($rowData['d']));
                if ($itemPack['max_deep'] < $max) {
                    $itemPack['max_deep'] = $max;
                }
            }

            // compact values from field types
            foreach ($rowData as $keyType => $values) {

                $clearElemId = $this->app->jbtables->getFieldName($elementId, $keyType);

                if (!empty($values)) {
                    $itemPack['data'][$clearElemId] = $values;
                }
            }
        }

        if ($returnDataPack) {
            return $itemPack;
        }

        $itemType = $item->getType()->id;
        $result   = array($itemType => array($itemPack));

        return $this->_multiInsertData($result);
    }

    /**
     * Get data for database from serach index item
     * @param $data
     * @param $elementId
     * @return array
     */
    public function _valuesByTypes($data, $elementId)
    {
        $value = JString::trim($data);

        // exclude for frontpage mark
        if ($elementId == 'itemfrontpage') {
            return $this->_getInsertData(array(array('n' => (int)$value)));
        }

        // exclude for related categoriess
        if ($elementId == 'itemcategory') {

            $values = explode("\n", $value);

            $result = array();
            foreach ($values as $valueRows) {
                if (is_numeric($valueRows)) {
                    $result[] = array('n' => $valueRows);
                } else {
                    $result[] = array('s' => $valueRows);
                }
            }

            return $this->_getInsertData($result);
        }

        // exclude for user fileds
        if ($elementType = $this->app->jbentity->getTypeByElementId($elementId)) {

            if (in_array($elementType, $this->_excludedIndexTypes, true)) {
                return array();

            } elseif ($elementType == 'country') {
                $elements = $this->app->jbentity->getItemTypesData();
                $value    = $this->_parseCoutries($value, $elements[$elementId]);
            }
        }

        $multiInsert = array();

        // check is number
        $strings = explode("\n", $value);
        if (!empty($strings)) {
            foreach ($strings as $string) {

                $string = JString::trim($string);
                if ($string != '') {
                    if (preg_match('#^([0-9\.\,\-]+)#ius', $string, $matches)) {

                        $number = str_replace(',', '.', $matches[1]);
                        if (is_numeric($number)) {
                            $multiInsert[] = array('n' => $number);
                        }
                    }

                    $multiInsert[] = array('s' => $string);
                }
            }
        }

        // check date
        $times = $this->app->jbdate->convertToStamp($data);
        if (!empty($times)) {
            foreach ($times as $time) {
                if (!empty($time)) {
                    $multiInsert[] = array('d' => $time);
                }
            }
        }

        return $this->_getInsertData($multiInsert);
    }

    /**
     * Multi insert query
     * @param array $data
     * @return array
     */
    private function _getInsertData($data)
    {
        if (!empty($data)) {
            $result = array('s' => array(), 'n' => array(), 'd' => array());

            foreach ($data as $item) {
                $key = current(array_keys($item));

                if (!isset($result[$key])) {
                    $result[$key] = array();
                }

                $result[$key][] = $item[$key];
            }

            return $result;
        }

        return array();
    }

    /**
     * Multi insert data
     * @param array $rows
     * @return int
     */
    private function _multiInsertData(array $rows)
    {
        $simpleData = array();
        $totalCount = 0;

        foreach ($rows as $type => $items) {

            $simpleData[$type] = array();

            // get all possable keys
            $keys = array();
            foreach ($items as $item) {
                $keys = array_merge($keys, array_keys($item['data']));
            }
            $keys = array_unique($keys);


            // create sample arrays for insert data
            $simpleData[$type] = array();
            foreach ($keys as $key) {
                $simpleData[$type][] = $key; //$this->app->jbtables->getFieldName($key);
            }
            $simpleData[$type][] = 'item_id';
            $simpleData[$type]   = array_fill_keys($simpleData[$type], null);


            // create batch data for insert
            $dataPack = array();
            foreach ($items as $item) {

                for ($i = 0; $i < $item['max_deep']; $i++) {
                    $data = $simpleData[$type];

                    $isFound = false;
                    foreach ($item['data'] as $key => $dataRow) {
                        if (isset($dataRow[$i])) {
                            $isFound    = true;
                            $data[$key] = $dataRow[$i];
                        } else {
                            $data[$key] = null;
                        }
                    }

                    if ($isFound) {
                        $data['item_id'] = $item['item_id'];
                        $dataPack[]      = $data;
                        $totalCount++;
                    }
                }

            }

            // batch insert
            $this->_multiInsert($dataPack, $this->app->jbtables->getIndexTable($type));
        }

        return $totalCount;
    }

    /**
     * Remove item by it Id
     * @param Item $item
     */
    public function removeById($item)
    {
        $tbl = $this->_jbtables->getIndexTable($item->getType()->id);

        if ($tbl && $this->_jbtables->isTableExists($tbl)) {
            $delete = $this->_getSelect()
                ->delete($this->app->jbtables->getIndexTable($item->getType()->id))
                ->where('item_id = ?', (int)$item->id);

            $this->_dbHelper->query((string)$delete);
        }
    }


    /**
     * Parse coutries
     * @param $value
     * @param $element
     * @return string
     */
    private function _parseCoutries($value, $element)
    {
        $result = array();

        if (!empty($element['selectable_country'])) {

            foreach ($element['selectable_country'] as $countryISO) {

                $country = $this->app->country->isoToName($countryISO);

                if (strpos($value, $country) !== false) {
                    $result[] = JText::_($country);
                }

            }
        }

        return implode("\n", $result);
    }

    /**
     * Parse Standart item data
     * @param Item $item
     * @return array
     */
    private function _parseStdData($item)
    {
        $itemCategories = $this->getRelatedCategoryIds($item->id);
        $itemTags       = $this->_getRelatedTags($item->id);

        $result = array(
            'itemcategory'  => implode("\n", $itemCategories),
            'itemfrontpage' => (int)in_array('0', $itemCategories, true),
            'itemtag'       => implode("\n", $itemTags),
        );

        return $result;
    }

    /**
     * Get related category id list
     * @param int $itemId
     * @param bool $getName
     * @return array
     */
    public function getRelatedCategoryIds($itemId, $getName = true)
    {
        $select = $this->_getSelect()
            ->select('tCategory.id AS id')
            ->from(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem')
            ->innerJoin(ZOO_TABLE_CATEGORY . ' AS tCategory ON tCategory.id = tCategoryItem.category_id')
            ->where('tCategoryItem.item_id = ?', $itemId);

        if ($getName) {
            $select->select('tCategory.name AS name');
        }

        $rows = $this->fetchAll($select);

        $result = array();
        foreach ($rows as $row) {
            $result[] = $row->id;

            if ($getName) {
                $result[] = $row->name;
            }

        }

        return $result;
    }

    /**
     * Get related tags
     * @param int $itemId
     * @return array
     */
    private function _getRelatedTags($itemId)
    {
        $select = $this->_getSelect()
            ->select('tTags.name AS name')
            ->from(ZOO_TABLE_TAG . ' AS tTags')
            ->where('tTags.item_id = ?', $itemId);

        $rows = $this->fetchAll($select);

        $result = array();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $result[] = $row->name;
            }
        }

        return $result;
    }

    /**
     * Fields without usefull content
     * @return array
     */
    public function getExcludeTypes()
    {
        return $this->_excludedIndexTypes;
    }

    /**
     * Standart fields for index
     * @return array
     */
    public function getStdIndexFields()
    {
        return $this->_stdIndexFields;
    }
}
