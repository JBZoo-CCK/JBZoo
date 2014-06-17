<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_BASE . '/modules/mod_jbzoo_item/types/rules.php');

/**
 * Class JBZooModItemCategory
 */
class JBZooModItemConditions extends JBZooItemType
{
    /**
     * @var array
     */
    protected $_elements = array();

    /**
     *
     */
    public function init()
    {
        $this->_elements = $this->_app->jbentity->getItemTypesData(false);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $this->init();
        $items          = array();
        $searchElements = array();
        $appId          = $this->_params->get('condition_app', '0');
        $type           = $this->_params->get('condition_type', 'product');
        $conditions     = $this->_params->get('conditions', array());
        $logic          = $this->_params->get('logic', 'AND');
        $order          = (array)$this->_params->get('order_default');
        $exact          = $this->_params->get('type_search', 0);
        $limit          = $this->_params->get('pages', 20);
        $elements       = $this->_getValue($conditions);

        if (!empty($elements)) {
            foreach ($elements as $fieldKey => $value) {

                if (empty($value)) {
                    continue;
                }

                if (strpos($fieldKey, '_') === false) {

                    $table      = $this->_app->jbtables;
                    $tableIndex = $table->getIndexTable($type);
                    $fields     = $table->getFields($tableIndex);
                    $myFiled    = $table->getFieldName($fieldKey);
                    $elements   = $this->_elements;
                    $element    = $elements[$fieldKey];
                    unset($elements);

                    if (in_array($myFiled, $fields) || $element['type'] == 'textarea') {
                        $searchElements[$fieldKey] = $value;
                    }

                } else {

                    $searchElements[$fieldKey] = $value;
                }

            }
        }
        
        $items = JBModelFilter::model()->search($searchElements, strtoupper($logic), $type, $appId, $exact, 0, $limit, $order);

        return $items;
    }


    /**
     * @param array $conditions
     * @return null
     */
    protected function _getValue(array $conditions)
    {
        $result = null;

        foreach ($conditions as $k => $cond) {

            $cond = (array)$cond;

            if (isset($cond['key']) && !empty($cond['key'])) {

                $key = preg_replace('#[^0-9a-z\_\-]#i', '', $cond['key']);

                $value = $this->_clearValue($cond['value']);
                $conds = $this->_checkRule($key, $value);

                if (empty($conds)) {
                    continue;
                }

                $result[$key][$k] = $conds[$key];

                if ($this->_checkDateElement($this->_elements[$key]['type'])) {

                    if (is_array($result[$key][$k]) && array_key_exists('range', $result[$key][$k])) {
                        if (!$this->_validateDate($result[$key][$k]['range'][0], 'Y-m-d') ||
                            !$this->_validateDate($result[$key][$k]['range'][1], 'Y-m-d')
                        ) {
                            unset($result[$key][$k]);
                        }
                    } elseif (is_array($result[$key][$k]) && array_key_exists('range-date', $result[$key][$k])) {
                        if (!$this->_validateDate($result[$key][$k]['range-date'][0], 'Y-m-d') ||
                            !$this->_validateDate($result[$key][$k]['range-date'][1], 'Y-m-d')
                        ) {
                            unset($result[$key][$k]);
                        }
                    } else {
                        if (!$this->_validateDate($result[$key][$k], 'Y-m-d')) {
                            unset($result[$key][$k]);
                        }
                    }
                }

                if (empty($value) || empty($key) || !$conds) {
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @return array|null|string
     */
    protected function _clearValue($value)
    {
        if (!is_array($value)) {

            $value = strip_tags($value);
            $value = JString::trim($value);

            if (strpos($value, '||')) {
                $value = explode('||', $value);
                $value = $this->_clearValue($value);
                $value = implode('||', $value);
            }

            return $value;
        } else {
            foreach ($value as $key => $val) {

                $value[$key] = JString::trim($val);
            }

            return $value;
        }
    }


    /**
     * @param $key
     * @param $value
     * @return bool
     */
    protected function _checkRule($key, $value)
    {
        $prefixClass  = 'JBZooModItemRule';
        $elements     = $this->_elements;
        $element      = $elements[$key];
        $similarTypes = array(
            'radio',
            'jbcolor',
            'country',
            'checkbox',
            'select',
            'textarea',
            'itemcreated',
            'itemmodified',
            'itempublish_up',
            'itempublish_down'
        );

        unset($elements);

        $className = $prefixClass . $element['type'];

        if (class_exists($className) && !in_array($element['type'], $similarTypes)) {

            $objField = new $className;

            $result = $objField->validateValues($key, $value);

            return $result;

        } elseif (in_array($element['type'], $similarTypes)) {

            if ($this->_checkDateElement($element['type'])) {

                $objField = new JBZooModItemRuleItemDate();
            } else {
                $objField = new JBZooModItemRuleText();
            }

            $result = $objField->validateValues($key, $value);

            return $result;

        } else {

            return false;
        }
    }

    /**
     * @param $date
     * @param string $format
     * @return bool
     */
    protected function _validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * @param $type
     * @return bool
     */
    protected function _checkDateElement($type)
    {
        $dateFormats = array(
            'date',
            'itemcreated',
            'itemmodified',
            'itempublish_up',
            'itempublish_down'
        );

        return in_array($type, $dateFormats, true);
    }
}