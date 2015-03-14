<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBModelElementDate
 */
class JBModelElementDate extends JBModelElementItemDate
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
        return $this->_getWhere($value);
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
        return $this->_getWhere($value);
    }


    /**
     * Get where conditions
     * @param $values
     * @return array|null
     */
    protected function _getWhere($values)
    {
        if (is_string($values)) {
            $values = array($values);
        }

        if (is_array($values) && !empty($values)) {

            $values = $this->_prepareValue($values);

            $where = array();

            if (is_array($values)) {

                foreach ($values as $value) {

                    $clearElementId = $this->_jbtables->getFieldName($this->_element->identifier, 'd');

                    $where[] = "tIndex." . $clearElementId
                        . " BETWEEN STR_TO_DATE('" . $value[0] . "', '%Y-%m-%d %H:%i:%s')"
                        . " AND STR_TO_DATE('" . $value[1] . "', '%Y-%m-%d %H:%i:%s')";
                }

                return array(implode(' OR ', $where));
            }

        }

        return null;
    }

    /**
     * Prepare an array
     * @param $value
     * @return array
     */
    protected function _prepare($value)
    {
        $result = array();

        if (is_string($value)) {
            $value = array($value);
        }

        if (isset($value['range-date'])) {
            $value = $value['range-date'];

            if (!$result[] = $this->_getDate($value)) {
                return false;
            }

            return $result;
        }

        foreach ($value as $val) {
            if (is_array($val)) {
                if (isset($val['range'])) {
                    $val = $val['range'];
                } else if (isset($val['range-date'])) {
                    $val = $val['range-date'];
                }

                if (!$result[] = $this->_getDate($val)) {
                    return false;
                }
            } else {
                $date     = date(self::DATE_FORMAT, strtotime($val));
                $result[] = array(
                    $date . ' 00:00:00',
                    $date . ' 23:59:59'
                );
            }
        }

        return $result;
    }

    /**
     * @param  array $value
     * @return array
     */
    protected function _getDate($value = array())
    {
        if (empty($value[0]) && empty($value[1])) {
            return false;
        }

        $result = array(
            ($value[0] ? $value[0] : '1970-01-01') . ' 00:00:00',
            ($value[1] ? $value[1] : '2099-12-31') . ' 23:59:59'
        );

        return $result;
    }

}