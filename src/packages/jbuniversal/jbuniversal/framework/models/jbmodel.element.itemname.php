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
 * Class JBModelElementItemname
 */
class JBModelElementItemname extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $exact);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $exact);
    }

    /**
     * @param $value
     * @param $exact
     * @return string
     */
    protected function _getWhere($value, $exact = 0)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $where = array();
        foreach ($value as $valueOne) {
            if ((int)$exact) {
                $where[] = 'tItem.id = ' . (int)$valueOne . ' OR tItem.name = ' . $this->_db->quote($valueOne);
            } else {
                $where[] = 'tItem.id = ' . (int)$valueOne . ' OR ' . $this->_buildLikeBySpaces($this->_prepareValue($valueOne), 'tItem.name');

            }
        }

        return $where;
    }

    /**
     * @param array|string $value
     * @param bool         $exact
     * @return mixed|void
     */
    protected function _prepareValue($value, $exact = false)
    {
        return $value;
    }

}