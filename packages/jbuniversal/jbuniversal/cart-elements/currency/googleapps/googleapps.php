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
