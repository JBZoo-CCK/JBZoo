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
 * Class JBModelItem
 */
class JBModelItem extends JBModel
{

    /**
     * @var ItemTable
     */
    protected $_table = null;

    /**
     * Create and return self instance
     * @return JBModelItem
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        $this->_table = $this->app->table->item;
    }

    /**
     * Get Zoo items
     * @param int       $appId
     * @param int|array $catId
     * @param string    $typeId
     * @param array     $options
     * @return array
     */
    public function getList($appId = null, $catId = null, $typeId = null, $options = array())
    {
        $options = $this->app->data->create($options);

        // create select
        $select = $this->_getSelect()
            ->select('tItem.id')
            ->from(ZOO_TABLE_ITEM . ' AS tItem');


        // check type
        if (!empty($typeId)) {
            $typeId = (array)$typeId;
            $select->where('tItem.type IN ("' . implode('", "', $typeId) . '")');
        }

        // check appId
        if (is_array($appId)) {
            $select->where('tItem.application_id IN (' . implode(',', $appId) . ')');
        } elseif ((int)$appId) {
            $select->where('tItem.application_id = ?', (int)$appId);
        }

        // check category
        $catId = array_filter((array)$catId, function ($value) {
            return $value > 0 || $value == -1 || $value === 0 || $value === '0';
        });

        if (
            !empty($catId) &&
            !in_array('-1', $catId)
        ) {
            $select->innerJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tItem.id = tCategoryItem.item_id');

            $subcatId = array();
            if ((int)$options->get('category_nested')) {
                $subcatId = JBModelCategory::model()->getNestedCategories($catId);
            }

            $catId += $subcatId;

            if (!empty($catId)) {
                $select->where('tCategoryItem.category_id IN ("' . implode('", "', $catId) . '")');
            }

        }

        // set limit
        if ($options->get('limit')) {
            $limit = $options->get('limit');

            if (is_array($limit)) {
                $select->limit($limit[1], $limit[0]);
            } else {
                $select->limit($limit);
            }
        }

        if ($options->get('id')) {
            $idList = (array)$options->get('id');
            $select->where('tItem.id IN (' . implode(',', $idList) . ')');
        }

        // check access
        if ($options->get('user')) {
            $select->where('tItem.' . $this->app->user->getDBAccessString());
        }

        // check status
        if ($options->get('published') == 1) {
            $select
                ->where('tItem.state = ?', 1)
                ->where('(tItem.publish_up = ' . $this->_dbNull . ' OR tItem.publish_up <= ' . $this->_dbNow . ')')
                ->where('(tItem.publish_down = ' . $this->_dbNull . ' OR tItem.publish_down >= ' . $this->_dbNow . ')');

        } else if ($options->get('state') == 2) {
            $select->where('tItem.state = ?', 1);

        } else if ($options->get('state') == 3) {
            $select->where('tItem.state = ?', 0);
        }

        $order = $options->get('order', 'id');
        if (isset($order)) {
            $select->order($this->app->jborder->get($options->get('order', 'id'), 'tItem'));
        }

        $rows = $this->_query($select);

        if (!empty($rows)) {
            // convert id list to Zoo Items
            $ids    = $this->_groupBy($rows, 'id');
            $result = $this->getZooItemsByIds($ids, $order);

            return $result;
        }

        return array();
    }

    /**
     * Get item categories
     * @param $itemId
     * @return mixed
     */
    public function getItemCategories($itemId)
    {
        $select = $this->_getSelect()
            ->select('tCategory.*')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory')
            ->innerJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategory.id = tCategoryItem.category_id')
            ->where('tCategoryItem.item_id = ?', $itemId);

        return $this->_query($select);
    }

    /**
     * Get related categories by item id
     * @param $itemId
     */
    public function getRelatedCategories($itemId)
    {
        return $this->app->category->getItemsRelatedCategoryIds($itemId, false);
    }

    /**
     * Get item by alias
     * @param $alias
     * @param $appId
     * @return Item|null
     */
    public function getByAlias($alias, $appId = null)
    {
        $alias = $this->app->string->sluggify($alias);

        if (!empty($alias)) {
            $conditions = array(
                'alias = ' . $this->app->database->Quote($alias),
            );

            if ((int)$appId) {
                $conditions[] = ' AND application_id = ' . (int)$appId;
            }

            return $this->_table->first(compact('conditions'));
        }

        return null;
    }

    /**
     * Get item by id
     * @param int $itemId
     * @param int $appId
     * @return Item|null
     */
    public function getById($itemId, $appId = null)
    {
        $itemId = (int)$itemId;
        if (!empty($itemId)) {

            $conditions = array(
                'id = ' . $itemId,
            );

            if ((int)$appId) {
                $conditions[] = ' AND application_id = ' . (int)$appId;
            }

            return $this->_table->first(compact('conditions'));
        }

        return null;
    }

    /**
     * Get item by name
     * @param int $name
     * @param int $appId
     * @return Item|null
     */
    public function getByName($name, $appId = null)
    {
        $name = JString::trim($name);
        if (!empty($name)) {

            $conditions = array(
                'name = ' . $this->app->database->Quote($name),
            );

            if ((int)$appId) {
                $conditions[] = ' AND application_id = ' . (int)$appId;
            }

            return $this->_table->first(compact('conditions'));
        }

        return null;
    }

