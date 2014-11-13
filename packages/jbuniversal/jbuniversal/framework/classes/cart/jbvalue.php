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
 * Class JBCartValue
 */
class JBCartValue
{
    const PERCENT = '%';

    const STYLE_NONE  = 'none';
    const STYLE_PLAIN = 'plain';
    const STYLE_TEXT  = 'text';
    const STYLE_HTML  = 'html';

    const ROUND_DEFAULT      = 9;
    const ROUND_TYPE_NONE    = 'none';
    const ROUND_TYPE_CEIL    = 'ceil';
    const ROUND_TYPE_FLOOR   = 'floor';
    const ROUND_TYPE_CLASSIC = 'classic';

    const ACT_PLUS     = 'plus';
    const ACT_MINUS    = 'minus';
    const ACT_ABS      = 'abs';
    const ACT_MODIFY   = 'modifier';
    const ACT_MULTIPLY = 'multiply';
    const ACT_INVERT   = 'invert';
    const ACT_POSITIVE = 'positive';
    const ACT_NEGATIVE = 'negative';
    const ACT_EMPTY    = 'empty';
    const ACT_CONVERT  = 'convert';

    /**
     * @type int
     */
    static protected $_counter = 0;

    /**
     * @type App
     */
    public $app = null;

    /**
     * @var int
     */
    protected $_id = 0;

    /**
     * @var float
     */
    protected $_value = 0.0;

    /**
     * @var string
     */
    protected $_currency = '';

    /**
     * @var string
     */
    protected $_baseCur = '';

    /**
     * @type array
     */
    protected $_rates = array();

    /**
     * @type array
     */
    protected $_logs = array();

    /**
     * @type bool
     */
    protected $_isDebug = false;

    /**
     * @var array
     */
    protected $_defaultFormat = array(
        'symbol'          => '',
        'round_type'      => 'none',
        'round_value'     => '2',
        'num_decimals'    => '2',
        'decimal_sep'     => '.',
        'thousands_sep'   => ' ',
        'format_positive' => '%v %s',
        'format_negative' => '-%v %s',
    );

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

    /**
     * @var JBVarsHelper
     */
    protected $_jbvars = null;

    /**
     * @param mixed    $data
     * @param JSONData $rates
     * @param string   $baseCur
     */
    public function __construct($data = 0, $rates = null, $baseCur = null)
    {
        $this->app      = App::getInstance('zoo');
        $this->_jbmoney = $this->app->jbmoney;
        $this->_jbvars  = $this->app->jbvars;

        $this->_isDebug = defined('JDEBUG') && JDEBUG;
        $this->_rates   = (array)($rates ? $rates : $this->_jbmoney->getData());
        $this->_baseCur = $baseCur ? $baseCur : $this->_jbmoney->getDefaultCur();

        list($this->_value, $this->_currency) = $this->_parse($data);

        self::$_counter++;
        $this->_id = self::$_counter;
        $this->_log('ValueId=' . $this->_id . ' has just created; Value = "' . $this->dump() . '"');
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->_id;
    }

    /**
     * @param null $currency
     * @return float
     */
    public function val($currency = null)
    {
        if ($currency && $currency != $this->_currency) {
            return $this->_convert($currency);
        }

        return $this->_round();
    }

    /**
     * @return float
     */
    public function cur()
    {
        return $this->_currency;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (float)$this->_value === 0.0;
    }

    /**
     * @return bool
     */
    public function isPositive()
    {
        return $this->_value > 0;
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return $this->_value < 0;
    }

    /**
     * @return array
     */
    public function rates()
    {
        $rates = $this->_rates;
        unset($rates[self::PERCENT]);

        return $rates;
    }

    /**
     * @param null $currecny
     * @return null|string
     */
    public function noStyle($currecny = null)
    {
        return $this->_format($currecny, self::STYLE_NONE);
    }

    /**
     * @param null $currecny
     * @return null|string
     */
    public function plain($currecny = null)
    {
        return $this->_format($currecny, self::STYLE_PLAIN);
    }

