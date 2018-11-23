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
 * Class JBCartElementCurrencyEuropeCB
 */
class JBCartElementCurrencyEuropeCB extends JBCartElementCurrency
{

    protected $_apiUrl = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

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
