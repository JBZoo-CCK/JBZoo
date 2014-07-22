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
 * Class JBCartElementCurrencyLocalXML
 */
class JBCartElementCurrencyLocalXML extends JBCartElementCurrency
{
    /**
     * Load data fron service
     * @param $currency
     * @return array
     */
    protected function _loadData($currency = null)
    {

        $path = trim($this->config->get('rate'));
        if (strpos($path, ':')) {
            $path = $this->app->path->path($path);
            $path = JPath::clean($path);
        } else {
            $path = JPATH_ROOT . '/' . $path;
        }

        $path = JPath::clean($path);

        if (!$path || !JFile::exists($path)) {
            return array();
        }

        $xml = simplexml_load_file($path);
        if (!$xml) {
            return array();
        }

        // load currency list
        foreach ($xml->curencylist->children() as $code => $currency) {

            $code = strtolower($code);

            foreach ($currency->attributes() as $value) {
                $result[$code] = (string)$value;
            }

            $result[$code] = $this->_jbmoney->clearValue($result[$code]);
        }

        $result = $this->_normToDefault($result);

        return $result;
    }


}
