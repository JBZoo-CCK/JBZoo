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
 * Class JBModelElementItemname
 */
class JBModelElementItemname extends JBModelElement
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
        return $this->_getWhere($value, $exact);
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
                if ((int)$valueOne > 0) {
                    $where[] = 'tItem.id = ' . (int)$valueOne;
                } else {
                    $where[] = 'tItem.name = ' . $this->_db->quote($valueOne);
                }
            } else {

                if ((int)$valueOne > 0) {
                    $where[] = 'tItem.id = ' . (int)$valueOne;
                } else {
                    $valueOne = $this->_prepareValue($valueOne);
                    $where[]  = $this->_buildLikeBySpaces($valueOne, 'tItem.name');
                }

            }
        }

        return $where;
    }

    /**
     * @param array|string $value
     * @param bool $exact
     * @return mixed|void
     */
    protected function _prepareValue($value, $exact = false)
    {
        return $value;
    }

}