    /**
     * @param null $currecny
     * @return null|string
     */
    public function html($currecny = null)
    {
        return $this->_format($currecny, self::STYLE_HTML);
    }

    /**
     * @param null $currecny
     * @return null|string
     */
    public function text($currecny = null)
    {
        return $this->_format($currecny, self::STYLE_TEXT);
    }

    /**
     * @return string
     */
    public function dump()
    {
        return $this->_value . ' ' . $this->_currency;
    }

    /**
     * @param JBCartValue|string $value
     * @param string             $mode
     * @param integer            $round
     * @throws JBCartValueException
     */
    public function compare($value, $mode = '==', $round = self::ROUND_DEFAULT)
    {
        if (!($value instanceof JBCartValue)) {
            $value = new JBCartValue($value);
        }

        $mode  = in_array($mode, array('=', '==', '===')) ? '==' : $mode;
        $round = (is_null($round)) ? self::ROUND_DEFAULT : ((int)$round);
        $val1  = round((float)$this->_value, $round);
        $val2  = round((float)$value->val($this->_currency), $round);

        $this->_log("Compare \"{$this->dump()}\" {$mode} \"{$value->dump()}\" // $val1$mode$val2, r=$round, ");

        if ($mode == '==') {
            return $val1 === $val2;

        } else if ($mode == '<') {
            return $val1 < $val2;

        } else if ($mode == '>') {
            return $val1 > $val2;

        } else if ($mode == '<=') {
            return $val1 <= $val2;

        } else if ($mode == '>=') {
            return $val1 >= $val2;
        }

        throw new JBCartValueException('Undefined compare mode: ' . $mode);
    }

