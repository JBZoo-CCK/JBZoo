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
    const PERCENT = '%';

    static $curList = array();

    /**
     * @var JSONData
     */
    protected $_config = array();

    /**
     * @var string
     */
    protected $_defaultCur = '';

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

        $this->_config     = JBModelConfig::model();
        $this->_defaultCur = $this->_config->get('default_currency', 'eur', 'cart.config'); // TODO use constant
    }

    /**
     * Get all currency values and cache in memory
     */
    public function init()
    {
        // optimize
        if (!empty(self::$curList)) {
            return self::$curList;
        }

        $this->app->jbdebug->mark('jbmoney::init::start');

        $curParams = $this->_config->getGroup('cart.currency')->get('list');

        $cacheKey = serialize(array(
            'params' => (array)$curParams,
            'date'   => date('d-m-Y'),
            //'rand'   => time(),
        ));

        self::$curList = $this->app->jbcache->get($cacheKey, 'currency', true);
        if (empty(self::$curList)) {

            $elements = $this->app->jbcartposition->loadElements('currency');

            foreach ($elements as $element) {

                $code      = $element->getCode();
                $checkCode = $element->checkCurrency($code);

                if ($code && $checkCode) {

                    $value = $element->getValue($code);
                    if ($value <= 0) {
                        continue;
                    }

                    self::$curList[$code] = array(
                        'code'   => $code,
                        'value'  => $value,
                        'format' => $element->getFormat(),
                        'name'   => $element->getCurrencyName(),
                    );
                }
            }

            if (!empty(self::$curList)) {

                self::$curList[JBCartValue::DEFAULT_CODE] = array(
                    'code'   => JBCartValue::DEFAULT_CODE,
                    'name'   => JText::_('JBZOO_CURRENCY_DEFAULT_CODE'),
                    'value'  => 1,
                    'format' => array(),
                );

                self::$curList[self::PERCENT] = array(
                    'value'  => 1,
                    'code'   => self::PERCENT,
                    'name'   => JText::_('JBZOO_CART_CURRENCY_PERCENT'),
                    'format' => array_merge($this->_defaultFormat, array('symbol' => self::PERCENT)),
                );
            }

            $this->app->jbcache->set($cacheKey, self::$curList, 'currency', true);
        }

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
        return $this->_defaultCur;
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
     * @return array
     */
    public function getData()
    {
        $this->init();

        return $this->app->data->create(self::$curList);
    }

}