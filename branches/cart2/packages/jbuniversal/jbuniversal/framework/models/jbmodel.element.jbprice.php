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
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param Element $element
     * @param int     $applicationId
     * @param string  $itemType
     */
    function __construct(Element $element, $applicationId, $itemType)
    {
        parent::__construct($element, $applicationId, $itemType);
        $this->money = $this->app->jbmoney;
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
        $data = $this->_processing($values, $exact);

        $where   = array();
        $isFirst = true;
        $type    = null;

        $innerSelect = $this
            ->_getSelect()
            ->select('DISTINCT tSku.item_id as id')
            ->from(ZOO_TABLE_JBZOO_SKU . ' AS tSku')
            ->where('tSku.element_id = ?', $elementId);

        foreach ($data as $id => $values) {
            if ($element = $this->_element->getElement($id)) {
                $type = $element->getElementType();
            }

            $innerWhere = array();
            foreach ($values as $key => $value) {

                if (is_null($value)) {
                    continue;
                }
                if (is_string($value)) {

                    $value = JString::trim($value);
                    if (JString::strlen($value) === 0) {
                        continue;
                    }
                }

                if ($id == '_value') {
                    $innerWhere[$key] = implode(' AND ', $this->_value($value));

                } elseif ($type == 'date') {
                    $innerWhere[$key] = implode(' AND ', $this->_date($value));

                } else {
                    $innerWhere[$key] = 'tSku.value_s = ' . $this->_quote($value);

                }
            }

            if (!empty($innerWhere)) {

                $innerWhere = array(' AND (' . implode(") $logic (", $innerWhere) . ')'); // between the elements
                array_unshift($innerWhere, 'tSku.param_id = ' . $this->_quote($id));

                $where[] = ($isFirst !== true ? " $logic " : null) . "(" . implode($innerWhere) . ')';
                $isFirst = false;
            }
        }

        if (!empty($where)) {

            $innerSelect->where(implode($where));
            $idList = $this->_groupBy($this->fetchAll($innerSelect), 'id');
            if (!empty($idList)) {
                return array('tItem.id IN (' . implode(',', $idList) . ')');
            }
        }

        return array('tItem.id IN (0)');
    }

    /**
     * @param array|string $values
     *
     * @return mixed|void
     */
    protected function _processing($values)
    {
        $elms = $this->_element->getElementsByType('date');
        $date = reset($elms);

        if (isset($values['_value'])) {
            $values['_value'] = $this->_processValue($values['_value']);
        }

        if ($date && isset($values[$date->identifier])) {
            $uuid          = $date->identifier;
            $values[$uuid] = $this->_processDate($values[$uuid]);
        }

        return $values;
    }

    /**
     * @param array $values
     * @return array|mixed
     */
    protected function _processValue($values = array())
    {
        if (empty($values)) {
            return $values;
        }

        if (count($values)) {
            foreach ($values as $key => $value) {
                $values[$key] = $this->_setMinMax($value);
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
            $value        = (array)$value;
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
        return array(
            'tSku.value_n >= ' . $this->_quote($values['min']),
            'tSku.value_n <= ' . $this->_quote($values['max'])
        );
    }

    /**
     * @param $date
     * @return array
     */
    protected function _date($date)
    {
        return array("tSku.value_d BETWEEN"
            . " STR_TO_DATE('" . $date[0] . "', '%Y-%m-%d %H:%i:%s') AND"
            . " STR_TO_DATE('" . $date[1] . "', '%Y-%m-%d %H:%i:%s')"
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
        if (isset($value['range'])) {

            if (strpos($value['range'], '/') !== false) {
                list($min, $max) = explode('/', $value['range']);

                $value['min'] = $min;
                $value['max'] = $max;
            }

        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function _conditionValue($value)
    {
        /*
        $jbmoney = $this->app->jbmoney;
        $where   = array();
        $valType = 0;
        if (!empty($value['val'])) {

            $val = 0;

            $min = floor($val);
            $max = ceil($val);

            if ($valType == 1) {

                $where[] = 'tSku.price >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.price <= ' . $this->_quote($max);
                }

            } else if ($valType == 2) {

                $where[] = 'tSku.total >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.total <= ' . $this->_quote($max);
                }

            } else {

                $where[] = 'tSku.price >= ' . $this->_quote($min);
                $where[] = 'tSku.total >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.price <= ' . $this->_quote($max);
                    $where[] = 'tSku.total <= ' . $this->_quote($max);
                }
            }
        }

        if (!empty($value['val_min']) || !empty($value['val_max']) || !empty($value['range'])) {

            if (!empty($value['range'])) {
                list($min, $max) = explode('/', $value['range']);
            } else {
                $min = $value['val_min'];
                $max = $value['val_max'];
            }

            //$min = floor($jbmoney->convert($value['currency'], $this->_defaultCurrency, $min));
            //$max = ceil($jbmoney->convert($value['currency'], $this->_defaultCurrency, $max));

            if ($valType == 1) {
                $where[] = 'tSku.price >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.price <= ' . $this->_quote($max);
                }

            } else if ($valType == 2) {
                $where[] = 'tSku.total >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.total <= ' . $this->_quote($max);
                }

            } else {
                $where[] = 'tSku.price >= ' . $this->_quote($min);
                $where[] = 'tSku.total >= ' . $this->_quote($min);
                if ($max > 0) {
                    $where[] = 'tSku.price <= ' . $this->_quote($max);
                    $where[] = 'tSku.total <= ' . $this->_quote($max);
                }
            }
        }

        return $where;
        */
    }

}