    /**
     * @param bool $isShort
     * @return array
     */
    public function getCurList($isShort = false)
    {
        $result = array();

        if (empty($this->_rates)) {
            return $result;
        }

        foreach ($this->_rates as $code => $currency) {

            if ($code == self::PERCENT) {
                continue;
            }

            if ($isShort) {
                $result[$code] = $code;
            } else {
                $result[$code] = $currency['name'] . ' (' . $code . ')';
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCodeList()
    {
        $codes = array_keys($this->_rates);
        return $codes;
    }

    /**
     * @param array $newFormat
     * @param null  $currency
     * @return $this
     */
    public function setFormat(array $newFormat, $currency = null)
    {
        $newFormat = (array)$newFormat;
        $currency  = empty($currency) ? $this->_currency : $this->_checkCur($currency);

        $this->_rates[$currency]['format'] = array_merge($this->_rates[$currency]['format'], $newFormat);

        $this->_log('New formating for "' . $currency . '": ' . $this->_logVar($newFormat) . '');

        return $this;
    }

    /**
     * @param mixed $value
     * @param bool  $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function add($value, $getClone = false)
    {
        return $this->_modifer($value, self::ACT_PLUS, $getClone);
    }

    /**
     * @param mixed $value
     * @param bool  $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function minus($value, $getClone = false)
    {
        return $this->_modifer($value, self::ACT_MINUS, $getClone);
    }

    /**
     * @param string $newCurrency
     * @param bool   $getClone
     * @return $this
     * @throws JBCartValueException
     */
    public function convert($newCurrency, $getClone = false)
    {
        return $this->_modifer($newCurrency, self::ACT_CONVERT, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function invert($getClone = false)
    {
        return $this->_modifer(null, self::ACT_INVERT, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function positive($getClone = false)
    {
        return $this->_modifer(null, self::ACT_POSITIVE, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function negative($getClone = false)
    {
        return $this->_modifer(null, self::ACT_NEGATIVE, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function abs($getClone = false)
    {
        return $this->_modifer(null, self::ACT_ABS, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function clean($getClone = false)
    {
        return $this->_modifer(null, self::ACT_EMPTY, $getClone);
    }

    /**
     * @param mixed $value
     * @param bool  $getClone
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function addModify($value, $getClone = false)
    {
        return $this->_modifer($value, self::ACT_MODIFY, $getClone);
    }

    /**
     * @param float $number
     * @param bool  $getClone
     * @return JBCartValue
     */
    public function multiply($number, $getClone = false)
    {
        return $this->_modifer($number, self::ACT_EMPTY, $getClone);
    }

    /**
     * @param string $data
     * @param string $currency
     * @return $this
     */
    public function set($data, $currency = null)
    {
        list($this->_value, $this->_currency) = $this->_parse($data, $currency);
        $this->_log('Set new value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @return array
     */
    public function logs()
    {
        return $this->_logs;
    }

    /**
     * @param bool $isDebug
     * @return $this
     */
    public function debug($isDebug = true)
    {
        $this->_isDebug = (int)$isDebug;
        return $this;
    }

    /**
     * @param string $currency
     * @return float
     */
    protected function _round($currency = '')
    {
        $currency   = $this->_checkCur(($currency ? $currency : $this->_currency));
        $roundType  = $this->_rates[$currency]['format']['round_type'];
        $roundValue = $this->_rates[$currency]['format']['round_value'];

        $value = $this->_value;
        if (self::ROUND_TYPE_CEIL == $roundType) {
            $base  = pow(10, $roundValue);
            $value = ceil($value * $base) / $base;

        } elseif (self::ROUND_TYPE_CLASSIC == $roundType) {
            $value = round($value, $roundValue);

        } elseif (self::ROUND_TYPE_FLOOR == $roundType) {
            $base  = pow(10, $roundValue);
            $value = floor($value * $base) / $base;

        } else {
            $value = round($value, self::ROUND_DEFAULT);
        }

        return $value;
    }

    /**
     * Convert value to money format from config
     * @param string $currecny
     * @param string $style
     * @return null|string
     */
    protected function _format($currecny = null, $style = self::STYLE_PLAIN)
    {
        if (empty($currecny)) {
            $currecny = $this->_currency;
        }

        $currecny = $this->_checkCur($currecny);
        $format   = $this->_rates[$currecny]['format'];
        $format   = array_merge($this->_defaultFormat, (array)$format);

        $value = $this->val($currecny);
        $value = !empty($value) ? $value : 0;

        $roundedValue = $this->_round($currecny);
        $valueStr     = number_format(abs($roundedValue), $format['num_decimals'], $format['decimal_sep'], $format['thousands_sep']);
        $moneyFormat  = ($value >= 0) ? $format['format_positive'] : $format['format_negative'];

        // output with styles
        $result = null;
        if (self::STYLE_NONE == $style) {
            $result = $value;

        } elseif (self::STYLE_PLAIN == $style) {
            $result = $valueStr;

        } elseif (self::STYLE_TEXT == $style) {
            $result = JString::str_ireplace(array('%s', '%v'), array($format['symbol'], $valueStr), $moneyFormat);

        } elseif (self::STYLE_HTML == $style) {
            $result = JString::str_ireplace(array('%s', '%v'), array(
                '<span class="jbcurrency-symbol">' . $format['symbol'] . "</span>\n",
                '<span class="jbcurrency-value">' . $valueStr . "</span>\n"
            ), $moneyFormat);

            $attrs = $this->app->jbhtml->buildAttrs(array(
                'class'              => 'jsMoney jbcartvalue',
                'data-round_type'    => $format['round_type'],
                'data-round_value'   => $format['round_value'],
                'data-num_decimals'  => $format['num_decimals'],
                'data-decimal_sep'   => $format['decimal_sep'],
                'data-thousands_sep' => $format['thousands_sep'],
            ), false);

            $result = '<span ' . $attrs . ">\n" . $result . '</span>';
        }

        $this->_log('Formated output in "' . $currecny . '" as "' . $style . '"');

        return $result;
    }

    /**
     * @param string $currency
     * @return float
     * @throws JBCartValueException
     */
    protected function _convert($currency)
    {
        $from = $this->_checkCur($this->_currency);
        $to   = $this->_checkCur($currency);

        $logFormat = '"' . $from . '"=>"' . $to . '"';

        if (($from == self::PERCENT && $to != self::PERCENT) ||
            ($from != self::PERCENT && $to == self::PERCENT)
        ) {
            throw new JBCartValueException('JBCartValue convertor - Percent can\'t be converted (' . $logFormat . ')');
        }

        if (empty($to)) {
            throw new JBCartValueException('JBCartValue convertor - undefined currency');
        }

        if (!isset($this->_rates[$to])) {
            throw new JBCartValueException('JBCartValue convertor - undefined currency: ' . $logFormat);
        }

        if (!isset($this->_rates[$from])) {
            throw new JBCartValueException('JBCartValue convertor - undefined currency: ' . $logFormat);
        }

        $result = $this->_value;
        if ($from != $to) {
            $normValue = $this->_value / $this->_rates[$from]['value'];
            $result    = round($normValue * $this->_rates[$to]['value'], self::ROUND_DEFAULT);

            $this->_log('Converted ' . $logFormat . '; New value = "' . $result . ' ' . $to . '"');
        }

        return $result;
    }

    /**
     * @param mixed  $value
     * @param string $action
     * @param bool   $getClone
     * @return $this
     * @throws JBCartValueException
     */
    protected function _modifer($value, $action, $getClone = false)
    {
        $newValue = null;
        if (self::ACT_PLUS == $action || self::ACT_MINUS == $action) {

            if ($value instanceof JBCartValue) {

                $logMess = ucfirst($action) . ' "' . $value->dump() . '"';

                if (self::ACT_MINUS == $action) {
                    $value->multiply(-1);
                }

                if ($this->_currency == self::PERCENT) {
                    if ($value->cur() == self::PERCENT) {
                        $addValue = $value->val();
                    } else {
                        throw new JBCartValueException('Impossible add "' . $value->text() . '" to "' . $this->text() . '"');
                    }
                } else {
                    if ($value->cur() != self::PERCENT) {
                        $addValue = $value->val($this->_currency);
                    } else {
                        $addValue = $this->_value * $value->val() / 100;
                    }
                }

                $newValue = $this->_value + $addValue;
                unset($value);

            } else {
                $parsedValue = JBCart::val($value); // we work only with objects!
                return $this->_modifer($parsedValue, $action, $getClone);
            }

        } else if (self::ACT_CONVERT == $action) {

            $newCurrency = $this->_checkCur($value);

            $obj = $getClone ? clone($this) : $this;

            if ($newCurrency !== $obj->_currency) {
                $obj->_value    = $obj->_convert($newCurrency);
                $obj->_currency = $newCurrency;
            }

            return $obj;

        } else if (self::ACT_MULTIPLY == $action) {
            $value    = (float)$value;
            $newValue = $value * $this->_value;
            $logMess  = 'Multiply with "' . $value . '"';

        } else if (self::ACT_INVERT == $action) {

            $logMess = 'Invert sign';
            if ($this->_value > 0) {
                $newValue = -1 * $this->_value;
            } else if ($this->_value < 0) {
                $newValue = abs($this->_value);
            } else {
                $newValue = $this->_value;
            }

        } else if (self::ACT_POSITIVE == $action) {
            $newValue = abs($this->_value);
            $logMess  = 'Set positive';

        } else if (self::ACT_NEGATIVE == $action) {
            $newValue = -1 * abs($this->_value);
            $logMess  = 'Set negative';

        } else if (self::ACT_ABS == $action) {
            $newValue = abs($this->_value);
            $logMess  = 'Set absolute value';

        } else if (self::ACT_EMPTY == $action) {
            $newValue = 0.0;
            $logMess  = 'Set empty';

        } else if (self::ACT_MODIFY == $action) {

            if (method_exists($value, 'modyfy')) {

                $logMess = 'Modyfied by elementId "' . $value->identifier . '"';
                if ($getClone) {
                    $clone = clone($this);
                    $value->modyfy($clone);

                    return $clone;
                }

                $value->modyfy($this);

            } else {
                throw new JBCartValueException('Value doesn\'t have modyfy action!');
            }

        } else {
            throw new JBCartValueException('Undefined action: "' . $action . '"');
        }

        if (is_null($newValue)) {
            throw new JBCartValueException('Undefined new value for action=' . $action);
        }

        // create new object or return self
        if ($getClone) {
            $clone         = clone($this);
            $clone->_value = $newValue;
            $clone->_log($logMess . '; New value = "' . $clone->dump() . '"');
            return $clone;
        }

        $this->_value = $newValue;
        $this->_log($logMess . '; New value = "' . $this->dump() . '"');
        return $this;
    }

    /**
     * @param mixed  $data
     * @param string $forceCur
     * @return array
     */
    protected function _parse($data, $forceCur = null)
    {
        $this->_log('Parse data = "' . $this->_logVar($data) . '" with forceCur = "' . $forceCur . '"');

        $value    = null;
        $currency = null;

        if (is_array($data)) {
            $value    = isset($data[0]) ? $data[0] : null;
            $currency = isset($data[1]) ? $data[1] : null;
            return $this->_parse($value, $currency);

        } else {
            $reg = '#(.*)(' . implode('|', $this->getCodeList()) . ')$#i';
            if (preg_match($reg, $data, $matches)) {
                $value    = $matches[1];
                $currency = $matches[2];
            }
        }

        if (is_null($value)) {
            $value = $data;
        }

        if ($forceCur) {
            $currency = $forceCur;
        }

        $value    = $this->_clean($value);
        $currency = $this->_checkCur($currency);
        if (empty($currency)) {
            $currency = $this->_baseCur;
        }

        return array($value, $currency);
    }

    /**
     * @param string $value
     * @return float
     */
    protected function _clean($value)
    {
        return $this->_jbvars->money($value, self::ROUND_DEFAULT);
    }

    /**
     * @param $currency
     * @return mixed|string
     * @throws JBCartValueException
     */
    protected function _checkCur($currency)
    {
        $currency = $this->_jbvars->lower($currency, true);

        if (self::PERCENT == $currency) {
            return self::PERCENT;
        }

        if (array_key_exists($currency, $this->_rates)) {
            return $currency;
        }

        if ($currency) {
            throw new JBCartValueException('Undefined currency: ' . $currency);
        }
    }

    /**
     * @param $message
     */
    protected function _log($message)
    {
        if ($this->_isDebug) {
            $this->_logs[] = (string)$message;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    protected function _logVar($data)
    {
        if ($this->_isDebug) {
            $removeData = array('Array', "\n", "\r", '     ');
            return str_replace($removeData, ' ', print_r($data, true));
        }
    }


    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->text();
    }

    /**
     * Serialize
     * @return array
     */
    public function __sleep()
    {
        $result   = array();
        $reflect  = new ReflectionClass($this);
        $propList = $reflect->getProperties();

        foreach ($propList as $prop) {

            $pName = $prop->name;

            if (in_array($pName, array('app'))
                || strpos($pName, '_jb') === 0
                || $prop->isStatic() == true
            ) {
                continue;
            }

            $result[] = $prop->name;
        }

        $this->_log('Serialized');

        return $result;
    }

    /**
     * Wake up after serialize
     */
    public function __wakeup()
    {
        $this->_log('Wakeup start --->');
        $this->__construct($this->dump(), null, $this->_rates);
        $this->_log('Wakeup <--- finish');
    }

    /**
     * Clone object
     */
    public function __clone()
    {
        self::$_counter++;

        $oldId     = $this->_id;
        $this->_id = self::$_counter;

        $this->_logs = array();
        $this->_log('Has cloned from id=' . $oldId . ' and created new with id=' . $this->_id . '; dump=' . $this->dump());
    }

}


/**
 * Class JBCartValueException
 */
class JBCartValueException extends AppException
{

}