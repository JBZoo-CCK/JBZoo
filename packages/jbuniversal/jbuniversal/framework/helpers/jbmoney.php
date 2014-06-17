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
 * Class JBMoneyHelper
 */
class JBMoneyHelper extends AppHelper
{
    const BASE_CURRENCY = 'EUR'; // don't touch!

    static $curList = array();
    static $formatList = array();

    protected $_serviceGoogle = 'http://rate-exchange.appspot.com/currency';
    protected $_serviceCBR = 'http://www.cbr.ru/scripts/XML_daily.asp';
    protected $_servicePrivatBank = 'https://privat24.privatbank.ua/p24/accountorder?oper=prp&PUREXML&apicour&country=ua&full';
    protected $_serviceEuropecb = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    const MODE_NONE       = 'manual';
    const MODE_GOOGLE     = 'google';
    const MODE_CBR        = 'cbr';
    const MODE_PRIVATBANK = 'privatbank';
    const MODE_EUROPECB   = 'europecb';

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_config = JBModelConfig::model();
        $this->_init();
    }

    /**
     * Get all currency values and cache in memory
     */
    protected function _init()
    {
        $mode = $this->getMode();

        $this->app->jbdebug->mark('jbmoney::init::' . $mode . '-start');

        if (!empty(self::$curList)) {
            return false;
        }

        $cacheKey = implode('|', array(
            'mode-' . $mode,
            //'date-' . date('d-m-Y'),
        ));

        $cachedData = $this->app->jbcache->get($cacheKey, 'currency', true);
        if (empty($cachedData)) {

            $xml = simplexml_load_file($this->app->path->path('jbconfig:jbcurrency.xml'));

            // load format list
            foreach ($xml->formatlist->children() as $name => $format) {
                self::$formatList[$name] = array();
                foreach ($format->attributes() as $key => $value) {
                    self::$formatList[$name][$key] = (string)$value;
                }
            }

            // load currency list
            foreach ($xml->curencylist->children() as $code => $currency) {

                $code = JString::strtoupper($code);

                self::$curList[$code] = array('name' => JText::_('JBZOO_JBCURRENCY_' . $code));

                foreach ($currency->attributes() as $key => $value) {
                    self::$curList[$code][$key] = (string)$value;
                }

                self::$curList[$code]['value'] = $this->clearValue(self::$curList[$code]['value']);
            }

            $this->_mergeWithOnline();

            $data = array('cur' => self::$curList, 'formats' => self::$formatList);
            $this->app->jbcache->set($cacheKey, $data, 'currency', true);

        } else {
            self::$curList    = $cachedData['cur'];
            self::$formatList = $cachedData['formats'];
        }

        $this->app->jbdebug->mark('jbmoney::init::' . $mode . '-finish');
    }

    /**
     * Load currency values from service
     * Used hack for parse CBR xml. simplexml_load_string sometimes doesn't work
     * @return mixed
     */
    protected function _loadFromCBR()
    {
        $result    = array();
        $url       = $this->_serviceCBR . '?date_req=' . date("d.m.Y");
        $xmlString = $this->_loadByUrl($url);
        if (empty($xmlString)) {
            return array();
        }

        $xmlString = JString::trim(iconv("WINDOWS-1251", "UTF-8//TRANSLIT", $xmlString));

        preg_match_all('#<Valute(.*?)<\/Valute>#ius', $xmlString, $out);
        if (!empty($out) && isset($out[1])) {
            foreach ($out[1] as $row) {

                preg_match("#<Value>(.*?)</Value>#ius", $row, $value);
                preg_match("#<CharCode>(.*?)</CharCode>#ius", $row, $code);
                preg_match("#<Nominal>(.*?)</Nominal>#ius", $row, $nominal);

                $value   = (float)$this->clearValue($value[1]);
                $nominal = trim(strtoupper($nominal[1]));
                $code    = trim(strtoupper($code[1]));

                $result[$code] = $value / $nominal;
            }

            $result['RUB'] = 1;
            $baseValue     = $result[$this->getDefaultCur()];

            foreach ($result as $code => $value) {
                $result[$code] = $baseValue / $value;
            }
        }

        return $result;
    }

    /**
     * Load currency values from service
     * @return mixed
     */
    protected function _loadFromPrivateBank()
    {
        $result    = array();
        $xmlString = $this->_loadByUrl($this->_servicePrivatBank);
        if (empty($xmlString)) {
            return array();
        }

        if ($xml = simplexml_load_string($xmlString)) {

            foreach ($xml as $row) {
                $row = (array)$row;
                $row = $row['@attributes'];

                if (!isset($row['ccy'])) {
                    continue;
                }

                $unit  = trim($row['unit']) * 100;
                $value = $this->clearValue($row['buy']) / $unit;
                $code  = strtoupper(trim($row['ccy']));

                $result[$code] = $value;
            }

            $result['RUB'] = $result['RUR'];
            $baseValue     = $result[$this->getDefaultCur()];

            foreach ($result as $code => $value) {
                $result[$code] = $baseValue / $value;
            }

        }

        return $result;
    }

    /**
     * Load currency values from service
     * @return mixed
     */
    protected function _loadFromEuropeCB()
    {
        $result    = array();
        $xmlString = $this->_loadByUrl($this->_serviceEuropecb);
        if (empty($xmlString)) {
            return array();
        }

        if ($xml = simplexml_load_string($xmlString)) {
            foreach ($xml->Cube->Cube->Cube as $row) {
                $value = $this->clearValue($row['rate']);
                $code  = strtoupper(trim($row['currency']));

                $result[$code] = $value;
            }

            $result['EUR'] = 1;
            foreach ($result as $code => $value) {
                $result[$code] = $value;
            }
        }

        return $result;
    }

    /**
     * Load currency rate from Google serve
     * @return array
     */
    protected function _loadFromGoogle()
    {
        $defaultCur = $this->getDefaultCur();
        $result     = array($defaultCur => 1);

        foreach (self::$curList as $code => $currency) {

            $code = strtoupper($code);
            if ($code == $defaultCur) {
                continue;
            }

            $response = $this->_loadByUrl($this->_serviceGoogle . '?' . $this->app->jbrouter->query(array(
                    'from' => $defaultCur,
                    'to'   => $code,
                )));

            if ($response) {
                $data          = $this->app->data->create(json_decode($response));
                $result[$code] = (float)$this->clearValue($data->get('rate', 0));
            }

        }

        return $result;
    }

    /**
     * Load currency values from service
     * @return bool
     */
    protected function _mergeWithOnline()
    {
        $onlineMode = $this->getMode();
        if ($onlineMode == self::MODE_NONE) {
            return false;
        }

        if ($onlineMode == self::MODE_GOOGLE) {
            $result = $this->_loadFromGoogle();

        } else if ($onlineMode == self::MODE_CBR) {
            $result = $this->_loadFromCBR();

        } else if ($onlineMode == self::MODE_PRIVATBANK) {
            $result = $this->_loadFromPrivateBank();

        } else if ($onlineMode == self::MODE_EUROPECB) {
            $result = $this->_loadFromEuropeCB();
        }

        if (!empty($result) && is_array($result)) {
            foreach ($result as $code => $value) {
                $code = trim(strtoupper($code));
                if ($value > 0) {
                    self::$curList[$code]['value'] = $value;
                }
            }
        }
    }

    /**
     * Clear price string
     * @param $value
     * @return mixed|string
     */
    public function clearValue($value)
    {
        $value = (string)$value;
        $value = JString::trim($value);
        $value = preg_replace('#[^0-9\,\.\-\+]#ius', '', $value);

        if (preg_match('#^([\+\-]{0,1})([0-9\.\,]*)$#ius', $value, $matches)) {
            $value = str_replace(',', '.', $matches[2]);
            return $matches[1] . (float)$value;
        }

        return 0;
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
        $value = $this->clearValue($value);
        $from  = $this->clearCurrency($from);
        $to    = $this->clearCurrency($to);

        if ($from == $to) {
            return $value;
        }

        $result = 0;

        if (isset(self::$curList[$to]) && isset(self::$curList[$from])) {
            $normValue = $value / self::$curList[$from]['value'];
            $result    = $normValue * self::$curList[$to]['value'];
        }

        return $result;
    }

    /**
     * Currency list
     * @param bool $isShort
     * @return array
     */
    public function getCurrencyList($isShort = false)
    {
        $result = array();
        foreach (self::$curList as $code => $currency) {

            if ($isShort) {
                $result[$code] = $code;
            } else {
                $result[$code] = $code . ' - ' . JText::_('JBZOO_JBCURRENCY_' . $code);
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
        $value  = $this->clearValue($value);
        $format = self::$formatList['default'];

        if (empty($code)) {
            return number_format($value, $format['decimals'], $format['dec_point'], $format['thousands_sep']);
        }

        $code = JString::trim(JString::strtoupper($code));
        if ($code == '%') {

            $formated = 0;
            if (!empty($value)) {
                $formated = abs(number_format($value, $format['decimals'], $format['dec_point'], $format['thousands_sep']));
            }

            $sign = '';
            if ($value[0] == '+' || $value[0] == '-') {
                $sign = $value[0];
            }

            return $sign . $formated . '%';

        } else if (isset(self::$curList[$code])) {

            $params = self::$curList[$code];

            $formatNum = $params['format'];

            if (isset(self::$formatList['format_' . $formatNum])) {
                $format = self::$formatList['format_' . $formatNum];
            }

            if (empty($value)) {
                $value = 0;
            }

            $formated = number_format($value, $format['decimals'], $format['dec_point'], $format['thousands_sep']);

            return (!empty($params['prefix']) ? $params['prefix'] : '')
            . $formated
            . (!empty($params['postfix']) ? ' ' . $params['postfix'] : '');
        }

        return null;

    }

    /**
     * Check currency
     * @param $currency
     * @param string $default
     * @return string
     */
    public function clearCurrency($currency, $default = 'EUR')
    {
        $currency = trim(strtoupper($currency));

        if ($currency == '%') {
            return '%';
        }

        if (isset(self::$curList[$currency])) {
            return $currency;
        }

        return $default;
    }

    /**
     * Calculate total value
     * @param float $value
     * @param string $baseCurrency
     * @param float $addValue
     * @param string $currency
     * @return float
     */
    public function calc($value, $baseCurrency, $addValue, $currency)
    {
        $value        = $this->clearValue($value);
        $baseCurrency = $this->clearCurrency($baseCurrency);
        $addValue     = $this->clearValue($addValue);
        $currency     = $this->clearCurrency($currency, $baseCurrency);

        $sign = '';
        if ($addValue[0] == '-' || $addValue[0] == '+') {
            $sign = $addValue[0];
        }

        if ($currency == '%') {
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

        if ($currency == '%') {
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
     * To default number format
     * @param $value
     * @return string
     */
    public function toNumberFormat($value)
    {
        $value = $this->clearValue($value);
        return number_format($value, 0, '.', ' ');
    }

    /**
     * Get base currency
     * @return string
     */
    public function getDefaultCur()
    {
        return self::BASE_CURRENCY;
    }

    /**
     * Get current config mode
     * @return int
     */
    public function getMode()
    {
        $params = $this->app->jbconfig->getList('config.custom');
        $mode   = $params->get('currency_mode', 'manual');

        return $mode;
    }

    /**
     * Check if exists currency
     * @param  $currency
     * @return bool|string
     */
    public function checkCurrency($currency)
    {
        $currency = strtoupper(trim($currency));
        if (array_key_exists($currency, self::$curList)) {
            return $currency;
        }

        return false;
    }

    /**
     * @param $url
     * @return null|string
     */
    protected function _loadByUrl($url)
    {
        $httpClient = JHttpFactory::getHttp();

        try {
            $responce = $httpClient->get($url);
        } catch (Exception  $e) {
            return null;
        }

        if ($responce && $responce->code == 200) {
            return $responce->body;
        }

        return null;
    }

}
