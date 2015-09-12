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
 * Class JBElement
 */
class JBCSVItem
{
    const SEP_ROWS = '///';
    const SEP_CELL = '|||';

    /**
     * @var App
     */
    public $app = null;

    /**
     * @var Element
     */
    protected $_element = null;

    /**
     * @var string
     */
    protected $_identifier = null;

    /**
     * @var Item
     */
    protected $_item = null;

    /**
     * @var mixed
     */
    protected $_value = null;

    /**
     * @var bool
     */
    protected $_isRepeatable = false;

    /**
     * @var string
     */
    protected $_autoType = 'string';

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var JSonData
     */
    protected $_exportParams;

    /**
     * @var JSonData
     */
    protected $_importParams;

    /**
     * @var JSonData
     */
    protected $_lastImportParams;

    /**
     * Constructor
     * @param Element|String $element
     * @param Item           $item
     * @param array          $options
     */
    function __construct($element, Item $item = null, $options = array())
    {
        // get Zoo app
        $this->app = App::getInstance('zoo');
        // set inner vars
        if (isset($options['elementId'])) {
            $this->_identifier = $options['elementId'];
            $this->_item       = $item;
            $this->_element    = $this->_item->getElement($this->_identifier);

        } else if (is_object($element)) {
            $this->_element    = $element;
            $this->_item       = $element->getItem();
            $this->_identifier = $element->identifier;

        } else {
            $this->_identifier = $element;
        }

        // set repeatable flag
        if (is_object($this->_element) && is_subclass_of($this->_element, 'ElementRepeatable')) {
            $this->_isRepeatable = true;
        }

        // set Item object
        if (!$this->_item && $item) {
            $this->_item = $item;
        }

        // get current value for export)
        if ($this->_item &&
            $this->_identifier &&
            isset($this->_item->elements[$this->_identifier])
        ) {
            $this->_value = $this->_item->elements[$this->_identifier];
        }

        $this->_config = JBModelConfig::model();

        $this->_exportParams     = $this->_config->getGroup('export.items', array());
        $this->_importParams     = $this->_config->getGroup('import.items', array());
        $this->_lastImportParams = $this->_config->getGroup('import.last.items', array());
    }

    /**
     * Export data to CSV cell
     * @return string
     */
    public function toCSV()
    {
        if ($this->_element) {
            if ($this->_isRepeatable) {

                // for repeatable objects
                $result = array();
                if (isset($this->_item->elements[$this->_identifier])) {
                    foreach ($this->_item->elements[$this->_identifier] as $self) {
                        $result[] = isset($self['value']) ? $self['value'] : null;
                    }
                }

                if ((int)$this->_exportParams->get('merge_repeatable')) {
                    return implode(JBCSVItem::SEP_ROWS, array_filter($result));
                } else {
                    return $result;
                }

            } else {
                // for no repeatable objects
                if (isset($this->_item->elements[$this->_identifier]['value'])) {
                    return $this->_item->elements[$this->_identifier]['value'];
                }
            }
        }

        return '';
    }

    /**
     * Import data from CSV cell
     * @param      $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        $value = $this->_getAutoClean($value);

        if ($this->_element) {

            if ($this->_isRepeatable) {
                // for repeatable objects

                if (strpos($value, JBCSVItem::SEP_ROWS)) {
                    $tmpData = $this->_getArray($value, JBCSVItem::SEP_ROWS);
                    foreach ($tmpData as $val) {

                        if ($val === '') {
                            return $this->_item;
                        }

                        $data[] = array('value' => $val);
                    }

                    $this->_element->bindData($data);
                } else {

                    if ($value === '') {
                        return $this->_item;
                    }

                    $data   = ($position == 1) ? array() : $data = $this->_element->data();
                    $data[] = array('value' => JString::trim($value));

                    $this->_element->bindData($data);
                }
            } else {

                if ($value === '') {
                    return $this->_item;
                }
                // for no repeatable objects
                $this->_element->bindData(array('value' => $value));
            }

        }

        return $this->_item;
    }

    /**
     * Get string after autoclean
     * @param string $value
     * @return array|int|string
     */
    protected function _getAutoClean($value)
    {
        if ($this->_autoType == 'string') {
            $value = $this->_getString($value);

        } else if ($this->_autoType == 'int') {
            $value = $this->_getInt($value);

        } else if ($this->_autoType == 'bool') {
            $value = $this->_getBool($value);

        } else if ($this->_autoType == 'date') {
            $value = $this->_getDate($value);

        } else if ($this->_autoType == 'alias') {
            $value = $this->_getAlias($value);

        } else if ($this->_autoType == 'array-cell') {
            $value = $this->_getArray($value, self::SEP_CELL);

        } else if ($this->_autoType == 'array-rows') {
            $value = $this->_getArray($value, self::SEP_ROWS);

        } else if ($this->_autoType == 'array-simple') {
            $value = $this->_getArray($value, 'simple');
        }

        return $value;
    }

