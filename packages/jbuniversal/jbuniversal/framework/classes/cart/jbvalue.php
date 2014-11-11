<?php

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

    /**
     * @type App
     */
    public $app = null;

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
     * @param string   $currency
     * @param JSONData $rates
     * @param string   $baseCur
     */
    public function __construct($data = 0, $currency = '', $rates = null, $baseCur = null)
    {
        $this->app      = App::getInstance('zoo');
        $this->_jbmoney = $this->app->jbmoney;
        $this->_jbvars  = $this->app->jbvars;

        $this->_isDebug = defined('JDEBUG') && JDEBUG;
        $this->_rates   = (array)($rates ? $rates : $this->_jbmoney->getData());
        $this->_baseCur = $baseCur ? $baseCur : $this->_jbmoney->getDefaultCur();

        list($this->_value, $this->_currency) = $this->_parse($data, $currency);
        $this->_log('Just created; Value = "' . $this->dump() . '"');
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
     * @param $newCurrency
     * @return $this
     * @throws JBCartValueException
     */
    public function convert($newCurrency)
    {
        $newCurrency = $this->_checkCur($newCurrency);

        if ($newCurrency !== $this->_currency) {
            $this->_value    = $this->_convert($newCurrency);
            $this->_currency = $newCurrency;
        }

        return $this;
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
     * @param array $newFormat
     * @param null  $currency
     * @return $this
     */
    public function setFormat(array $newFormat, $currency = null)
    {
        $newFormat = (array)$newFormat;
        $currency  = empty($currency) ? $this->_currency : $this->_checkCur($currency);

        $this->_rates[$currency]['format'] = array_merge($this->_rates[$currency]['format'], $newFormat);

        $this->_log('New formating for "' . $currency . '": ' . $this->_logArray($newFormat) . '');

        return $this;
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
        $result     = array();
        $reflect    = new ReflectionClass($this);
        $properties = $reflect->getProperties();

        foreach ($properties as $property) {

            $pName = $property->name;
            if (in_array($pName, array('app')) || strpos($pName, '_jb') === 0) {
                continue;
            }

            array_push($result, $property->name);
        }

        $this->_log('Serialized');

        return $result;
    }

    /**
     * Wake up after serialize
     */
    public function __wakeup()
    {
        $this->_log('Wakeup --->');
        $this->__construct($this->dump(), null, $this->_rates);
        $this->_log('Wakeup <---');
    }

    /**
     * @param JBCartValue $value
     * @param string      $currency
     * @param bool        $isMinus
     * @return $this
     * @throws JBCartValueException
     */
    public function add($value, $currency = null, $isMinus = false)
    {
        if ($value instanceof JBCartValue) {

            if ($isMinus) {
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

            $this->_value += $addValue;

            if ($isMinus) {
                $this->_log('Minus "' . $value->multiply(-1)->dump() . '"; New value = "' . $this->dump() . '"');
            } else {
                $this->_log('Plus "' . $value->dump() . '"; New value = "' . $this->dump() . '"');
            }

        } else {
            $value = JBCart::val($value, $currency);
            return $this->add($value, $currency, $isMinus);
        }

        return $this;
    }

    /**
     * @param JBCartValue $value
     * @param string      $currency
     * @return JBCartValue
     * @throws JBCartValueException
     */
    public function minus($value, $currency = null)
    {
        return $this->add($value, $currency, true);
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
     * @param JBCartElement $element
     * @return $this
     */
    public function addModify(JBCartElement $element)
    {
        if (method_exists($element, 'modyfy')) {
            $element->modyfy($this);
            $this->_log('Modyfied by elementId "' . $element->identifier . '"; New value = "' . $this->dump() . '"');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function abs()
    {
        $this->_value = abs($this->_value);
        $this->_log('Set ABS; New value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @return $this
     */
    public function negative()
    {
        $this->_value = -1 * abs($this->_value);
        $this->_log('Set negative; New value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @return $this
     */
    public function positive()
    {
        $this->_value = abs($this->_value);
        $this->_log('Set positive; New value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @return $this
     */
    public function invertSign()
    {
        if ($this->_value > 0) {
            $this->_value = -1 * $this->_value;
        } else if ($this->_value < 0) {
            $this->_value = abs($this->_value);
        }

        $this->_log('Invert sign; New value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @param int|float $number
     * @return $this
     */
    public function multiply($number)
    {
        $this->_value = $number * $this->_value;

        $this->_log('Multiply with "' . $number . '"; New value = "' . $this->dump() . '"');

        return $this;
    }

    /**
     * @param mixed  $data
     * @param string $forceCur
     * @return array
     */
    protected function _parse($data, $forceCur = null)
    {
        $this->_log('Parse data = "' . $data . '" with forceCur = "' . $forceCur . '"');

        $value    = null;
        $currency = null;

        $codes = $this->getCodeList();
        $reg   = '#(.*)(' . implode('|', $codes) . ')$#i';
        if (preg_match($reg, $data, $matches)) {
            $value    = $matches[1];
            $currency = $matches[2];
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
    protected function _logArray(array $data)
    {
        if ($this->_isDebug) {
            $removeData = array('Array', "\n", "\r", '     ');
            return str_replace($removeData, ' ', print_r($data, true));
        }
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
}


/**
 * Class JBCartValueException
 */
class JBCartValueException extends AppException
{

}