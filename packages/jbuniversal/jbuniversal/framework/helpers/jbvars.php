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
        $value = (string)$value;
        $value = trim($value);
        $value = preg_replace('#[^0-9\,\.\-\+]#ius', '', $value);

        if (preg_match('#^([\+\-]{0,1})([0-9\.\,]*)$#ius', $value, $matches)) {
            $value = str_replace(',', '.', $matches[2]);

            $value = (float)($matches[1] . (float)$value);

            return round($value, $round);
        }

        return 0.0;
    }

    /**
     * Convert string to lower chars
     * @param string $string
     * @param bool   $speedUp
     * @return mixed|string
     */
    public function lower($string, $speedUp = false)
    {
        if ($speedUp) {
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
     * @param bool   $speedUp
     * @return mixed|string
     */
    public function upper($string, $speedUp = false)
    {
        if ($speedUp) {
            $string = trim($string);
            $string = function_exists('mb_strtoupper') ? mb_strtoupper($string) : strtoupper($string);
        } else {
            $string = JString::trim($string);
            $string = JString::strtoupper($string);
        }

        return $string;
    }

}
