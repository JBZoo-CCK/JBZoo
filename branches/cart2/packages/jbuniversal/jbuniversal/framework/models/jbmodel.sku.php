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
 * Class JBModelSku
 */
class JBModelSku extends JBModel
{
    /**
     * Return array of id - primary key and elements identifier
     * @type array
     */
    public static $ids = array();

    //TODO Move to other place
    const JBZOO_TABLE_SKU_PARAMS = '#__zoo_jbzoo_sku_params';
    const JBZOO_TABLE_SKU_VALUES = '#__zoo_jbzoo_sku_values';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        self::$ids = $this->getElementsKeys(true);
    }

    /**
     * Create and return self instance
     * @return JBModelSku
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Check columns in SKU table
     * Add new fields if it not exists
     * @deprecated
     */
    public function checkColumns()
    {
        return $this->_jbtables->checkSku();
    }

    /**
     * @param bool $byForce
     * @return array|mixed
     */
    public function getElementsKeys($byForce = false)
    {
        static $loaded;
        if (!isset($loaded) || $byForce === true) {
            $query = $this->_getSelect()
                          ->select('id, element_id')
                          ->from(self::JBZOO_TABLE_SKU_PARAMS);

            $this->_db->setQuery($query);
            self::$ids = $this->_db->loadAssocList('element_id', 'id');
            $loaded    = true;
        }

        return self::$ids;
    }

    /**
     * Get primary key by element_id
     *
     * @param  string $id
     * @return int
     */
    public function getElementKey($id)
    {
        return isset(self::$ids[$id]) ? self::$ids[$id] : false;
    }

    /**
     * Get id of value by value and param_id
     * @param $value
     * @param $param_id
     * @return mixed
     */
    public function getValueId($value, $param_id)
    {
        $select = $this->_getSelect()
                       ->select('id')
                       ->from(self::JBZOO_TABLE_SKU_VALUES)
                       ->where('value_s = ?', $value)
                       ->where('param_id = ?', $param_id)
                       ->limit(1);

        $this->_db->setQuery($select);

        return $this->_db->loadResult();
    }

    /**
     * Get item by sku
     *
     * @param $sku
     *
     * @return mixed|null
     */
    public function getItemIdBySku($sku)
    {
        $sku = JString::trim($sku);

        if (!empty($sku) && isset(self::$ids['_sku'])) {
            $select = $this->_getSelect()
                           ->select('tItem.id')
                           ->from(ZOO_TABLE_ITEM . ' AS tItem')
                           ->innerJoin(ZOO_TABLE_JBZOO_SKU . ' AS tSku ON tSku.item_id = tItem.id')
                           ->where('tSku.param_id = ?', self::$ids['_sku'])
                           ->where('tSku.value = ?', $sku)
                           ->limit(1);

            if ($row = $this->fetchRow($select)) {
                $row = $this->_groupBy($row, 'item_id');
                reset($row);

                return current($row);
            }
        }

        return null;
    }

    /**
     * Update SKU by item
     *
     * @param Item $item
     *
     * @return bool
     */
    public function updateItemSku(Item $item)
    {
        if ($item) {
            $this->checkColumns();

            $elements = $this->app->jbprice->getItemPrices($item);
            if (!empty($elements)) {
                foreach ($elements as $key => $element) {
                    $this->removeByItem($item, $key);
                    $this->_indexPrice($element->getIndexData());
                }
            }
        }

        return false;
    }

    /**
     * Remove columns from #__jbzoo_config when element deleted
     * @param $identifier
     */
    public function removeByElement($identifier)
    {

    }

    /**
     * Remove rows by item
     * @param \Item $item
     * @param       $identifier
     * @return bool
     */
    public function removeByItem(Item $item, $identifier)
    {
        $select = $this->_getSelect()
                       ->select('value_id')
                       ->from(ZOO_TABLE_JBZOO_SKU)
                       ->where('item_id = ?', $item->id)
                       ->where('element_id = ?', $identifier)
                       ->group('value_id');

        $this->_db->setQuery($select);
        $rows = $this->_db->loadAssocList('value_id', 'value_id');

        $select = $this->_getSelect()
                       ->select('value_id')
                       ->from(ZOO_TABLE_JBZOO_SKU)
                       ->group('value_id');

        if (!empty($rows)) {
            foreach ($rows as $id) {
                $select
                    ->clear('where')
                    ->where('value_id = ?', $id)
                    ->where('element_id = ?', $identifier)
                    ->where('item_id <> ?', $item->id);

                $this->_db->setQuery($select);
                $inUse = $this->_db->loadResult();

                unset($rows[$inUse]);
            }

            if (!empty($rows)) {
                if (count($rows) > 0) {
                    $select = $this->_getSelect()
                                   ->delete(self::JBZOO_TABLE_SKU_VALUES)
                                   ->where('id IN(' . implode(',', $rows) . ')');

                    $this->sqlQuery($select);
                }
            }
        }

        $select = $this->_getSelect()
                       ->delete(ZOO_TABLE_JBZOO_SKU)
                       ->where('item_id = ?', $item->id);

        $result = $this->sqlQuery($select);

    }

    /**
     * Save to index table
     *
     * @param array $data
     *
     * @return bool
     */
    public function _indexPrice(array $data)
    {
        if (!empty($data)) {
            foreach ($data as $values) {
                $value_id = $this->getValueId($values['value_s'], self::$ids[$values['param_id']]);

                if (!$value_id) {
                    $value_id = $this->_insert(array(
                        'value_s'  => $values['value_s'],
                        'value_n'  => $values['value_n'],
                        'value_d'  => $values['value_d'],
                        'param_id' => self::$ids[$values['param_id']],
                        'variant'  => $values['variant']
                    ), self::JBZOO_TABLE_SKU_VALUES);
                }

                $eav = array(
                    'item_id'    => $values['item_id'],
                    'element_id' => $values['element_id'],
                    'param_id'   => self::$ids[$values['param_id']],
                    'value_id'   => $value_id,
                    'variant'    => $values['variant']
                );

                $this->_insert($eav, ZOO_TABLE_JBZOO_SKU);

            }

            return true;
        }

        return false;
    }

    /**
     * Insert elements if they are not exists
     * @return $this
     */
    public function updateParams()
    {
        $elements = array_merge(
            $this->app->jbcartelement->getPriceCore(),
            (array)$this->app->jbcartposition->loadParams(JBCart::CONFIG_PRICE),
            (array)$this->app->jbcartposition->loadParams(JBCart::CONFIG_PRICE_TMPL)
        );

        if (!empty($elements[JBCart::ELEMENT_TYPE_PRICE])) {
            foreach ($elements[JBCart::ELEMENT_TYPE_PRICE] as $id => $element) {
                if (!isset(self::$ids[$id]) && $element->isCore()) {
                    $this->_insert(array(
                        'element_id' => $element->identifier
                    ), self::JBZOO_TABLE_SKU_PARAMS);
                }
            }
        }
        $this->getElementsKeys(true);

        return $this;
    }
}