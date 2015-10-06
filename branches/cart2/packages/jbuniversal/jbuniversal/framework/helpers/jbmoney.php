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
     * @var JBCacheHelper
     */
    protected $_jbcache = array();

    /**
     * @var string
     */
    protected $_defaultCur = '';

    /**
     * @var string
     */
    protected $_currencyMode = 'default';

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
        'format_positive' => '%v%s',
        'format_negative' => '-%v%s',
    );

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_config     = JBModelConfig::model();
        $this->_defaultCur = $this->_config->getCurrency();

        $this->_currencyMode = $this->_config->get('undefined_currency', 'default', 'cart.config');
        if (empty($this->_currencyMode)) {
            $this->_currencyMode = 'default';
        }

        $this->_jbcache = $this->app->jbcache;
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
        $ttl       = (int)$this->_config->get('currency_ttl', 1440, 'cart.config');

        $cacheKey = $this->_jbcache->hash(array(
            'params' => (array)$curParams,
            'date'   => date('d-m-Y'),
            'ttl'    => $ttl,
            //'rand'   => time(), // debug mode =)
        ));

        self::$curList = $this->_jbcache->get($cacheKey, 'currency', true, array('ttl' => $ttl));
        if (empty(self::$curList)) {

            $elements = $this->app->jbcartposition->loadElements('currency');

            self::$curList = array(
                JBCartValue::DEFAULT_CODE => array(
                    'code'   => JBCartValue::DEFAULT_CODE,
                    'value'  => 1,
                    'name'   => JText::_('JBZOO_CURRENCY_DEFAULT_CODE'),
                    'format' => $this->_defaultFormat,
                ),
                self::PERCENT             => array(
                    'code'   => self::PERCENT,
                    'value'  => 1,
                    'name'   => JText::_('JBZOO_CART_CURRENCY_PERCENT'),
                    'format' => array_merge($this->_defaultFormat, array('symbol' => self::PERCENT)),
                )
            );

            $noCache = null; // fallback flag

            /** @type JBCartElementCurrency $element */
            foreach ($elements as $element) {

                $code = $element->getCode();
                if (empty($code)) {
                    continue;
                }

                try {

                    if ($noCache) { // speedup if fatal errors have appeared
                        $value = $element->getFallbackValue();
                    } else {
                        $value = $element->getValue($code);
                    }

                } catch (JBCartElementCurrencyException $e) {
                    $noCache = time();
                    $value   = $element->getFallbackValue();
                    if (JDEBUG || !$this->app->jbenv->isSite()) {
                        $this->app->jbnotify->warning($e->getMessage());
                    }
                }

                if ($code && $value > 0) {
                    self::$curList[$code] = array(
                        'code'   => $code,
                        'value'  => $value,
                        'name'   => $element->getName(),
                        'format' => $element->getFormat(),
                    );
                }

            }

            if (count(self::$curList) == 2 || !isset(self::$curList['eur'])) {
                self::$curList['eur'] = self::$curList[JBCartValue::DEFAULT_CODE];
            }

            if (!$noCache) {
                $this->_jbcache->set($cacheKey, self::$curList, 'currency', true, array('ttl' => $ttl));
            }
        }

        $this->app->jbdebug->mark('jbmoney::init::finish');

        return self::$curList;
    }

    /**
     * Clear price string
     * @deprecated plz, use app->jbvars->money
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
        $this->init();
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
                $codeName      = $code == JBCartValue::DEFAULT_CODE ? '' : ' (' . $code . ')';
                $result[$code] = $currency['name'] . $codeName;
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
        $this->init();
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
        return $this->app->jbvars->currency($currency, $default);
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
        $this->init();

        $currency = trim(strtolower($currency));
        if (isset(self::$curList[$currency])) {
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
        $this->init();
        return JBCart::val($value)->text();
    }

    /**
     * @return JSONData
     */
    public function getData()
    {
        static $result;

        // for speed only
        if (!isset($result)) {

            $this->init();
            $result = $this->app->data->create(self::$curList);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCurrencyMode()
    {
        return $this->_currencyMode;
    }

}
