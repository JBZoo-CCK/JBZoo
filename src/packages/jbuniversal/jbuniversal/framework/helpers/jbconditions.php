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

require_once(JPATH_ROOT . '/media/zoo/applications/jbuniversal/framework/classes/jbrules.php');


/**
 * Class JBConditionsHelper
 */
class JBConditionsHelper extends AppHelper
{   
    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_elements    = $this->app->jbentity->getItemTypesData(false);
    }

    /**
     * @param array $conditions
     * @return null
     */
    public function getValue(array $conditions)
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
                    if (!isset($result[$key][$param_id])) {
                        $result[$key][$param_id] = array();
                    }

                    $result[$key][$param_id] = array_merge($result[$key][$param_id], $data);

                    // $result[$key][$param_id] = $data[$param_id];
                } else {
                    $result[$key] = $data;
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

        $prefixClass  = 'JBZooRule';
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

        if (is_subclass_of($className, ' JBZooRuleJBPrice')) {

            $objField = new $className;
            return $objField->validateElements($key, $param_id, $value);

        } elseif (class_exists($className) && !in_array($element['type'], $similarTypes)) {

            $objField = new $className;
            return $objField->validateValues($key, $value);

        } elseif (in_array($element['type'], $similarTypes)) {

            if ($this->_checkDateElement($element['type'])) {
                $objField = new JBZooRuleItemDate();
            } else {
                $objField = new JBZooRuleText();
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