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
 * Class JBCartElementCurrencyEuropeCB
 */
class JBCartElementCurrencyEuropeCB extends JBCartElementCurrency
{

    protected $_apiUrl = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * @param null $currency
     * @return array
     */
    public function _loadData($currency = null)
    {
        $result    = array();
        $xmlString = $this->_loadUrl($this->_apiUrl);
        if (empty($xmlString)) {
            return array();
        }

        if ($xml = simplexml_load_string($xmlString)) {
            foreach ($xml->Cube->Cube->Cube as $row) {

                $value = $this->_jbmoney->clearValue($row['rate']);
                $code  = strtolower(trim($row['currency']));

                $result[$code] = $value;
            }

            $result['eur'] = 1;
        }

        $result = $this->_normToDefault($result);

        return $result;
    }

}
