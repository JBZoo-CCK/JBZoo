<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alenxader Oganov <t_tapak@yahoo.com>
 */

/**
 * Class JBPriceHelper
 */
class JBPriceHelper extends AppHelper
{
    const ELEMENTS_CSV_GROUP = 'price';

    /**
     * Check if value seems as numeric
     * @param $value
     * @return bool|int|string
     */
    public function isNumeric($value)
    {
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? true : false;
    }

    /**
     * Check if value seems as date
     * @param $date
     * @return null|string
     */
    public function isDate($date)
    {
        $result = $this->app->jbdate->convertToStamp($date);

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Get all JBPrice elements from all types in app
     * @param array $filter Array of options to filter re result.
     *                      Can be like type, identifier etc.
     * @return array
     */
    public function getAppPrices($filter = array())
    {
        $elements    = array();
        $application = $this->app->zoo->getApplication();

        foreach ($application->getTypes() as $id => $type) {
            $elTypes = $this->getTypePrices($type);

            if (!empty($elTypes)) {
                foreach ($elTypes as $key => $element) {
                    $elements[$id][$key] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Get price elements from item
     * @param Item $item
     * @return array
     */
    public function getItemPrices(Item $item)
    {
        return array_filter($item->getElements(), create_function('$element', 'return $element instanceof ElementJBPrice;'));
    }

    /**
     * Get price elements from type
     * @param Type $type
     * @return array
     */
    public function getTypePrices(Type $type)
    {
        return array_filter($type->getElements(), create_function('$element', 'return $element instanceof ElementJBPrice;'));
    }

    /**
     * Get prices list
     * @return array
     */
    public function getPricesList()
    {
        $types = $this->getAppPrices();
        $app   = $this->app->zoo->getApplication();
        $list  = array();

        if (!empty($types)) {
            foreach ($types as $id => $elements) {
                $name = $app->getType($id)->config->get('name');
                foreach ($elements as $identifier => $element) {
                    $list[$identifier] = JString::ucfirst($name) . ' - ' . JString::ucfirst($element->config->get('name'));
                }
            }
        }

        return $list;
    }

    /**
     * Get field type by value
     * @param $value
     * @return string
     */
    public function getFieldKey($value)
    {
        if ($this->isDate($value)) {
            return 'd';
        } elseif ($this->isNumeric($value)) {
            return 'n';
        }

        return 's';
    }

    /**
     * Get element value
     * @param      $value
     * @param bool $toString
     * @return bool|string
     */
    public function getValue($value, $toString = true)
    {
        if ($value instanceof JBCartValue) {
            $value = $value->data($toString);

        } elseif (is_string($value)) {
            $value = JString::trim($value);

        } elseif (is_array($value) && !empty($value)) {
            $value = JArrayHelper::toString($value, PHP_EOL);

        } elseif(is_object($value)) {
            return $this->getValue((array) $value, $toString);

        }
        $value = JString::trim($value);

        return (!$this->isEmpty($value) ? $value : null);
    }

    /**
     * Check value is not empty string or null.
     * @param $value
     * @return bool
     */
    public function isEmpty($value)
    {
        if (is_string($value)) {
            $value = JString::trim($value);

        } elseif (is_array($value)) {
            $value = array_filter($value);

        } elseif(is_object($value)) {
            return $this->isEmpty((array)$value);
        }

        return ($value === null || empty($value));
    }

    /**
     * Check if string has plus|minus|percent at the start or end
     * @param string|int $value
     * @return bool|string
     */
    public function isModifier($value)
    {
        if (!empty($value) && ($value[0] === '-' || $value[0] === '+' || $value[0] === '%')) {
            return $value[0];
        }

        $value = JBCart::val($value);
        if ($value->isCur('%')) {
            return true;
        }

        return false;
    }

    /**
     * @param  ElementJBPrice $jbPrice
     * @param  string         $id
     * @param  string|array   $value
     * @return bool
     */
    public function addOption($jbPrice, $id, $value)
    {
        if (empty($value)) {
            return $value;
        }

        $helper = $this->app->jbcartposition;
        $model  = JBModelConfig::model();

        $positions = $model->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $jbPrice->identifier);
        $position  = $positions->get(JBCart::DEFAULT_POSITION, array());

        if (JString::strlen($id) === ElementJBPrice::SIMPLE_PARAM_LENGTH) {
            $option = $value;

            if (isset($position[$id])) {
                $element = $position[$id];

                if ($element['type'] == 'color') {
                    $options = $this->app->jbcolor->parse($element['options']);
                    $new     = $this->app->jbcolor->build($option, $options);

                } else {
                    $options = $this->parse($element['options']);
                    $new     = $this->build($option, $options);
                }

                if ($new !== null && $new !== '' && $new != $element['options']) {
                    $element['options'] = $new;
                    $position[$id]      = $element;

                    $positions->set(JBCart::DEFAULT_POSITION, $position);
                    $helper->savePrice(JBCart::CONFIG_PRICE, (array)$positions, $jbPrice->identifier);
                }
            }
        }
        unset($positions, $position);

        return false;
    }

    /**
     * Build new options with new value
     * @param $value
     * @param $options
     * @return string
     */
    public function build($value, $options)
    {
        $value = JString::trim($value);
        $keys  = array_keys($options);

        if (!in_array($this->clean($value), $keys, true)) {
            $options[$value] = $value;
        }

        return implode(PHP_EOL, $options);
    }

    /**
     * @param  $options
     * @return array
     */
    public function parse($options)
    {
        $data   = explode(PHP_EOL, $options);
        $result = array();

        foreach ($data as $option) {
            $key = $this->clean($option);

            if ($key !== '') {
                $result[$key] = JString::trim($option);
            }
        }

        return $result;
    }

    /**
     * Cleans data
     * @param string|array $data
     * @return string mixed
     */
    public function clean($data)
    {
        if (!is_array($data)) {
            return $this->_clean($data);
        }

        foreach ($data as $key => $value) {
            $data[$this->_clean($key)] = $this->_clean($value);
        }

        return $data;
    }

    /**
     * @param  JBCartElementPrice $element
     * @param  ElementJBPrice     $jbPrice
     * @param  array              $options
     * @return \JBCSVItemPrice
     */
    public function csvItem($element, $jbPrice, $options = array())
    {
        if (empty($jbPrice)) {
            return false;
        }

        if ($element instanceof JBCartElementPrice) {
            $type = $element->getElementType();
        } else {
            $type = $element;
        }
        // load table class
        $class = 'JBCSVItemPrice' . $type;

        if (!class_exists('JBCSVItemPrice')) {
            $this->app->loader->register('JBCSVItemPrice', 'jbelements:price/price.php');
        }

        if (!class_exists($class)) {
            $this->app->loader->register($class, 'jbelements:' . self::ELEMENTS_CSV_GROUP . '/' . strtolower($type) . '.php');
        }

        if (class_exists($class)) {
            $instance = new $class($element, $jbPrice, $options);
        } else {
            $instance = new JBCSVItemPrice($element, $jbPrice, $options);
        }

        return $instance;
    }

    /**
     * @param  string      $str
     * @param  bool|string $charlist
     * @return mixed|string
     */
    private function _clean($str, $charlist = false)
    {
        $str = JString::trim($str, $charlist);
        $str = JString::strtolower($str);

        return $str;
    }

}