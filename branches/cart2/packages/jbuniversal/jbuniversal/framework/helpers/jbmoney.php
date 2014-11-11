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
 * Class JBMoneyHelper
 */
class JBMoneyHelper extends AppHelper
{
    const BASE_CURRENCY = 'EUR'; // don't touch!
    const PERCENT       = '%';

    static $curList = array();

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @var boolean
     */
    protected $_isBuilded = false;

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
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_config = JBModelConfig::model();
    }

    /**
     * Get all currency values and cache in memory
     */
    public function init()
    {
        // optimize
        if ($this->_isBuilded || !empty(self::$curList)) {
            return self::$curList;
        }

        $this->app->jbdebug->mark('jbmoney::init::start');

        $curParams = $this->_config->getGroup('cart.currency')->get('list');

        $cacheKey = serialize(array(
            'params' => (array)$curParams,
            'date'   => date('d-m-Y'),
            //'debug'  => time(),
        ));

        self::$curList = $this->app->jbcache->get($cacheKey, 'currency', true);
        if (empty(self::$curList)) {

            $elements = $this->app->jbcartposition->loadElements('currency');

            foreach ($elements as $element) {

                $code = $element->getCode();

                if ($code && $element->checkCurrency($code)) {

                    self::$curList[$code] = array(
                        'code'   => $code,
                        'name'   => $element->getCurrencyName(),
                        'value'  => $element->getValue($code),
                        'format' => $element->getFormat(),
                    );

                }
            }

            if (empty(self::$curList)) { // TODO it doesn't work!!!
                $defaultCur = $this->getDefaultCur();

                self::$curList [$defaultCur] = array(
                    'value'  => 1,
                    'code'   => $defaultCur,
                    'name'   => JText::_('JBZOO_CART_CURRENCY_DEFAULT'),
                    'format' => $this->_defaultFormat,
                );
            }

            self::$curList[self::PERCENT] = array(
                'value'  => null,
                'code'   => self::PERCENT,
                'name'   => JText::_('JBZOO_CART_CURRENCY_PERCENT'),
                'format' => array_merge($this->_defaultFormat, array('symbol' => self::PERCENT)),
            );

            $this->app->jbcache->set($cacheKey, self::$curList, 'currency', true);
        }

        $this->_isBuilded = true;

        $this->app->jbdebug->mark('jbmoney::init::finish');

        return self::$curList;
    }

    /**
     * Clear price string
     * @param $value
     * @return float
     */
    public function clearValue($value)
    {
        return $this->app->jbvars->money($value);
    }

    /**
     * Convert currency
     * @param $from    string
     * @param $to      string
     * @param $value   float
     * @return mixed
     */
    public function convert($from, $to, $value)
    {
        return JBCart::val($value, $from)->val($to);
    }

    /**
     * Currency list
     * @param bool $isShort
     * @return array
     */
    public function getCurrencyList($isShort = false)
    {
        $this->init();

        $result = array();

        if (empty(self::$curList)) {
            return $result;
        }

        foreach (self::$curList as $code => $currency) {

            if ($isShort) {
                $result[$code] = $code;
            } else {
                $result[$code] = $currency['name'] . ' (' . $code . ')';
            }
        }

        return $result;
    }

    /**
     * convert number to money formated string
     * @param $value
     * @param $code
     * @return null|string
     */
    public function toFormat($value, $code = null)
    {
        return JBCart::val($value, $code)->text();
    }


    /**
     * Check currency
     * @param        $currency
     * @param string $default
     * @return string
     */
    public function clearCurrency($currency, $default = null)
    {
        $this->init();

        $currency = $this->app->jbvars->lower($currency, true);

        if ($currency == self::PERCENT) {
            return self::PERCENT;
        }

        if (isset(self::$curList[$currency])) {
            return $currency;
        }

        return $default;
    }

    /**
     * Get base currency
     * @return string
     */
    public function getDefaultCur()
    {
        if (!class_exists('JBCartElementCurrency')) {
            $this->app->jbcartelement; // just init constructor
        }

        return JBCartElementCurrency::BASE_CURRENCY;
    }

    /**
     * Check if exists currency
     * @param  $currency
     * @return bool|string
     */
    public function checkCurrency($currency)
    {
        $currency = trim(strtolower($currency));
        if (array_key_exists($currency, self::$curList)) {
            return $currency;
        }

        return false;
    }

    /**
     * @param       $value
     * @return mixed
     */
    public function format($value)
    {
        return JBCart::val($value)->text();
    }

    /**
     * Calculate total value
     * @param float  $value
     * @param string $baseCurrency
     * @param float  $addValue
     * @param string $currency
     * @return float
     */
    public function calc($value, $baseCurrency, $addValue, $currency)
    {
        $value    = $this->clearValue($value);
        $addValue = $this->clearValue($addValue);

        $baseCurrency = $this->clearCurrency($baseCurrency);
        $currency     = $this->clearCurrency($currency, $baseCurrency);

        $sign = '';
        if ($addValue[0] == '-' || $addValue[0] == '+') {
            $sign = $addValue[0];
        }

        if ($currency == self::PERCENT) {
            $addValue = (float)($sign . abs($value * $addValue / 100));
        } else {
            $addValue = (float)($sign . abs($this->convert($currency, $baseCurrency, $addValue)));
        }

        if ($sign == '-' || $sign == '+') {
            $result = $value + $addValue;
        } else {
            $result = $addValue;
        }

        if ($result <= 0) {
            return 0;
        }

        return $result;
    }

    /**
     * Calculate with discount value
     * @param $value
     * @param $baseCurrency
     * @param $addValue
     * @param $currency
     * @return float
     */
    public function calcDiscount($value, $baseCurrency, $addValue, $currency)
    {
        $value        = $this->clearValue($value);
        $baseCurrency = $this->clearCurrency($baseCurrency);
        $addValue     = $this->clearValue($addValue);
        $currency     = $this->clearCurrency($currency, $baseCurrency);

        if ($currency == self::PERCENT) {
            $addValue = $value * $addValue / 100;
        } else {
            $addValue = $this->convert($currency, $baseCurrency, $addValue);
        }

        $result = $value + $addValue;
        if ($result <= 0) {
            return 0;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->init();

        return $this->app->data->create(self::$curList);
    }

}