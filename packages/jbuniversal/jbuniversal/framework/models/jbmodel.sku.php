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
     * Drop JBZoo SKU table
     * @deprecated
     */
    public function dropTable()
    {
        $this->_jbtables->dropTable(ZOO_TABLE_JBZOO_SKU);
    }

    /**
     * Update SKU by item
     * @param Item $item
     * @return bool
     */
    public function updateItemSku(Item $item)
    {
        if ($item) {
            $this->checkColumns();
            $this->removeByItem($item);

            $priceElements = $item->getElementsByType('jbpriceadvance');

            if (!empty($priceElements)) {

                foreach ($priceElements as $element) {
                    $this->_indexPrice($element->getIndexData(true));
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Save to index table
     * @param array $data
     * @return bool
     */
    public function _indexPrice(array $data)
    {
        if (!empty($data)) {

            foreach ($data as $values) {
                $values['params'] = (string)$this->app->data->create($values['params']);
                $this->_insert($values, ZOO_TABLE_JBZOO_SKU);
            }

            return true;
        }

        return false;
    }

    /**
     * Remove rows by item
     */
    public function removeByItem(Item $item)
    {
        $select = $this->_getSelect()
            ->delete(ZOO_TABLE_JBZOO_SKU)
            ->where('item_id = ?', $item->id);

        $this->sqlQuery($select);
    }

    /**
     * Get item by sku
     * @param $sku
     * @return mixed|null
     */
    public function getItemIdBySku($sku)
    {
        $sku = JString::trim($sku);

        if (!empty($sku)) {
            $select = $this->_getSelect()
                ->select('tItem.id')
                ->from(ZOO_TABLE_ITEM . ' AS tItem')
                ->innerJoin(ZOO_TABLE_JBZOO_SKU . ' AS tSku ON tSku.item_id = tItem.id')
                ->where('tSku.sku = ?', $sku)
                ->limit(1);

            if ($row = $this->fetchRow($select)) {
                $row = $this->_groupBy($row, 'item_id');
                reset($row);
                return current($row);
            }
        }

        return null;
    }

}