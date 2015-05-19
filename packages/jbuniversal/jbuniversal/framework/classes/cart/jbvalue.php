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
    const PERCENT      = '%';
    const DEFAULT_CODE = 'default_cur';

    const STYLE_NONE       = 'none';
    const STYLE_PLAIN      = 'plain';
    const STYLE_TEXT       = 'text';
    const STYLE_HTML       = 'html';
    const STYLE_HTML_INPUT = 'html_input';

    const ROUND_DEFAULT      = 8;
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
    const ACT_CLEAN    = 'clean';
    const ACT_CONVERT  = 'convert';
    const ACT_PERCENT  = 'percent';

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
     * @type string
     */
    protected $_currencyMode = 'default';

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

        $this->_currencyMode = $this->app->jbmoney->getCurrencyMode();

        $this->_isDebug = JDEBUG;
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
     * @param string $currency
     * @return bool
     */
    public function isCur($currency)
    {
        $currency = $this->_checkCur($currency);

        return $currency == $this->_currency;
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
     * @param bool $showPlus
     * @return null|string
     */
    public function html($currecny = null, $showPlus = false)
    {
        return $this->_format($currecny, self::STYLE_HTML, $showPlus);
    }

    /**
     * @param null  $currecny
     * @param array $params
     * @return null|string
     */
    public function htmlInput($currecny = null, $params = array())
    {
        return $this->_format($currecny, self::STYLE_HTML_INPUT, false, $params);
    }

    /**
     * @param null $currency
     * @param bool $showPlus
     * @return null|string
     */
    public function htmlAdv($currency, $showPlus = false)
    {
        $currency = $this->_checkCur($currency);

        if ($this->_currency == self::PERCENT
            || $currency == self::PERCENT
            || ($this->isCur($currency) && $currency != self::PERCENT)
        ) {
            $html = $this->_format($this->_currency, self::STYLE_HTML, $showPlus);

        } else {
            $html  = $this->_format($this->_currency, self::STYLE_HTML, $showPlus);
            $title = $this->_format($currency, self::STYLE_TEXT, $showPlus);
            $html  = '<span class="hasTip" title="' . $title . '">' . $html . '</span>';
        }

        return $html;
    }

    /**
     * @param null $currecny
     * @param bool $showPlus
     * @return float|int|null|string
     */
    public function text($currecny = null, $showPlus = false)
    {
        return strip_tags($this->_format($currecny, self::STYLE_TEXT, $showPlus));
    }

    /**
     * @param bool $showId
     * @return string
     */
    public function dump($showId = false)
    {
        $id = $showId ? ';id=' . $this->_id : '';
        return $this->_value . ' ' . $this->_currency . $id;
    }

    /**
     * @param bool $toString
     * @return array|string
     */
    public function data($toString = false)
    {
        $data = array($this->val(), $this->cur());
        return (int)$toString ? implode(' ', $data) : $data;
    }

    /**
     * @return JBCartValue
     */
    public function getClone()
    {
        return clone($this);
    }

    /**
     * Set empty
     */
    public function setEmpty()
    {
        $this->_log('Set empty!');
        $this->_value = 0;
        return $this;
    }

    /**
     * @param JBCartValue                  $value
     * @param string|int|float|JBCartValue $mode
     * @param integer                      $round
     * @return bool
     */
    public function compare($value, $mode = '==', $round = self::ROUND_DEFAULT)
    {
        if (!($value instanceof JBCartValue)) {
            $value = new JBCartValue($value);
        }

        $mode  = in_array($mode, array('=', '==', '===')) ? '==' : $mode;
        $round = (is_null($round)) ? self::ROUND_DEFAULT : ((int)$round);
        $val1  = round((float)$this->val($this->_currency), $round);
        $val2  = round((float)$value->val($this->_currency), $round);

        $this->_log("Compared \"{$this->dump()}\" {$mode} \"{$value->dump()}\" // $val1$mode$val2, r=$round, ");

        if ($mode == '==') {
            return $val1 === $val2;

        } else if ($mode == '!=' || $mode == '!==') {
            return $val1 !== $val2;

        } else if ($mode == '<') {
            return $val1 < $val2;

        } else if ($mode == '>') {
            return $val1 > $val2;

        } else if ($mode == '<=') {
            return $val1 <= $val2;

        } else if ($mode == '>=') {
            return $val1 >= $val2;
        }

        $this->_error('Undefined compare mode: ' . $mode);
        return false;
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
     */
    public function add($value, $getClone = false)
    {
        return $this->_modifer($value, self::ACT_PLUS, $getClone);
    }

    /**
     * @param mixed $value
     * @param bool  $getClone
     * @return JBCartValue
     */
    public function minus($value, $getClone = false)
    {
        return $this->_modifer($value, self::ACT_MINUS, $getClone);
    }

    /**
     * @param string $newCurrency
     * @param bool   $getClone
     * @return $this
     */
    public function convert($newCurrency, $getClone = false)
    {
        if (empty($newCurrency) || $newCurrency == self::DEFAULT_CODE) {
            return $this;
        }

        return $this->_modifer($newCurrency, self::ACT_CONVERT, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     */
    public function invert($getClone = false)
    {
        return $this->_modifer(null, self::ACT_INVERT, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     */
    public function positive($getClone = false)
    {
        return $this->_modifer(null, self::ACT_POSITIVE, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     */
    public function negative($getClone = false)
    {
        return $this->_modifer(null, self::ACT_NEGATIVE, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     */
    public function abs($getClone = false)
    {
        return $this->_modifer(null, self::ACT_ABS, $getClone);
    }

    /**
     * @param bool $getClone
     * @return JBCartValue
     */
    public function clean($getClone = false)
    {
        return $this->_modifer(null, self::ACT_CLEAN, $getClone);
    }

    /**
     * @param mixed $value
     * @param bool  $getClone
     * @return JBCartValue
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
        return $this->_modifer($number, self::ACT_MULTIPLY, $getClone);
    }

    /**
     * @param  $value
     * @return JBCartValue
     */
    public function percent($value)
    {
        $value = JBCart::val($value);
        return $this->_modifer($value, self::ACT_PERCENT, true);
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
     * @param null   $value
     * @return float|null
     */
    protected function _round($currency = '', $value = null)
    {
        $currency = $this->_checkCur(($currency ? $currency : $this->_currency));

        $format     = $this->_rates[$currency]['format'];
        $roundType  = isset($format['round_type']) ? $format['round_type'] : 'none';
        $roundValue = (int)((isset($format['round_value'])) ? $format['round_value'] : 2);

        if (is_null($value)) {
            $value = $this->_value;
        }

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
     * @param null   $currecny
     * @param string $style
     * @param bool   $showPlus
     * @param bool   $params
     * @return float|int|null|string
     */
    protected function _format($currecny = null, $style = self::STYLE_PLAIN, $showPlus = false, $params = array())
    {
        if (empty($currecny)) {
            $currecny = $this->_currency;
        }

        $cookieCur = $this->app->jbrequest->getCurrency();
        if ($cookieCur && $this->app->jbrequest->isAjax()) {
            $currecny = $cookieCur;
        }

        if ($this->cur() == self::PERCENT) {
            $currecny = $this->cur();
        }

        $currecny = $this->_checkCur($currecny);
        $format   = $this->_rates[$currecny]['format'];
        $format   = array_merge($this->_defaultFormat, (array)$format);

        $value = $this->val($currecny);
        $value = !empty($value) ? $value : 0;

        $roundedValue = $this->_round($currecny, $value);
        $isPositive   = ($value >= 0);
        $valueStr     = number_format(abs($roundedValue), $format['num_decimals'], $format['decimal_sep'], $format['thousands_sep']);
        $moneyFormat  = ($isPositive) ? $format['format_positive'] : $format['format_negative'];

        $attrs = null;
        if (strpos($style, 'html') !== false) {
            $attrs = array(
                'data-moneyid'  => self::$_counter,
                'data-value'    => $this->_value,
                'data-currency' => $this->_currency,
                'data-showplus' => (int)$showPlus,
            );
        }

        // output with styles
        $result = null;
        if (self::STYLE_NONE == $style) {
            $result = $value;

        } elseif (self::STYLE_PLAIN == $style) {
            $result = $valueStr;
            if ($isPositive && $showPlus) {
                $result = '+' . $result;
            }

        } elseif (self::STYLE_TEXT == $style) {
            $result = str_replace(array('%s', '%v'), array($format['symbol'], $valueStr), $moneyFormat);
            if ($isPositive && $showPlus) {
                $result = '+' . $result;
            }

        } elseif (self::STYLE_HTML == $style) {
            $result = str_replace(array('%s', '%v'), array(
                '<span class="jbcurrency-symbol">' . $format['symbol'] . "</span>",
                '<span class="jbcurrency-value">' . $valueStr . "</span>"
            ), $moneyFormat);

            if ($isPositive && $showPlus) {
                $result = '+' . $result;
            }

            $attrs['class'] = 'jsMoney jbcartvalue';

            $result = '<span ' . $this->app->jbhtml->buildAttrs($attrs, false) . ">\n" . $result . '</span>';

        } elseif (self::STYLE_HTML_INPUT == $style) {

            $class = 'jsMoney jsMoneyInput jbcartvalue-input';
            $attrs = array_merge($attrs, $params, array(
                'value' => $this->text(),
                'type'  => 'text',
            ));

            $attrs['name']  = isset($attrs['name']) ? $attrs['name'] : '';
            $attrs['class'] = (isset($params['class'])) ? ($class . ' ' . $params['class']) : $class;

            $result = '<input ' . $this->app->jbhtml->buildAttrs($attrs, false) . ' />';
        }

        $this->_log('Formated output in "' . $currecny . '" as "' . $style . '"');

        return $result;
    }

    /**
     * @param string $currency
     * @param bool   $addTolog
     * @return float
     */
    protected function _convert($currency, $addTolog = false)
    {
        $from = $this->_checkCur($this->_currency);
        $to   = $this->_checkCur($currency);

        $logFormat = '"' . $from . '"=>"' . $to . '"';

        if (($from == self::PERCENT && $to != self::PERCENT) ||
            ($from != self::PERCENT && $to == self::PERCENT)
        ) {
            $this->_error(__CLASS__ . ' convertor - Percent can\'t be converted (' . $logFormat . ')');
        }

        if (empty($to) || !isset($this->_rates[$to])) {
            $this->_error(__CLASS__ . ' convertor - undefined target currency: ' . $logFormat);
        }

        if (!isset($this->_rates[$from])) {
            $this->_error(__CLASS__ . ' convertor - undefined source currency: ' . $logFormat);
        }

        $result = $this->_value;
        if ($from != $to) {
            $normValue = $this->_value / $this->_rates[$from]['value'];
            $result    = round($normValue * $this->_rates[$to]['value'], self::ROUND_DEFAULT);

            if ($addTolog) {
                $this->_log('Converted ' . $logFormat . '; New value = "' . $result . ' ' . $to . '"');
            }
        }

        return $result;
    }

    /**
     * @param mixed  $value
     * @param string $action
     * @param bool   $getClone
     * @return $this
     */
    protected function _modifer($value, $action, $getClone = false)
    {
        $logMess = $newValue = null;
        if (self::ACT_PLUS == $action || self::ACT_MINUS == $action) {

            if ($value instanceof JBCartValue) {

                $logMess  = ucfirst($action) . ' "' . $value->dump() . '"';
                $addValue = 0;

                if ($this->_currency == self::PERCENT) {
                    if ($value->cur() == self::PERCENT) {
                        $addValue = $value->val();
                    } else {
                        $this->_error('Impossible add "' . $value->text() . '" to "' . $this->text() . '"');
                    }
                } else {
                    if ($value->cur() != self::PERCENT) {
                        $addValue = $value->val($this->_currency);
                    } else {
                        $addValue = $this->_value * $value->val() / 100;
                    }
                }

                if (self::ACT_MINUS == $action) {
                    $addValue *= -1;
                }

                $newValue = $this->_value + $addValue;

            } else {
                $parsedValue = JBCart::val($value); // we work only with objects!
                return $this->_modifer($parsedValue, $action, $getClone);
            }

        } else if (self::ACT_CONVERT == $action) {

            $newCurrency = $this->_checkCur($value);

            $obj = $getClone ? clone($this) : $this;

            if ($newCurrency !== $obj->_currency) {
                $obj->_value    = $obj->_convert($newCurrency, true);
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

        } else if (self::ACT_CLEAN == $action) {
            $newValue = 0.0;
            $logMess  = 'Set empty';

        } else if (self::ACT_MODIFY == $action) {

            if (method_exists($value, 'modify')) {

                $this->_log('Modyfied by elementId "' . $value->identifier . '"; '
                    . $value->getElementGroup() . '/' . $value->getElementType());

                if ($getClone) {
                    $clone = clone($this);
                    $value->modify($clone);

                    return $clone;
                }

                return $value->modify($this);

            } else {
                $this->_error('Value doesn\'t have modyfy action!');
            }

        } else if (self::ACT_PERCENT == $action) {
            $percent = 0.0;
            if (!$this->isEmpty() && !$value->isEmpty()) {
                $percent = ($this->_value / $value->val($this->_currency)) * 100;
            }
            return JBCart::val($percent, self::PERCENT);

        } else {
            $this->_error('Undefined action: "' . $action . '"');
        }

        if (is_null($newValue)) {
            $this->_error('Undefined new value for action=' . $action);
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
            $data = trim($data);

            if ($this->_currencyMode == 'default') {
                $reg = '#(.*)(' . implode('|', $this->getCodeList()) . ')$#i';
            } else {
                $reg = '#(.*)([a-z]{3}|%)$#i';
            }

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
     * @return bool|mixed|null|string
     */
    protected function _checkCur($currency)
    {
        $currency = strtolower(trim($currency));

        if (self::PERCENT === $currency) {
            return $currency;
        }

        if (JBCartValue::DEFAULT_CODE === $currency) {
            return $this->_baseCur;
        }

        if (empty($currency)) {
            return $this->_baseCur;
        }

        if (isset($this->_rates[$currency])) {
            return $currency;
        }

        if ($currency) {

            if ($this->_currencyMode == 'fatal') {
                $this->_error('Undefined currency: ' . $currency);

            } else if ($this->_currencyMode == 'notice') {
                trigger_error('Undefined currency: ' . $currency, E_USER_NOTICE);
            }

            return isset($this->_rates[$this->_baseCur]) ? $this->_baseCur : 'eur';
        }

        return false;
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
     * @return bool|mixed
     */
    protected function _logVar($data)
    {
        if ($this->_isDebug) {
            $removeData = array('Array', "\n", "\r", '     ');
            return str_replace($removeData, ' ', print_r($data, true));
        }

        return false;
    }

    /**
     * @param string $message
     * @throws JBCartValueException
     */
    protected function _error($message)
    {
        $this->_log($message);
        throw new JBCartValueException(__CLASS__ . ': ' . $message);
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->html();
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
        $this->_log('Wakeup start--->');
        $this->__construct($this->dump(), null, $this->_rates);
        $this->_log('Wakeup < --- finish');
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
        $this->_log('Has cloned from id = ' . $oldId . ' and created new with id = ' . $this->_id . '; dump = ' . $this->dump());
    }

}


/**
 * Class JBCartValueException
 */
class JBCartValueException extends AppException
{

}