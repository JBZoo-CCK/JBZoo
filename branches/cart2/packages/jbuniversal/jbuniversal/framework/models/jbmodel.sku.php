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
     * Get item by sku
     * @param $sku
     * @return mixed|null
     */
    public function getItemIdBySku($sku)
    {
        $sku = JString::trim($sku);

        if (!empty($sku)) {
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
                $row = $this->_groupBy($row, 'item_id');
                reset($row);

                return current($row);
            }
        }

        return null;
    }

}