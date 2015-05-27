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
     * @param $value
     * @return mixed|string
     */
    public function phone($value)
    {
        $value = preg_replace('#[^0-9]#ius', '', $value);
        $value = JString::trim((string)$value);

        return $value;
    }

    /**
     * Convert string to lower chars
     * @param string      $string
     * @param bool        $noJoomla
     * @param string|bool $trimList
     * @return mixed|string
     */
    public function lower($string, $noJoomla = false, $trimList = false)
    {
        if (is_array($string)) {
            foreach ($string as $key => $value) {
                $string[$key] = $this->lower($value, $noJoomla, $trimList);
            }

            return $string;
        }

        if ($noJoomla) {
            $trimList = $trimList !== false ? $trimList : " \t\n\r\0\x0B"; // PHP system default

            $string = trim($string, $trimList);
            $string = function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
        } else {
            $string = JString::trim($string, $trimList);
            $string = JString::strtolower($string);
        }

        return $string;
    }

    /**
     * Convert string to upper chars
     * @param string      $string
     * @param bool        $noJoomla
     * @param string|bool $trimList
     * @return mixed|string
     */
    public function upper($string, $noJoomla = false, $trimList = false)
    {
        if (is_array($string)) {
            foreach ($string as $key => $value) {
                $string[$key] = $this->upper($value, $noJoomla, $trimList);
            }

            return $string;
        }

        if ($noJoomla) {
            $trimList = $trimList !== false ? $trimList : " \t\n\r\0\x0B"; // PHP system default

            $string = trim($string, $trimList);
            $string = function_exists('mb_strtoupper') ? mb_strtoupper($string) : strtoupper($string);
        } else {
            $string = JString::trim($string, $trimList);
            $string = JString::strtoupper($string);
        }

        return $string;
    }

    /**
     * @param string $value
     * @param int    $round
     * @return float
     */
    public function number($value, $round = 10)
    {
        return $this->money($value, $round);
    }

    /**
     * @param string $currency
     * @param bool   $default
     * @return bool|mixed|string
     */
    public function currency($currency, $default = false)
    {
        $currency = $this->lower($currency, true);
        $rates    = $this->app->jbmoney->getData();
        if ($rates && $rates->get($currency)) {
            return $currency;
        }

        return $default;
    }

    /**
     * @param $email
     * @return bool|string
     */
    public function email($email)
    {
        $email = JString::trim($email);

        // like in JFormRuleEmail
        $regex = chr(1) . '^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$' . chr(1) . 'u';

        if (preg_match($regex, $email)) {
            return $email;
        }

        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    public function bool($value)
    {
        static $trueValues;

        if (is_null($trueValues)) {
            $trueValues = array_unique(array(
                '1',
                'y',
                'yes',
                'true',
                'да',
                'on',
                $this->lower(JText::_('YES')),
                $this->lower(JText::_('JYES')),
                $this->lower(JText::_('JBZOO_YES'))
            ));
        }

        $value = $this->lower($value);

        if (in_array($value, $trueValues, true)) {
            return true;
        }

        if ($this->number($value) >= 1) {
            return true;
        }

        return false;
    }

}
