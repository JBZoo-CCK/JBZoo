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
 * Class JBCartElementCurrency
 */
abstract class JBCartElementCurrency extends JBCartElement
{

    const BASE_CURRENCY = 'eur'; // don't touch!

    /**
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_CURRENCY;

    /**
     * @var array|null
     */
    protected $_curList = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

    /**
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbmoney = $this->app->jbmoney;
    }

    /**
     * Load data from service and cache it
     */
    public function getData($currency = null)
    {
        $mode = strtolower(get_class($this));
        $this->app->jbdebug->mark('JBMoney::getData::' . $mode . '-start / ' . $currency);

        $data = $this->_loadData($currency);

        $this->app->jbdebug->mark('JBMoney::getData::' . $mode . '-finish / ' . $currency);

        return $data;
    }

    /**
     * Load data fron service
     * @param $currency
     * @return array
     */
    abstract protected function _loadData($currency = null);

    /**
     * Simple load URL with Joomla API
     * @param string $url
     * @param array  $data
     * @param array  $params
     * @return mixed
     * @throws JBCartElementCurrencyException
     */
    protected function _loadUrl($url, $data = array(), $params = array())
    {
        $params['response'] = 'full';
        $params['timeout']  = 5;
        $params['debug']    = JDEBUG;
        $params['follow']   = false;

        $result = $this->app->jbhttp->request($url, $data, $params);

        if (empty($result) || !isset($result->code) || $result->code != 200) {

            $code     = $this->getCode();
            $elemName = $this->getName() . ' (' . $this->getElementType() . ', ' . $code . ')';
            $fallback = '1 eur = ' . $this->getFallbackValue() . ' ' . $code;

            $message = JText::sprintf('JBZOO_ELEMENT_CURRENCY_NO_CONNECT', $elemName, $fallback);

            if (JDEBUG) {
                if (is_object($result) && isset($result->body)) { // for server response (error text)
                    $reason = $result->body;
                } else { // for exception
                    $reason = $result;
                }

                $reason = JString::trim(strip_tags($reason));
                if ($reason) {
                    $reason = JString::substr($reason, 0, 200);
                    $message .= '<br>' . JText::sprintf('JBZOO_ELEMENT_CURRENCY_NO_CONNECT_REASON', $reason);
                }
            }

            throw new JBCartElementCurrencyException($message);
        }

        return $result->body;
    }

    /**
     * @param $currency
     * @return mixed
     */
    public function checkCurrency($currency)
    {
        $this->_curList = $this->getData($currency);

        return isset($this->_curList[$currency]);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        $code = $this->config->get('code');
        $code = trim($code);
        $code = strtolower($code);

        return $code;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function _normToDefault($data)
    {
        if (empty($data)) {
            return array();
        }

        $baseCur = self::BASE_CURRENCY;

        $baseValue = 1;
        if (isset($data[$baseCur])) {
            $baseValue = $data[$baseCur];
        }

        if ($baseValue != 1) {
            foreach ($data as $code => $value) {
                $data[$code] = $baseValue / $value;
            }
        }

        return $data;
    }

    /**
     * @param $currency
     * @return float
     */
    public function getValue($currency)
    {
        $jbvars   = $this->app->jbvars;
        $currency = $jbvars->lower($currency);

        if ($currency == self::BASE_CURRENCY) {
            return 1;
        }

        $data = $this->getData($currency);
        if (isset($data[$currency])) {
            return $jbvars->money($data[$currency]);
        }

        return $this->getFallbackValue();
    }

    /**
     * @return mixed
     */
    public function getFallbackValue()
    {
        return $this->app->jbvars->money($this->config->get('fallback_rate', 1));
    }

    /**
     * Get money print format
     * @return array
     */
    public function getFormat()
    {
        return array(
            'symbol'          => $this->config->get('symbol', ''),
            'round_type'      => $this->config->get('round_type', 'none'),
            'round_value'     => (int)$this->config->get('round_value', 6),
            'num_decimals'    => (int)$this->config->get('num_decimals', 2),
            'decimal_sep'     => $this->config->get('decimal_sep', '.'),
            'thousands_sep'   => $this->config->get('thousands_sep', ' '),
            'format_positive' => $this->config->get('format_positive', '%v %s'),
            'format_negative' => $this->config->get('format_negative', '-%v %s'),
        );
    }

}

/**
 * Class JBCartElementCurrencyException
 */
class JBCartElementCurrencyException extends JBCartElementException
{
}
