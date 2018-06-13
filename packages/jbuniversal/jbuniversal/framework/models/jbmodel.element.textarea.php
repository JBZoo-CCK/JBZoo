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
 * Class JBModelElementItemtag
 */
class JBModelElementTextarea extends JBModelElement
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
        return $this->_getWhere($value, $elementId, $exact);
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
        return $this->_getWhere($value, $elementId, $exact);
    }

    /**
     * Get conditions for search
     * @param $value
     * @param $elementId
     * @param $exact
     * @return array
     */
    protected function _getWhere($value, $elementId, $exact = false)
    {
        $values = $this->_prepareValue($value);

        $elementCondition = 'tZooIndex.element_id = ' . $this->_quote($elementId);

        $result = array();
        foreach ($values as $value) {
            $result[] = '( (' . $elementCondition . ') AND (' . $this->_buildLikeBySpaces($value, 'tZooIndex.value') . ') )';
        }

        if (!empty($result)) {
            return array('(' . implode(' OR ', $result) . ')');
        }

        return array('tItem.id IN (0)');
    }

    /**
     * @param array|string $value
     * @param bool $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        return $value;
    }

}
