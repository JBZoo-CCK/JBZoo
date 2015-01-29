<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
     *
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
     *
     * @param $date
     * @return null|string
     */
    public function isDate($date)
    {
        return $this->app->jbdate->isDate($date);
    }

    /**
     * Get all JBPrice elements from all types in app
     * @return array
     */
    public function getAppPrices()
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
        $list  = array();

        if (!empty($types)) {
            foreach ($types as $id => $elements) {
                foreach ($elements as $identifier => $element) {
                    $list[$identifier] = ucfirst($id) . ' - ' . ucfirst($element->config->get('name'));
                }
            }
        }

        return $list;
    }

    /**
     * Get field type by value
     *
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
    public function getValue($value, $toString = false)
    {
        if ($value instanceof JBCartValue) {
            $value = $value->data($toString);

        } elseif (is_string($value)) {
            $value = JString::trim($value);

        }

        if (is_array($value) && !empty($value)) {
            return $value;

        } elseif (JString::strlen($value) !== 0) {
            return $value;

        }

        return false;
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

            $element    = $position[$id];
            $oldOptions = $element['options'];

            if ($id['type'] == 'color') {
                $options = $this->app->jbcolor->parse($oldOptions);
                $new     = $this->app->jbcolor->build($option, $options);
            } else {
                $options = $this->parse($oldOptions);
                $new     = $this->build($option, $options);
            }

            if ($new != $oldOptions && !empty($new)) {
                $element['options'] = $new;
                $position[$id]      = $element;

                $positions->set(JBCart::DEFAULT_POSITION, $position);
                $helper->savePrice(JBCart::CONFIG_PRICE, $positions, $jbPrice->identifier);
            }
        }

        return false;
    }

    /**
     * Build new options with new value
     *
     * @param $value
     * @param $options
     * @return string
     */
    public function build($value, $options)
    {
        $value = $this->_clean($value);
        $keys  = array_keys($options);

        if (!in_array($value, $keys)) {
            $options[$value] = $value;
        }

        return implode("\n", $options);
    }

    /**
     * @param  $options
     * @return array
     */
    public function parse($options)
    {
        $data   = explode("\n", $options);
        $result = array();

        foreach ($data as $option) {
            $option = $this->_clean($option);

            if (JString::strlen($option) !== 0) {
                $result[$option] = $option;
            }
        }

        return $result;
    }

    /**
     * Cleans data
     * @param string $data
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