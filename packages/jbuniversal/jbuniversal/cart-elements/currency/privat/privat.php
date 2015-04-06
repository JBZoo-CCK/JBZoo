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
 * Class JBCartElementCurrencyPrivat
 */
class JBCartElementCurrencyPrivat extends JBCartElementCurrency
{
    /**
     * Service URL
     * @var string
     */
    protected $_apiUrl = 'https://privat24.privatbank.ua/p24/accountorder?oper=prp&PUREXML&apicour&country=ua&full';

    /**
     * @param null $currency
     * @return array|mixed
     */
    public function _loadData($currency = null)
    {
        $xmlString = $this->_loadUrl($this->_apiUrl, array(), array(
            'driver' => 'socket' // curl can't check ssl cert
        ));

        if (empty($xmlString)) {
            return array();
        }

        $result = array();
        if ($xml = simplexml_load_string($xmlString)) {

            foreach ($xml as $row) {
                $row = (array)$row;
                $row = $row['@attributes'];

                if (!isset($row['ccy'])) {
                    continue;
                }

                $unit  = trim($row['unit']) * 100;
                $value = $this->app->jbvars->money($row['buy']) / $unit;
                $code  = strtolower(trim($row['ccy']));

                $result[$code] = $value;
            }

            $result['rub'] = $result['rur'];
            $result['uah'] = 100;
        }

        $result = $this->_normToDefault($result);

        return $result;
    }

}