    /**
     * Get bool value from CSV
     * @param string $value
     * @return int
     */
    protected function _getBool($value)
    {
        return (int)$this->app->jbvars->bool($value);
    }

    /**
     * Get int value
     * @param string $value
     * @return int
     */
    protected function _getInt($value)
    {
        return (int)$this->_getString($value);
    }

    /**
     * Get int value
     * @param string $value
     * @return int
     */
    protected function _getFloat($value)
    {
        $value = (float)$this->app->jbmoney->clearValue($value);

        return $value;
    }

    /**
     * Get clean string
     * @param $value
     * @return string
     */
    protected function _getString($value)
    {
        return JString::trim($value);
    }

    /**
     * Get alias string
     * @param $value
     * @return string
     */
    protected function _getAlias($value)
    {
        return $this->app->string->sluggify($value, false);
    }

    /**
     * Get date from string
     * @param string $value
     * @param null   $default
     * @return string
     */
    protected function _getDate($value, $default = null)
    {
        if ($time = strtotime($this->_getString($value))) {
            return date('Y-m-d H:i:s', $time);
        }

        return $default;
    }

    /**
     * Get array
     * @param string $value
     * @param string $separator
     * @param string $toType
     * @return array
     */
    protected function _getArray($value, $separator = self::SEP_ROWS, $toType = 'string')
    {
        if ($separator == 'simple') {
            $options = array($value);

        } else if (strpos($value, $separator) === false) {
            $options = array($value);

        } else {
            $options = explode($separator, $value);
        }

        if ($toType) {
            foreach ($options as $key => $option) {

                if ($toType == 'bool') {
                    $options[$key] = $this->_getBool($option);

                } else if ($toType == 'int') {
                    $options[$key] = $this->_getInt($option);

                } else if ($toType == 'float') {
                    $options[$key] = $this->_getFloat($option);

                } else if ($toType == 'alias') {
                    $options[$key] = $this->_getAlias($option);

                } else if ($toType == 'date') {
                    $options[$key] = $this->_getDate($option);

                } else {
                    $options[$key] = $this->_getString($option);
                }

            }
        }

        return $options;
    }

    /**
     * Pack data from string
     * @param      $data
     * @param bool $nullElement
     * @return string
     */
    protected function _packToLine($data, $nullElement = false)
    {
        $result = array();

        if (!empty($data)) {
            $from = array(':', ';');
            $to   = array('%col%', '%sem%');

            $dataValue = JString::trim($data['_value']);
            $dataCurr  = JString::trim($data['_currency']);

            if (!empty($dataValue)) {
                $result['_value'] = '_value:' . str_replace($from, $to, $data['_value']);
            }
            if (!empty($dataCurr)) {
                $result['_currency'] = '_currency:' . str_replace($from, $to, $data['_currency']);
            }

            unset($data['_value']);
            unset($data['_currency']);
            if (!empty($data)) {
                foreach ($data as $key => $value) {

                    $key = JString::strtolower($key);
                    if ($nullElement) {
                        $result[] = $key . ':' . str_replace($from, $to, $value);
                    }

                    if (is_string($value)) {
                        $value = JString::trim($value);
                        if (JString::strlen($value) > 0 && !empty($value) && $key) {
                            $result[] = $key . ':' . str_replace($from, $to, $value);
                        }
                    } else if (!empty($value) && is_array($value)) {

                        foreach ($value as $i => $val) {

                            if (is_string($val)) {
                                $val = JString::trim($val);
                                if (JString::strlen($val) > 0 && !empty($val) && $i) {
                                    $result[$i] = $i . ':' . str_replace($from, $to, $val);
                                }
                            } else if (is_array($val)) {
                                $val = $val[key($val)];
                                $val = JString::trim($val);
                                if (JString::strlen($val) > 0 && !empty($val) && $i) {
                                    $result[$i] = $i . ':' . str_replace($from, $to, $val);
                                }
                            }
                        }
                    }
                }
            }
        }

        return implode(';', $result);
    }

    /**
     * Unpack data from string
     * @param  $string
     * @return array
     */
    protected function _unpackFromLine($string)
    {
        $result = array();
        if (!empty($string)) {
            $from = array('%col%', '%sem%');
            $to   = array(':', ';');

            $list = explode(';', $string);
            foreach ($list as $item) {
                if (strpos($item, ':')) {
                    list($key, $value) = explode(':', $item);
                    $key          = JString::strtolower($key);
                    $result[$key] = str_replace($from, $to, $value);
                }
            }
        }

        return $result;
    }
}
