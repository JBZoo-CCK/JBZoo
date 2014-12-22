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
 * Class JBModelElementJBPrice
 */
class JBModelElementJBPrice extends JBModelElement
{
    /**
     * @type \JBPriceHelper
     */
    protected $helper;

    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param Element $element
     * @param int     $applicationId
     * @param string  $itemType
     */
    function __construct(Element $element, $applicationId, $itemType)
    {
        parent::__construct($element, $applicationId, $itemType);
        $this->money  = $this->app->jbmoney;
        $this->helper = $this->app->jbprice;
    }

    /**
     * Set OR element conditions
     *
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     *
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, 'AND', $exact);
    }

    /**
     * Set AND element conditions
     *
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     *
     * @return JBDatabaseQuery
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, 'OR', $exact);
    }

    /**
     * @param JBDatabaseQuery $select
     * @param                 $elementId
     * @param                 $values
     * @param string          $logic
     * @param bool            $exact
     *
     * @internal param $value
     * @return array
     */
    protected function _getWhere(JBDatabaseQuery $select, $elementId, $values, $logic = 'AND', $exact = false)
    {
        $data  = $this->_prepareValue($values, $exact);
        if (empty($data)) {
            return null;
        }

        $isFirst = 0;
        $logic   = 'AND';

        $where = $this
            ->_getSelect()
            ->select('tSku.item_id as id')
            ->from(ZOO_TABLE_JBZOO_SKU . ' AS tSku')
            ->where('tSku.element_id = ?', $elementId)
            ->innerJoin(JBModelSku::JBZOO_TABLE_SKU_VALUES . ' AS tValues ON tValues.id = tSku.value_id');

        $iterator = new RecursiveArrayIterator($data);
        foreach ($iterator as $key => $value) {
            $id = array_search($iterator->key(), JBModelSku::$ids);
            if ($isFirst !== 0) {
                $logic = ' OR ';
            }

            $where->where('tSku.param_id = ?', $iterator->key(), $logic);
            if ($iterator->hasChildren()) {

                $children = (array)$iterator->getChildren();
                $inParams = 'AND';
                $first    = key($children);

                foreach ($children as $param_id => $string) {
                    if ($first != $param_id) {
                        $inParams = 'OR';
                    }

                    if ($id == '_value') {
                        $where->where($string, null, $inParams);

                    } elseif ((int)($param_id === 'id')) {
                        $where->where('tSku.value_id = ?', $string, $inParams);

                    } elseif ($this->isDate($string)) {
                        $string = $this->_date($string);
                        $where->where($string, null, $inParams);

                    } elseif ($exact) {
                        $where->where('tValues.value_s = ?', $string, $inParams);

                    } else {
                        $where->where($this->_buildLikeBySpaces($string, 'tValues.value_s'), null, $inParams);
                    }
                }
            }

            $isFirst++;
        }

        $where->group('tSku.item_id');
        if ($isFirst > 0) {
            $where->having('COUNT(tSku.item_id) >= ?', $isFirst);
        }

        $idList = $this->_groupBy($this->fetchAll($where), 'id');
        if (!empty($idList)) {
            return array('tItem.id IN (' . implode(',', $idList) . ')');
        }

        return array('tItem.id IN (0)');
    }

    /**
     * @param array|string $values
     *
     * @param bool         $exact
     * @return mixed|void
     */
    protected function _prepareValue($values, $exact = false)
    {
        $values = $this->unsetEmpty($values);

        $value_id = isset(JBModelSku::$ids['_value']) ? JBModelSku::$ids['_value'] : false;

        if (isset($values[$value_id]) && !empty($values[$value_id])) {
            $values[$value_id] = $this->_processValue($values[$value_id]);
        }

        return $values;
    }

    /**
     * Unset empty
     *
     * @param $values
     * @return array
     */
    protected function unsetEmpty($values)
    {
        if (empty($values)) {
            return $values;
        }
        $result = array();

        $iterator  = new RecursiveArrayIterator($values);
        $recursive = new RecursiveIteratorIterator($iterator);

        foreach ($recursive as $key => $value) {

            $value = JString::trim($value);
            if (!empty($value)) {
                $depth  = $recursive->getDepth();
                $subKey = $recursive->getSubIterator(--$depth)->key();

                if ($subKey != $iterator->key()) {
                    $result[$iterator->key()][$subKey][$key] = $value;
                } else {
                    $result[$iterator->key()][$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $values
     * @return array|mixed
     */
    protected function _processValue($values = array())
    {
        if (count($values)) {

            foreach ($values as $key => $value) {

                if (is_string($value) && $this->isRange($value)) {
                    $range        = $this->_setMinMax($value);
                    $values[$key] = $this->_value($range);

                } elseif ((is_array($value) && (isset($value['range']) && !empty($value['range'])))) {
                    $range        = $this->_setMinMax($value['range']);
                    $values[$key] = $this->_value($range);

                } elseif ((is_array($value)) && (!empty($value['min']) || !empty($value['max']))) {
                    $values[$key] = $this->_value($value);

                } elseif (is_string($value)) {
                    $values[$key] = $this->toSql('tValues.value_n', $value);

                } else {
                    unset($value[$key]);
                }
            }
        }

        return $values;
    }

    /**
     * @param array $values
     * @return array
     */
    protected function _processDate($values = array())
    {
        if (empty($values)) {
            return $values;
        }

        foreach ($values as $key => $value) {
            $value = (array)$value;

            if (!isset($value[0]) && !isset($value[1])) {
                unset($values[$key]);
                continue;
            }

            $values[$key] = array(
                (isset($value[0]) ? $value[0] : '1970-01-01') . ' 00:00:00',
                (isset($value[1]) ? $value[1] : '2099-12-31') . ' 23:59:59'
            );

        }

        return $values;
    }

    /**
     * @param $values
     * @return array
     */
    protected function _value($values)
    {
        $range = array();
        if (isset($values['min'])) {
            $range[] = 'tValues.value_n >= ' . $this->_quote(floor($values['min']));
        }

        if (isset($values['max'])) {
            $range[] = ' tValues.value_n <= ' . $this->_quote(ceil($values['max']));
        }

        return implode(' AND ', $range);
    }

    /**
     * @param $date
     * @return array
     */
    protected function _date($date)
    {
        return array("tValues.value_d BETWEEN"
            . " STR_TO_DATE('" . $date[0] . "', ' % Y -%m -%d % H:%i:%s') AND"
            . " STR_TO_DATE('" . $date[1] . "', ' % Y -%m -%d % H:%i:%s')"
        );
    }

    /**
     * Get min/max value
     *
     * @param array $value
     *
     * @return array $result
     */
    protected function _setMinMax($value)
    {
        list($min, $max) = explode('/', $value);
        $value = array(
            'min' => $min,
            'max' => $max
        );

        return $value;
    }

    /**
     * Check if value is range
     *
     * @param  $value
     * @return bool
     */
    protected function isRange($value)
    {
        if (strpos($value, '/') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Check if string seems like date
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    protected function isDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * @param $column
     * @param $value
     * @return string
     */
    public function toSql($column, $value)
    {
        return $column . ' = ' . $this->_quote($value);
    }
}
