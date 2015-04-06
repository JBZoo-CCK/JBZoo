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
     * Get the unique id(primary key) for element
     * @param string $id Identifier of price element
     * @return int|bool
     */
    public function getId($id)
    {
        return $id;
    }

    /**
     * Get id of value by value and param_id
     * @param $value
     * @param $param_id
     * @param $element_id
     * @return mixed
     */
    public function getValueId($value, $element_id, $param_id)
    {
        $select = $this->_getSelect()
                       ->select('id')
                       ->from(ZOO_TABLE_JBZOO_SKU)
                       ->where('value_s = ?', $value)
                       ->where('element_id = ?', $element_id)
                       ->where('param_id = ?', $param_id)
                       ->limit(1);

        $this->_db->setQuery($select);

        return $this->_db->loadResult();
    }

    /**
     * Get item id by sku
     * @param string $sku
     * @return mixed|null
     */
    public function getItemIdBySku($sku)
    {
        $sku = JString::trim($sku);
        if ($sku !== '') {
            $select = $this
                ->_getSelect()
                ->clear('select')
                ->select('tItem.id')
                ->from(ZOO_TABLE_ITEM . ' AS tItem')
                ->innerJoin(ZOO_TABLE_JBZOO_SKU . ' AS tSku ON tSku.item_id = tItem.id')
                ->where('tSku.param_id = \'_sku\'')
                ->where('tSku.value_s = ?', $sku)
                ->limit(1);

            if ($row = $this->fetchRow($select)) {
                $row = $this->_groupBy((array)$row, 'item_id');
                reset($row);

                return current($row);
            }
        }

        return null;
    }

    /**
     * Save to index table
     * @param array $data
     * @return bool
     */
    public function _indexPrice(array $data)
    {
        if (count($data)) {
            foreach ($data as $values) {

                $eav = array(
                    'value_s'    => $values['value_s'],
                    'value_n'    => $values['value_n'],
                    'value_d'    => $values['value_d'],
                    'item_id'    => $values['item_id'],
                    'element_id' => $values['element_id'],
                    'param_id'   => $values['param_id'],
                    'variant'    => $values['variant']
                );

                $this->_insert($eav, ZOO_TABLE_JBZOO_SKU);
            }

            return true;
        }

        return false;
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
            $elements = $this->app->jbprice->getItemPrices($item);
            if (!empty($elements)) {
                foreach ($elements as $key => $element) {
                    //$this->removeByItem($item, $key);
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
     * @param Item  $item
     * @param       $identifier
     * @return bool
     */
    public function removeByItem(Item $item, $identifier)
    {
        return false;
    }

    /**
     * Insert elements if they are not exists
     * @return $this
     */
    public function updateParams()
    {
        return $this;
    }
}