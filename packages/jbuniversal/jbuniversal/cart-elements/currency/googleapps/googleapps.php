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
 * Class JBCartElementCurrencyCustom
 */
class JBCartElementCurrencyGoogleApps extends JBCartElementCurrency
{
    /**
     * @var string
     */
    protected $_serviceGoogle = 'http://rate-exchange.appspot.com/currency';

    /**
     * Load data fron service
     * @param $currency
     * @return array
     */
    protected function _loadData($currency = null)
    {
        $defaultCur = $this->_jbmoney->getDefaultCur();
        $result     = array($defaultCur => 1.0);

        if ($currency == $defaultCur) {
            return $result;
        }

        $url = $this->_serviceGoogle . '?' . $this->app->jbrouter->query(array(
                'from' => $defaultCur,
                'to'   => $currency,
            ));

        $response = $this->_loadUrl($url);
        if ($response) {
            $data              = $this->app->data->create(json_decode($response));
            $result[$currency] = $this->_jbmoney->clearValue($data->get('rate', 0));
        }

        $result = $this->_normToDefault($result);

        return $result;
    }

}
