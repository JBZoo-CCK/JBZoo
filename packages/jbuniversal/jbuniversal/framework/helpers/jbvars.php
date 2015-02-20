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
 * Class JBVarsHelper
 */
class JBVarsHelper extends AppHelper
{

    /**
     * @param string $value
     * @param int    $round
     * @return float
     */
    public function money($value, $round = 10)
    {
        $value = trim($value);
        $value = str_replace(array(' ', ','), array('', '.'), $value);
        $value = (float)$value;
        $res   = round($value, $round);

        return $res;
    }

    /**
     * Convert string to lower chars
     * @param string $string
     * @param bool   $joomla
     * @return mixed|string
     */
    public function lower($string, $joomla = false)
    {
        if ($joomla) {
            $string = trim($string);
            $string = function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
        } else {
            $string = JString::trim($string);
            $string = JString::strtolower($string);
        }

        return $string;
    }

    /**
     * Convert string to lower chars
     * @param string $string
     * @param bool   $joomla
     * @return mixed|string
     */
    public function upper($string, $joomla = false)
    {
        if ($joomla) {
            $string = trim($string);
            $string = function_exists('mb_strtoupper') ? mb_strtoupper($string) : strtoupper($string);
        } else {
            $string = JString::trim($string);
            $string = JString::strtoupper($string);
        }

        return $string;
    }

    /**
     * @param $value
     * @return float
     */
    public function number($value)
    {
        return $this->money($value);
    }

    /**
     * @param $currency
     * @return bool|mixed|string
     */
    public function currency($currency)
    {
        $currency = $this->lower($currency, true);
        $rates    = $this->app->jbmoney->getData();
        if ($rates && $rates->get($currency)) {
            return $currency;
        }

        return false;
    }

}
