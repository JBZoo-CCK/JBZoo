<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
     * @type JBPriceHelper
     */
    protected $helper;

    /**
     * @type JBMoneyHelper
     */
    protected $money;

    /**
     * @type string
     */
    protected $_currency;

    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param Element $element
     * @param int     $applicationId
     * @param string  $itemType
     */
    public function __construct(Element $element, $applicationId, $itemType)
    {
        parent::__construct($element, $applicationId, $itemType);

        $this->money  = $this->app->jbmoney;
        $this->helper = $this->app->jbprice;

        $this->_currency = JBModelConfig::model()->getCurrency();
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, 'AND', $exact);
    }

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return JBDatabaseQuery
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, 'OR', $exact);
    }

    /**
     * @param JBDatabaseQuery $select
     * @param                 $elementId
     * @param                 $data
     * @param string          $logic
     * @param bool            $exact
     * @internal param $value
     * @return array
     */
    protected function _getWhere(JBDatabaseQuery $select, $elementId, $data, $logic = 'AND', $exact = false)
    {
        $data = $this->_prepareValue($data, $exact);
        if (empty($data)) {
            return null;
        }

        $isFirst  = 0;
        $logic    = 'AND';
        $all      = array();
        $iterator = new RecursiveArrayIterator($data);
        $where    = $this->_getSelect();
        foreach ($iterator as $identifier => $_value) {
            $where
                ->clear()
                ->select('tSku.item_id as id')
                ->from(ZOO_TABLE_JBZOO_SKU . ' AS tSku')
                ->where('tSku.element_id = ?', $elementId);

            if ($isFirst !== 0) {
                $logic = ' OR ';
            }
            $where->where('tSku.param_id = ?', $iterator->key(), $logic);

            if ($_value !== null && $_value !== '') {
                $_value   = (array)$_value;
                $inParams = 'AND';
                $first    = key($_value);

                foreach ($_value as $key => $val) {
                    if ($first != $key) {
                        $inParams = 'OR';
                    }
                    $this->_where($val, $where, $identifier, $key, $inParams, $exact);
                }
            }
            $where->group('tSku.item_id');
            $all[] = '(' . $where->__toString() . ')';
        }

        $query = $this
            ->_getSelect()
            ->clear()
            ->select('tAll.id')
            ->from('(' . implode('UNION ALL', $all) . ') as tAll')
            ->group('tAll.id')
            ->having('COUNT(tAll.id) = ?', count($all));

        $idList = $this->_groupBy($this->fetchAll($query), 'id');

        if (!empty($idList)) {
            return array('tItem.id IN (' . implode(',', $idList) . ')');
        }

        return array('tItem.id IN (0)');
    }

    /**
     * @param        $value
     * @param        $where
     * @param        $id
     * @param        $key
     * @param string $logic
     * @param bool   $exact
     */
    protected function _where($value, &$where, $id, $key, $logic = 'AND', $exact = false)
    {
        if ($id == '_value' || $id == '_balance' || $id == '_discount') {
            $where->where('tSku.variant = ?', -1);
        }

        if ($id == '_value') {
            $where->where($value, null, $logic);

        } elseif ($id == '_sku') {
            $where->where($this->_buildLikeBySpaces($value, 'tSku.value_s'), null, $logic);

        } elseif ((int)($key === 'id')) {
            $value = (array)$value;

            array_walk($value, function ($v) use (&$where, &$logic) {
                $where->where('tSku.value_s = ?', $v, $logic);
                $logic = 'OR';
            });
        } elseif (is_array($value) && (isset($value['id']) && !empty($value['id']))) {
            $where->where('tSku.value_s = ?', $value['id'], $logic);

        } elseif ($this->isDate($value)) {
            $value = $this->_date($value);
            $where->where($value, null, $logic);

        } elseif ($this->isNumeric($value)) {
            $where->where('tSku.value_n = ?', $value, $logic);

        } elseif ($exact) {
            $where->where('tSku.value_s = ?', $value, $logic);

        } else {
            $where->where($this->_buildLikeBySpaces($value, 'tSku.value_s'), null, $logic);
        }
    }

    /**
     * @param array|string $values
     * @param bool         $exact
     * @return mixed|void
     */
    protected function _prepareValue($values, $exact = false)
    {
        $values = $this->unsetEmpty($values);

        if (array_key_exists('_value', $values)) {
            $values['_value'] = $this->_processValue((array)$values['_value']);
        }

        return $values;
    }

    /**
     * Unset empty
     * @param $values
     * @return array
     */
    protected function unsetEmpty($values)
    {
        if (empty($values)) {
            return $values;
        }
        $values = array_filter($values, function ($value) {
            return ($value !== null && $value !== '');
        });
        $result = array();

        $iterator  = new RecursiveArrayIterator($values);
        $recursive = new RecursiveIteratorIterator($iterator);
        foreach ($recursive as $key => $value) {
            $value = JString::trim($value);
            if ($value !== '' && $value !== null) {
                $depth = $recursive->getDepth();
                $depth--;

                $subKey      = null;
                $subIterator = $recursive->getSubIterator($depth);
                if ($subIterator) {
                    $subKey = $subIterator->key();
                }

                if ($subKey && $subKey != $iterator->key()) {
                    $result[$iterator->key()][$subKey][$key] = $value;

                } elseif ($iterator->key() != $key) {
                    $result[$iterator->key()][$key] = $value;

                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param array|string $values
     * @return array|mixed
     */
    protected function _processValue($values = array())
    {
        if (count($values)) {
            $currency = null;
            if (is_array($values) && array_key_exists('currency', $values)) {
                $currency = $values['currency'];
                unset($values['currency']);
            }

            if (is_string($values) && $this->isRange($values)) {
                $range  = $this->_setMinMax($values);
                $values = $this->_value($range, $currency);

            } elseif ((is_array($values) && (isset($values['range']) && !empty($values['range'])))) {
                $range  = $this->_setMinMax($values['range']);
                $values = $this->_value($range, $currency);

            } elseif ((is_array($values)) && (!empty($values['min']) || !empty($values['max']))) {
                $values = $this->_value($values, $currency);

            } elseif (is_string($values) && $values !== '') {
                $values = $this->_value(array(
                    'min' => $values,
                    'max' => $values,
                ), $currency);
            } elseif (is_array($values)) {
                foreach ($values as $key => $value) {
                    $values[$key] = $this->_processValue($value);
                }
            } else {
                $values = null;
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
                (isset($value[1]) ? $value[1] : '2099-12-31') . ' 23:59:59',
            );

        }

        return $values;
    }

    /**
     * @param  array  $values
     * @param  string $currency
     * @return array
     */
    protected function _value($values, $currency = null)
    {
        $range = array();

        if (isset($values['min'])) {
            $min     = JBCart::val($values['min'], $currency)->val($this->_currency);
            $range[] = 'tSku.value_n >= ' . $this->_quote($min);
        }

        if (isset($values['max'])) {
            $max     = JBCart::val($values['max'], $currency)->val($this->_currency);
            $range[] = ' tSku.value_n <= ' . $this->_quote($max);
        }

        return implode(' AND ', $range);
    }

    /**
     * @param $date
     * @return array
     */
    protected function _date($date)
    {
        return array('tSku.value_d BETWEEN'
            . ' STR_TO_DATE(\'' . $date[0] . '\', \' % Y -%m -%d % H:%i:%s\') AND'
            . ' STR_TO_DATE(\'' . $date[1] . '\', \' % Y -%m -%d % H:%i:%s\')',
        );
    }

    /**
     * Get min/max value
     * @param array $value
     * @return array $result
     */
    protected function _setMinMax($value)
    {
        list($min, $max) = explode('/', $value);
        $value = array(
            'min' => $min,
            'max' => $max,
        );

        return $value;
    }

    /**
     * Check if value is range
     * @param  $value
     * @return bool
     */
    protected function isRange($value)
    {
        return strpos($value, '/') !== false;
    }

    /**
     * Check if string seems like date
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
     * @param $value
     * @return mixed
     */
    protected function isNumeric($value)
    {
        return $this->app->jbprice->isNumeric($value);
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
