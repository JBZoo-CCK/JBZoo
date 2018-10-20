<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
    protected $_apiUrl = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5';
    // old https://privat24.privatbank.ua/p24/accountorder?oper=prp&PUREXML&apicour&country=ua&full

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
               
                $row = $row->exchangerate;
                  
                if (!isset($row['ccy'])) {
                    continue;
                }
               
                $unit  = 100; // trim($row['unit']) * 100
                $value = $this->app->jbvars->money($row['buy']);
                $code  = strtolower(trim($row['ccy']));
                
                $result[$code] = $value;
            }
        
            $result['rub'] = $result['rur'];
            $result['uah'] = '100';
        
        }

        $result = $this->_normToDefault($result);
       
        return $result;
    }
        
}
