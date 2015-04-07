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
 * Class JBCartElementCurrencyCustom
 */
class JBCartElementCurrencyCBR extends JBCartElementCurrency
{

    protected $_apiUrl = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * Simple cache
     * @type array
     */
    protected $_curList = null;

    /**
     * Parse CBR XML
     * TODO: Sometimes SimpleXML doesn't work, so we used preg_matches
     * @param null $currency
     * @return array|void
     */
    public function _loadData($currency = null)
    {
        if (is_null($this->_curList)) {
            $this->_curList = array();

            $params = array();
            if ((int)$this->config->get('force_date', 1)) {
                $params['date_req'] = date("d/m/Y");
            }

            $xmlString = $this->_loadUrl($this->_apiUrl, $params);
            if (empty($xmlString)) {
                $xmlString = $this->app->jbhttp->url($this->_apiUrl, $params); // anti ban
            }

            if (empty($xmlString)) {
                return $this->_curList;
            }

            $xmlString = JString::trim(iconv("WINDOWS-1251", "UTF-8//TRANSLIT", $xmlString));

            preg_match_all('#<Valute(.*?)<\/Valute>#ius', $xmlString, $out);
            if (!empty($out) && isset($out[1])) {
                foreach ($out[1] as $row) {

                    preg_match("#<Value>(.*?)</Value>#ius", $row, $value);
                    preg_match("#<CharCode>(.*?)</CharCode>#ius", $row, $code);
                    preg_match("#<Nominal>(.*?)</Nominal>#ius", $row, $nominal);

                    $value   = $this->_jbmoney->clearValue($value[1]);
                    $nominal = trim(strtolower($nominal[1]));
                    $code    = trim(strtolower($code[1]));

                    $this->_curList[$code] = $value / $nominal;
                }

                $this->_curList['rub'] = 1;
            }

            $this->_curList = $this->_normToDefault($this->_curList);
        }

        return $this->_curList;
    }

    /**
     * Load URL with antiban system
     * @param string $url
     * @param array  $data
     * @param array  $params
     * @return mixed
     * @throws JBCartElementCurrencyException
     */
    protected function _loadUrl($url, $data = array(), $params = array())
    {
        $result = $this->app->jbhttp->request($url, $data, array(
            'response' => 'full',
            'follow'   => false,
        ));

        if (is_object($result) && $result->code == 200) {
            return $result->body;
        }

        if (isset($result->headers['Set-Cookie'])) {

            $cookie = array();

            if (preg_match_all('#(.*?)=(.*?)(\n|;)#i', $result->headers['Set-Cookie'], $matches)) {
                if (!empty($matches)) {
                    foreach ($matches[1] as $key => $value) {
                        $data[$value] = $matches[2][$key];
                        $cookie[]     = $value . '=' . $matches[2][$key];
                    }
                }
                $cookie = implode('; ', $cookie);
            }

            if ($cookie) {
                $params['headers'] = array('cookie' => $cookie);
            }
        }

        return parent::_loadUrl($url, $data, $params);
    }

}
