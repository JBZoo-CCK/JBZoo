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
 * Class JBModelElementRange
 */
class JBModelElementRange extends JBModelElement
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
        return $this->_getWhere($value, $elementId);
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
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Prepare value
     * @param array|string $value
     * @param bool $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        $value  = parent::_prepareValue($value);
        $values = array();

        if (!empty($value['range-date']) ||
            !empty($value['range'])
        ) {
            $value = array($value);
        }

        foreach ($value as $val) {

            if ($this->_isDate($val)) {
                $tmp = $val['range-date'];

            } elseif (is_string($val) && strpos($val, '/')) {
                $tmp = explode('/', $val);

            } else {
                if(is_string($val['range']) && strpos($val['range'], '/')) {
                    $tmp = explode('/', $val['range']);

                } else {
                    $tmp = $val['range'];

                }
            }

            if ($this->_isDate($val)) {

                $values[] = array(
                    $this->app->jbdate->toMysql($tmp[0]),
                    $this->app->jbdate->toMysql($tmp[1]),
                );

            } else {

                $values[] = array(
                    JString::trim($tmp[0]),
                    JString::trim($tmp[1])
                );
            }

        }

        return $values;
    }

    /**
     * Check is value is date
     * @param $value
     * @return bool
     */
    protected function _isDate($value)
    {
        return is_array($value) && isset($value['range-date']);
    }

    /**
     * Get where conditions
     * @param $values
     * @param $elementId
     * @return array|null
     */
    protected function _getWhere($values, $elementId)
    {
        if(is_array($values) && !isset($values['range-date'])) {
            foreach($values as $val) {
                $isDate = $this->_isDate($val);
            }
        } else {
            $isDate = $this->_isDate($values);
        }

        $values = $this->_prepareValue($values);

        if (!empty($values)) {
            $where = array();

            foreach ($values as $value) {

                if (strlen($value[0]) == 0 && strlen($value[1]) == 0) {
                    return null;
                }

                $clearElementId = $this->_jbtables->getFieldName($elementId, $isDate ? 'd' : 'n');

                $where[] = 'tIndex.' . $this->_jbtables->getFieldName($elementId, 's') .' IS NOT NULL';

                if ($isDate) {

                    if (!empty($value[0]) && empty($value[1])) {
                        $where[] = "tIndex." . $clearElementId . " >= STR_TO_DATE('" . $value[0] . "', '%Y-%m-%d %H:%i:%s')";

                    } elseif (empty($value[0]) && !empty($value[1])) {
                        $where[] = "tIndex." . $clearElementId . " <= STR_TO_DATE('" . $value[1] . "', '%Y-%m-%d %H:%i:%s')";

                    } else {
                        $where[] = "tIndex." . $clearElementId
                            . " BETWEEN STR_TO_DATE('" . $value[0] . "', '%Y-%m-%d %H:%i:%s')"
                            . " AND STR_TO_DATE('" . $value[1] . "', '%Y-%m-%d %H:%i:%s')";
                    }

                } else {

                    if (strlen($value[0]) != 0) {
                        $where[] = 'tIndex.' . $clearElementId . ' >= ' . (float)$value[0];
                    }

                    if (strlen($value[1]) != 0) {
                        $where[] = 'tIndex.' . $clearElementId . ' <= ' . (float)$value[1];
                    }
                }
            }

            return $where;
        }

        return null;
    }
}
