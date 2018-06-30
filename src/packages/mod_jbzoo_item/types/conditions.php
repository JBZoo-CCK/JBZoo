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
     * @type int
     */
    protected $_app_id = 0;

    /**
     * @type string
     */
    protected $_type = null;

    /**
     * Init vars
     */
    public function init()
    {
        $this->_elements = $this->app->jbentity->getItemTypesData(false);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $this->init();
        $searchElements = array();
        $this->_app_id  = $this->_params->get('condition_app', '0');
        $this->_type    = $this->_params->get('condition_type', 'product');
        $conditions     = (array)$this->_params->get('conditions', array());
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

                    $table      = $this->app->jbtables;
                    $tableIndex = $table->getIndexTable($this->_type);
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

        $items = JBModelFilter::model()->search(
            $searchElements, strtoupper($logic), $this->_type, $this->_app_id,
            $exact, 0, $limit, $order);

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

                $key      = preg_replace('#[^0-9a-z\_\-]#i', '', $cond['key']);
                $param_id = null;

                if (strpos($key, '__')) {
                    list($key, $param_id) = explode('__', $key);
                }

                $value = $this->_clearValue($cond['value']);
                $conds = $this->_checkRule($key, $value, $param_id);

                if (empty($conds)) {
                    continue;
                }

                $data = $conds[$key];
                if (isset($param_id)) {
                    $result[$key][$param_id][$k] = $data[$param_id];

                } else {
                    $result[$key][$k] = $data;
                }

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
     * @param $param_id
     * @return bool
     */
    protected function _checkRule($key, $value, $param_id = null)
    {
        if (!isset($this->_elements[$key])) {
            return false;
        }

        $prefixClass  = 'JBZooModItemRule';
        $element      = $this->_elements[$key];
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

        $className = $prefixClass . $element['type'];

        if (is_subclass_of($className, 'JBZooModItemRuleJBPrice')) {

            $objField = new $className;
            return $objField->validateElements($key, $param_id, $value);

        } elseif (class_exists($className) && !in_array($element['type'], $similarTypes)) {

            $objField = new $className;
            return $objField->validateValues($key, $value);

        } elseif (in_array($element['type'], $similarTypes)) {

            if ($this->_checkDateElement($element['type'])) {
                $objField = new JBZooModItemRuleItemDate();
            } else {
                $objField = new JBZooModItemRuleText();
            }

            return $objField->validateValues($key, $value);
        }

        return false;
    }

    /**
     * @param string $date
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