    /**
     * Get item by id
     * @param $sku
     * @param $appId
     * @return Item|null
     */
    public function getBySku($sku, $appId = null)
    {
        $itemId = (int)JBModelSku::model()->getItemIdBySku($sku);
        if ($itemId > 0 && $item = $this->getById($itemId)) {
            if ($appId === null) {
                return $item;

            } elseif ((int)$item->application_id === (int)$appId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Create new empty item in DB
     * @param int    $appId
     * @param string $type
     * @param string $nameSuf
     * @return Item
     */
    public function createEmpty($appId, $type, $nameSuf = null)
    {
        // some vars
        $now    = $this->app->date->create()->toSQL();
        $userId = $this->app->user->get()->get('id');

        // create empty item
        $item = $this->app->object->create('Item');

        // set default data
        $item->application_id = (int)$appId;
        $item->type           = $type;
        $item->state          = 0;
        $item->access         = $this->app->joomla->getDefaultAccess();
        $item->modified_by    = $userId;
        $item->created_by     = $userId;
        $item->created        = $now;
        $item->modified       = $now;
        $item->publish_up     = $now;
        $item->name           = JText::_('JBZOO_NEW_ITEM_NAME') . (($nameSuf) ? ' #' . $nameSuf : '');
        $item->alias          = uniqid('item-uid-'); // hack for speed

        // set default params
        $item->getParams()->loadArray(array(
            "metadata.title"          => "",
            "metadata.description"    => "",
            "metadata.keywords"       => "",
            "metadata.robots"         => "",
            "metadata.author"         => "",
            "config.enable_comments"  => 1,
            "config.primary_category" => "",
            "jbzoo.no_index"          => 1, // hack for speed
        ));

        // for create item_id in new Item object
        $this->_table->save($item);

        return $item;
    }

    /**
     * Disable all items in app
     * @param int    $appId
     * @param string $typeid
     * @param array  $exclude
     * @return bool
     */
    public function disableAll($appId, $typeid, $exclude = array())
    {
        if (!(int)$appId) {
            return false;
        }

        $select = $this->_getSelect()
            ->update(ZOO_TABLE_ITEM)
            ->where('application_id = ?', (int)$appId)
            ->where('type = ?', $typeid)
            ->set('state = 0');

        if (!empty($exclude)) {
            $select->where('id NOT IN (' . implode(', ', $exclude) . ')');
        }

        $this->_query($select);

        return true;
    }

    /**
     * Remove all items in app
     * @param int    $appId
     * @param string $typeid
     * @param array  $exclude
     * @return bool
     */
    public function removeAll($appId, $typeid, $exclude = array())
    {
        if (!(int)$appId) {
            return false;
        }

        $select = $this->_getSelect()
            ->select('tItem.id')
            ->from(ZOO_TABLE_ITEM . ' AS tItem')
            ->where('type = ?', $typeid)
            ->where('application_id = ?', (int)$appId);

        if (!empty($exclude)) {
            $select->where('id NOT IN (' . implode(', ', $exclude) . ')');
        }

        $rows = $this->fetchAll($select);
        $ids  = $this->_groupBy($rows, 'id');

        if (!empty($ids)) {
            $whereIds = 'item_id IN (' . implode(', ', $ids) . ')';

            // delete item to category relations
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_CATEGORY_ITEM)->where($whereIds));

            // delete related comments
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_COMMENT)->where($whereIds));

            // delete related search data rows
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_SEARCH)->where($whereIds));

            // delete related rating data rows
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_RATING)->where($whereIds));

            // delete related tag data rows
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_TAG)->where($whereIds));

            // delete related jbzoo index rows
            // TODO remove data from index tables. For this we must know item types

            // delete related favorite rows
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_JBZOO_FAVORITE)->where($whereIds));

            // delete from item table
            $this->sqlQuery($this->_getSelect()->delete(ZOO_TABLE_ITEM)->where('id IN (' . implode(', ', $ids) . ')'));
        }

        return true;
    }

    /**
     * @param int    $appId
     * @param string $types
     * @param array  $catList
     * @param int    $isPublished
     * @return int
     */
    public function getTotal($appId, $types, $catList = array(), $isPublished = 1)
    {
        $select = $this->_getSelect()
            ->select('COUNT(tItem.id) AS count')
            ->from(ZOO_TABLE_ITEM . ' AS tItem');

        if ($isPublished == 1) {
            $select
                ->where('tItem.state = ?', 1)
                ->where('(tItem.publish_up = ' . $this->_dbNull . ' OR tItem.publish_up <= ' . $this->_dbNow . ')')
                ->where('(tItem.publish_down = ' . $this->_dbNull . ' OR tItem.publish_down >= ' . $this->_dbNow . ')');

        } elseif ($isPublished == 2) {
            $select->where('tItem.state = ?', 1);

        } elseif ($isPublished == 3) {
            $select->where('tItem.state = ?', 0);
        }

        if (!empty($appId)) {
            $strApp = is_array($appId) ? 'tItem.application_id IN (' . implode(',', $appId) . ')'
                : 'tItem.application_id = ' . (int)$appId;

            $select->where($strApp);
        }


        if (!empty($types)) {
            $srtTypes = is_array($types) ? 'tItem.type IN ("' . implode('","', $types) . '")'
                : 'tItem.type = ' . $this->_quote($types);

            $select->where($srtTypes);
        }


        if (!empty($catList) && !in_array('-1', $catList)) {
            $select->innerJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tItem.id = tCategoryItem.item_id');
            $select->where('tCategoryItem.category_id IN ("' . implode('", "', (array)$catList) . '")');
        }

        $result = $this->fetchRow($select);

        return (int)$result->count;
    }

}