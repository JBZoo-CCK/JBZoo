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
 * Class JBPriceParamsHelper
 */
class JBPriceParamsHelper extends AppHelper
{
    /**
     * @var array
     */
    public $mainParams = array(
        '_value'    => 'value',
        '_currency' => 'currency'
    );

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
     * Check if value seems as numeric
     *
     * @param $value
     * @return bool|int|string
     */
    public function isNumeric($value)
    {
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? $value : false;
    }

    /**
     * Check if value seems as date
     *
     * @param $date
     * @return null|string
     */
    public function isDate($date)
    {
        $times = $this->app->jbdate->convertToStamp($date);
        if (!empty($times)) {
            return implode($times);
        }

        return null;
    }

    /**
     * Get all JBPrice elements from all types
     * @return array
     */
    public function getJBPriceElements()
    {
        $elements    = array();
        $application = $this->app->zoo->getApplication();

        foreach ($application->getTypes() as $type) {

            $plains = $type->getElementsByType('jbpriceplain');
            $calcs  = $type->getElementsByType('jbpricecalc');

            if (!empty($plains)) {

                foreach ($plains as $key => $plain) {
                    $elements[$key] = ucfirst($type->identifier) . ' - ' . ucfirst($plain->config->get('name'));
                }
            }

            if (!empty($calcs)) {

                foreach ($calcs as $key => $calc) {
                    $elements[$key] = ucfirst($type->identifier) . ' - ' . ucfirst($calc->config->get('name'));
                }
            }
        }

        return $elements;
    }

    /**
     * @param  $price
     * @param  $id
     * @param  $value
     * @return bool
     */
    public function addValueToParam($price, $id, $value)
    {
        $position  = $this->app->jbcartposition;
        $model     = JBModelConfig::model();
        $positions = $model->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $price)
                           ->get(JBCart::DEFAULT_POSITION, array());

        if (isset($positions[$id])) {
            $element = $positions[$id];

            if ($element['type'] == 'color') {
                $jbcolor = $this->app->jbcolor;

                if (strpos($value, '#')) {
                    list($label, $color) = explode('#', $value);
                } else {
                    $label = $value;
                }

                $label = JString::trim($label);

                if (empty($label)) {
                    return false;
                }

                $oldData   = $element['options'];
                $clearData = $jbcolor->parse($oldData);
                $newData   = $jbcolor->build($value, $clearData);

                if ($oldData == $newData) {
                    return false;

                } else if (!empty($newData)) {
                    $element['options'] = $newData;
                    $positions[$id]     = $element;
                    $result['list']     = $positions;

                    $position->savePrice(JBCart::CONFIG_PRICE, $result, null, $price);
                }

            } else {
                $oldData = $element['options'];;
                $clearData = $this->parse($oldData);
                $newData   = $this->build($value, $clearData);

                if ($oldData == $newData) {
                    return false;

                } else if (!empty($newData)) {
                    $element['options'] = $newData;
                    $positions[$id]     = $element;
                    $result['list']     = $positions;

                    $position->savePrice(JBCart::CONFIG_PRICE, $result, null, $price);
                }
            }
        }

        return false;
    }

    /**
     * @param  $options
     * @return array
     */
    public function parse($options)
    {
        $data    = explode("\n", $options);
        $options = array();

        foreach ($data as $param) {

            $param = $this->_clean($param);
            if (!empty($param)) {

                if (!$hasSeparator = strpos($param, '||')) {
                    $options[$this->_clean($param)] = $this->_clean($param);
                } else {
                    list($label, $value) = explode('||', $param);
                    $options[$this->_clean($label)] = $this->_clean($value);
                }

            }
        }

        return $options;
    }

    /**
     * @param $new
     * @param $options
     * @return string
     */
    public function build($new, $options)
    {
        $result = array();
        $keys   = array_keys($options);
        $val    = $this->app->string->sluggify($new);

        if (!$hasSeparator = strpos($new, '||')) {
            $label = $this->_clean($new);
        } else {
            list($label, $val) = explode('||', $new);
        }

        $label = $this->_clean($label);

        if (empty($label)) {
            return false;
        }
        if (!in_array($label, $keys)) {
            $options[$label] = $val;
        }

        foreach ($options as $key => $value) {
            $result[] = $key . '||' . $value;
        }

        return implode("\n", $result);
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
     * @param  string $id
     * @param  string $value
     * @return array
     */
    public function getNestingValue($id, $value)
    {
        $result = $value;
        if (JString::strlen($id) == ElementJBPriceAdvance::SIMPLE_PARAM_LENGTH) {
            $result = array('value' => $value);
        }

        return $result;
    }

    /**
     * Check if is param main
     * Main param like this - _value, _currency
     *
     * @param $id
     * @return bool
     */
    public function isMain($id)
    {
        $converse = array_flip($this->mainParams);

        if (in_array($id, $converse) || in_array($id, $this->mainParams)) {
            return true;
        }

        return false;
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