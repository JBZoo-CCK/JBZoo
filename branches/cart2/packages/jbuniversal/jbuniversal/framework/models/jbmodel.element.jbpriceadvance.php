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
 * Class JBModelElementJBPriceAdvance
 */
class JBModelElementJBPriceAdvance extends JBModelElement
{

    protected $_defaultCurrency = 'EUR';

    /**
     * @param Element $element
     * @param int $applicationId
     * @param string $itemType
     */
    function __construct(Element $element, $applicationId, $itemType)
    {
        parent::__construct($element, $applicationId, $itemType);
        $this->_defaultCurrency = $this->_config->get('currency_default', 'EUR');
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, $exact);
    }

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($select, $elementId, $value, $exact);
    }

    /**
     * @param JBDatabaseQuery $select
     * @param $elementId
     * @param $value
     * @param bool $exact
     * @return array
     */
    protected function _getWhere(JBDatabaseQuery $select, $elementId, $value, $exact = false)
    {
        $value = $this->_prepareValue($value, $exact);

        $isUse = false;
        $where = array();
        $i     = 0;

        foreach ($value as $val) {

            // by SKU value
            if (!empty($val['sku'])) {
                $isUse = true;
                if ($exact) {
                    $where[] = 'tSku.sku = ' . $this->_quote($val['sku']);
                } else {
                    $where[] = $this->_buildLikeBySpaces($val['sku'], 'tSku.sku');
                }
            }

            // by balance
            if (!empty($val['balance'])) {
                $isUse   = true;
                $where[] = 'tSku.balance <> 0';
            }

            if (!empty($val['new'])) {
                $isUse   = true;
                $where[] = 'tSku.is_new = 1';
            }

            if (!empty($val['hit'])) {
                $isUse   = true;
                $where[] = 'tSku.is_hit = 1';
            }

            if (!empty($val['sale'])) {
                $isUse   = true;
                $where[] = 'tSku.is_sale = 1';
            }

            if (!empty($val['val']) || !empty($val['val_min']) || !empty($val['val_max']) || !empty($val['range'])) {
                $isUse   = true;
                $where[] = '(' . implode(' AND ', $this->_conditionValue($val)) . ')';
            }

            if ($isUse && $i <= 0) {
                $select->where('tSku.element_id = ?', $elementId);
                $i++;
            }
        }

        return array(implode(' AND ', $where));
    }

    /**
     * @param $value
     * @return array
     */
    protected function _conditionValue($value)
    {
        $jbmoney = $this->app->jbmoney;
        $valType = (int)$value['val_type'];
        $where   = array();

        if (!empty($value['val'])) {

            $val = $jbmoney->convert($value['currency'], $this->_defaultCurrency, $value['val']);

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

            $min = floor($jbmoney->convert($value['currency'], $this->_defaultCurrency, $min));
            $max = ceil($jbmoney->convert($value['currency'], $this->_defaultCurrency, $max));

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

        $priceType = (int)$value['price_type'];
        if ($priceType == 1) {
            $where[] = 'tSku.type = 1';

        } else if ($priceType == 2) {
            $where[] = 'tSku.type = 2';
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
        if (is_array($value)) {

            $data   = array();
            $result = array(
                'sku'        => '',
                'balance'    => '',
                'sale'       => '',
                'new'        => '',
                'hit'        => '',
                'val'        => '',
                'val_min'    => '',
                'val_max'    => '',
                'range'      => '',
                'currency'   => '',
                'val_type'   => 0,
                'price_type' => 0,
            );

            if (isset($value['range']) ||
                isset($value['val']) ||
                isset($value['val_min']) ||
                isset($value['currency']) ||
                isset($value['val_max'])
            ) {
                $value = $this->_setMinMax($value);

                return array(array_merge($result, $value));
            }

            foreach ($value as $val) {
                $val    = $this->_setMinMax($val);
                $data[] = array_merge($result, $val);
            }

            return $data;
        }

        return parent::_prepareValue($value, $exact);
    }


    /**
     * Get min/max value
     * @param array $value
     * @return array $result
     */
    protected function _setMinMax($value)
    {
        if (isset($value['range'])) {

            if (strpos($value['range'], '/') !== false) {
                list($min, $max) = explode('/', $value['range']);

                $value['val_min'] = $min;
                $value['val_max'] = $max;
            }

        }

        return $value;
    }

}
