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
 * Class JBModelCategory
 */
class JBModelCategory extends JBModel
{

    /**
     * @var CategoryTable
     */
    protected $_table = null;

    /**
     * Create and return self instance
     * @return JBModelCategory
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

        $this->_table = $this->app->table->category;
    }

    /**
     * @param $categoryId
     * @return mixed
     */
    public function getParent($categoryId)
    {
        $select = $this->_getSelect()
            ->select('tCategory.*')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory')
            ->where('tCategory.id = ?', $categoryId);

        return $this->fetchRow($select);
    }

    /**
     * Get nested categories by parent category id
     * @param array $categoryId
     * @param int $appId
     * @return array
     */
    public function getNestedCategories($categoryId = null, $appId = null)
    {
        $categoryId = (array)$categoryId;

        $select = $this->_getSelect()
            ->select('tCategory.id')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory');

        if (!empty($categoryId)) {
            $select->where('tCategory.parent IN (' . implode(', ', $categoryId) . ')');
        }

        if ((int)$appId) {
            $select->where('tCategory.application_id = ?', (int)$appId);
        }

        $subcategories   = $this->fetchAll($select);
        $subcategoriesId = $this->_groupBy($subcategories, 'id');

        $result = $subcategoriesId;

        foreach ($subcategoriesId as $catId) {
            $result += $this->getNestedCategories($catId);
        }

        return $result;
    }

    /**
     * Get category list
     * @param null $appId
     * @param array $options
     * @return array|JObject
     */
    public function getList($appId = null, $options = array())
    {

        $options = $this->app->data->create($options);

        $select = $this->_getSelect()
            ->select('tCategory.id')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory');

        if ((int)$appId) {
            $select->where('tCategory.application_id = ?', (int)$appId);
        }

        if (!is_null($options->get('parent'))) {
            $select->where('tCategory.parent = ?', $options->get('parent'));
        }

        if ($options->get('published')) {
            $select->where('tCategory.published = ?', 1);
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

        if ($options->get('order')) {
            $select->order($this->app->jborder->get($options->get('order', 'id'), 'tCategory', true));
        }

        // request to DB
        $rows = $this->_query($select);

        if (!empty($rows)) {
            // onvert id list to Zoo Items
            $ids    = $this->_groupBy($rows, 'id');
            $result = $this->getZooCatsByIds($ids, $options->get('order'));

            return $result;
        }

        return array();
    }


    /**
     * @param $ids
     * @param $order
     * @return array
     */
    public function getZooCatsByIds($ids, $order = null)
    {
        if (empty($ids)) {
            return array();
        }

        $conditions = array(
            'id IN (' . implode(',', $ids) . ')'
        );
        $order      = $this->app->jborder->get($order, null, true);
        $result     = $this->_table->all(compact('conditions', 'order'));

        return $result;
    }

    /**
     * Get category by alias
     * @param $alias
     * @param $appId
     * @return Item|null
     */
    public function getByAlias($alias, $appId = null)
    {
        if ($alias = $this->app->string->sluggify($alias)) {
            $conditions = array(
                'alias = ' . $this->app->database->Quote($alias)
            );

            if ((int)$appId) {
                $conditions[] = ' AND application_id = ' . (int)$appId;
            }

            return $this->_table->first(compact('conditions'));
        }

        return null;
    }

    /**
     * Get category by id
     * @param int $categoryId
     * @param int $appId
     * @return Item
     */
    public function getById($categoryId, $appId = null)
    {
        $conditions = array(
            'id = ' . (int)$categoryId
        );

        if ((int)$appId) {
            $conditions[] = ' AND application_id = ' . (int)$appId;
        }

        return $this->_table->first(compact('conditions'));
    }

    /**
     * Get category by name
     * @param int $name
     * @param int $appId
     * @return Item
     */
    public function getByName($name, $appId = null)
    {
        $conditions = array(
            'name = ' . $this->app->database->Quote($name)
        );

        if ((int)$appId) {
            $conditions[] = ' AND application_id = ' . (int)$appId;
        }

        return $this->_table->first(compact('conditions'));
    }

    /**
     * Create new empty category in DB
     * @param int $appId
     * @param string $nameSuf
     * @return Category
     */
    public function createEmpty($appId, $nameSuf = null)
    {
        // create empty category
        $category                 = $this->app->object->create('Category');
        $category->application_id = (int)$appId;
        $category->name           = JText::_('JBZOO_NEW_CATEGORY_NAME') . (($nameSuf) ? ' #' . $nameSuf : '');
        $category->parent         = 0;
        $category->ordering       = 0;
        $category->published      = 0;
        $category->alias          = uniqid('category-uid-'); // hack for speed

        // for create category_id in new Category object
        $this->_table->save($category);

        return $category;
    }

    /**
     * Disable all catagories
     * @param int $appId
     * @param array $exclude
     * @return bool
     */
    public function disableAll($appId, $exclude = array())
    {
        if (!(int)$appId) {
            return false;
        }

        $select = $this->_getSelect()
            ->update(ZOO_TABLE_CATEGORY)
            ->where('application_id = ?', (int)$appId)
            ->set('published = 0');

        if (!empty($exclude)) {
            $select->where('id NOT IN (' . implode(', ', $exclude) . ')');
        }

        $this->_query($select);

        return true;
    }

    /**
     * Remove all catagories
     * @param int $appId
     * @param array $exclude
     * @return bool
     */
    public function removeAll($appId, $exclude = array())
    {
        if (!(int)$appId) {
            return false;
        }

        $conditions = array(
            'application_id = ' . (int)$appId
        );

        if (!empty($exclude)) {
            $conditions[] = ' AND id NOT IN (' . implode(', ', $exclude) . ')';
        }

        $categories = $this->_table->all(compact('conditions'));
        foreach ($categories as $category) {
            $this->_table->delete($category);
            $this->_table->unsetObject($category->id);
            unset($category);
        }

        return true;
    }

    /**
     * Get Items by category
     * @param int $applicationId
     * @param int $categoryId
     * @param string $itemsOrder
     * @param null $limit
     * @return array
     */
    public function getItemsByCategory($applicationId, $categoryId, $itemsOrder, $limit = null)
    {
        if ($limit == 0) {
            return array();
        }

        $limit = ($limit < 0) ? 99999 : $limit;

        $items = $this->app->table->item->getByCategory($applicationId, $categoryId, true, null, $itemsOrder, 0, $limit);

        return $items;
    }
}
