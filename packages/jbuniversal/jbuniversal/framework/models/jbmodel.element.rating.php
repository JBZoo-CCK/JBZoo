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
 * Class JBModelElementRating
 */
class JBModelElementRating extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value);
    }

    /**
     * Prepare and validate value
     * @param array|string $value
     * @param bool $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        $values    = explode('/', $value);
        $values[0] = (int)trim($values[0]);
        $values[1] = (int)trim($values[1]);

        return $values;
    }

    /**
     * Get conditions for search
     * @param string|array $value
     * @return string
     */
    protected function _getWhere($value)
    {
        $value = $this->_prepareValue($value);

        if ($value[0] == 0 && $this->_config->get('stars') == $value[1]) {
            return array();
        }

        $select = $this->_getItemSelect()
            ->clear('select')
            ->select('tItem.id AS id')
            ->innerJoin(ZOO_TABLE_RATING . ' AS tRating ON tRating.item_id = tItem.id')
            ->where('element_id = ?', $this->_identifier)
            ->group('tItem.id')
            ->having('AVG(value) >= ?', $value[0])
            ->having('AVG(value) <= ?', $value[1]);

        $idList = $this->fetchList($select);

        if (!empty($idList)) {
            return array('tItem.id IN (' . implode(',', $idList) . ')');
        }

        return array('tItem.id IN (0)');
    }

}
