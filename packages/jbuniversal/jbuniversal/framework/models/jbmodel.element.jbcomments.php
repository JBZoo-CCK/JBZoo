<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBModelElementJBComments
 */
class JBModelElementJBComments extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhereLike($select, $elementId, $value, $exact);
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
        return $this->_getWhereLike($select, $elementId, $value, $exact);
    }

    /**
     * @param JBDatabaseQuery $select
     * @param $elementId
     * @param $value
     * @param $exact
     * @return array
     */
    public function _getWhereLike(JBDatabaseQuery $select, $elementId, $value, $exact)
    {
        $values = $this->_prepareValue($value, $exact);

        if (empty($values) && is_array($values)) {
            return array();
        } elseif ($values === null) {
            return array('tItem.id IN (0)');
        }

        $conditions = array();
        foreach ($values as $valueOne) {
            $conditions[] = $this->_buildLikeBySpaces($valueOne, 'content') . PHP_EOL;
        }

        $where = ' (' . implode(' OR ', $conditions) . ') ';

        $subSelect = $this->_getSelect()
            ->select(array('comm.item_id AS id'))
            ->from(ZOO_TABLE_COMMENT . ' AS comm')
            ->where($where);

        $rows = $this->fetchList($subSelect);

        if (!empty($rows)) {
            return array('tItem.id IN (' . implode(',', $rows) . ')');
        }

        return array('tItem.id IN (0)');
    }

}
