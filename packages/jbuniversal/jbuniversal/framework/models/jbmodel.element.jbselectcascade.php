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
 * Class JBModelElementJBSelectCascade
 */
class JBModelElementJBSelectCascade extends JBModelElement
{

    /**
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param array|string $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($elementId, $value, $exact);
    }

    /**
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param array|string $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($elementId, $value, $exact);
    }

    /**
     * Prepare value
     * @param string|array $value
     * @param boolean $exact
     * @return mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $value = array_filter($value);

        return $value;
    }

    /**
     * @param $elementId
     * @param $value
     * @param $exact
     * @return array
     */
    protected function _getItemList($elementId, $value, $exact)
    {
        $values = $this->_prepareValue($value);

        $result = array();

        if (!empty($values)) {
            $indexTable = $this->_jbtables->getIndexTable($this->_itemType);
            $field      = $this->_jbtables->getFieldName($elementId);

            $i = 0;

            foreach ($values as $valueItem) {

                if(is_array($valueItem)) {
                    $valueItem = $valueItem[$i];
                }

                $innerSelect = $this->_getSelect()
                    ->select('DISTINCT tInnerIndex.item_id as id')
                    ->from($indexTable . ' AS tInnerIndex')
                    ->where('tInnerIndex.' . $field . ' = ?', $valueItem);

                $tmpRes = $this->_groupBy($this->fetchAll($innerSelect), 'id');

                if ($exact) {
                    if ($i == 0) {
                        $result = $tmpRes;
                    } else {
                        $result = array_intersect($tmpRes, $result);
                    }

                } else {
                    $result = array_merge($result, $tmpRes);
                }

                $i++;
            }
        }

        $result = array_unique($result);
        return $result;
    }

    /**
     * @param $elementId
     * @param $value
     * @param $exact
     * @return array
     */
    protected function _getWhere($elementId, $value, $exact)
    {
        $idList = $this->_getItemList($elementId, $value, $exact);

        if (!empty($idList)) {
            return array('tItem.id IN (' . implode(',', $idList) . ')');
        }

        return array('tItem.id IN (0)');
    }

}
