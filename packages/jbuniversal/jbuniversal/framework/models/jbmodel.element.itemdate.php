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
 * Class JBModelElementItemDate
 */
class JBModelElementItemDate extends JBModelElement
{

    const DATE_FORMAT = 'Y-m-d';

    protected $_fieldname = 'date';

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
        $value = $this->_prepareValue($value);

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
        $value = $this->_prepareValue($value);

        return $this->_getWhere($value);
    }

    /**
     * Get conditions for search
     * @param string|array $value
     * @return array
     */
    protected function _getWhere($value)
    {
        $result = array();

        if (is_array($value)) {
            foreach ($value as $val) {

                if (empty($val[0]) || empty($val[1])) {
                    return null;
                }

                $result[] = "tItem." . $this->_fieldname
                    . " BETWEEN STR_TO_DATE('" . $val[0] . "', '%Y-%m-%d %H:%i:%s')"
                    . " AND STR_TO_DATE('" . $val[1] . "', '%Y-%m-%d %H:%i:%s')";
            }
        }

        return array(implode(' OR ', $result));
    }

    /**
     * Prepare and validate value
     * @param array|string $value
     * @param bool $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        $result = $this->_prepare($value);

        return $result;
    }

    /**
     * Prepare an array
     * @param $value
     * @return array
     */
    protected function _prepare($value)
    {
        $result = array();

        if(is_string($value)) {
            $value = array($value);
        }

        if (isset($value['range-date'])) {
            $value = $value['range-date'];

            $result[] = $this->_getDate($value);

            return $result;
        }

        foreach ($value as $val) {
            if (is_array($val)) {
                if (isset($val['range'])) {
                    $val = $val['range'];
                } else if(isset($val['range-date'])) {
                    $val = $val['range-date'];
                }

                $result[] = $this->_getDate($val);
            } else {
                $date = date(self::DATE_FORMAT, strtotime($val));
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
        $result = array(
            ($value[0] ? $value[0] : '1970-01-01') . ' 00:00:00',
            ($value[1] ? $value[1] : '2099-12-31') . ' 23:59:59'
        );

        return $result;
    }


}