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
 * Class JBStringHelper
 */
class JBStringHelper extends AppHelper
{

    const MAX_LENGTH = 30;
    const ENCODING   = 'UTF-8';

    /**
     * Get sub string (by words)
     *
     * @param $text
     * @param $searchword
     *
     * @return mixed|string
     */
    public function smartSubstr($text, $searchword)
    {
        $length      = self::MAX_LENGTH;
        $textlen     = JString::strlen($text);
        $lsearchword = JString::strtolower($searchword);
        $wordfound   = false;
        $pos         = 0;
        $chunk       = '';

        while ($wordfound === false && $pos < $textlen) {

            if (($wordpos = @JString::strpos($text, ' ', $pos + $length)) !== false) {
                $chunk_size = $wordpos - $pos;
            } else {
                $chunk_size = $length;
            }

            $chunk     = JString::substr($text, $pos, $chunk_size);
            $wordfound = JString::strpos(JString::strtolower($chunk), $lsearchword);

            if ($wordfound === false) {
                $pos += $chunk_size + 1;
            }
        }

        if ($wordfound !== false) {
            return (($pos > 0) ? '...' : '') . $chunk;

        } elseif (($wordpos = @JString::strpos($text, ' ', $length)) !== false) {
            return JString::substr($text, 0, $wordpos) . '...';

        } else {
            return JString::substr($text, 0, $length);
        }
    }


    /**
     * Get truncated string (by words)
     *
     * @param $string
     * @param $maxlen
     *
     * @return string
     */
    public function cutByWords($string, $maxlen = 255)
    {

        $len    = (JString::strlen($string) > $maxlen) ? JString::strrpos(JString::substr($string, 0, $maxlen), ' ')
            : $maxlen;
        $cutStr = JString::substr($string, 0, $len);

        return (JString::strlen($string) > $maxlen) ? $cutStr . '...' : $cutStr;
    }

    /**
     * @param int    $n      число
     * @param string $only   Единственная форма: 1 секунда
     * @param string $dual   Двойственная форма: 2 секунды
     * @param string $plural Множественная форма: 5 секунд
     *
     * @return string Правильная форма
     */
    function declension($n, $only, $dual, $plural)
    {
        $n  = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $plural;
        if ($n1 > 1 && $n1 < 5) return $dual;
        if ($n1 == 1) return $only;

        return $plural;
    }

    /**
     * Parse text by lines
     *
     * @param string $text
     *
     * @return array
     */
    public function parseLines($text)
    {
        $text  = JString::trim($text);
        $text  = htmlspecialchars_decode($text);
        $text  = strip_tags($text);

        //$text  = addslashes($text);
        $lines = explode("\n", $text);

        $result = array();
        if (!empty($lines)) {

            foreach ($lines as $line) {

                $line = JString::trim($line);
                    $result[$line] = $line;

            }
        }

        //$result = array_filter($result);
        $result = array_unique($result);

        return $result;
    }

    /**
     * Get unique string
     *
     * @param  string $prefix
     *
     * @return string
     */
    public function getId($prefix = 'unique-')
    {
        $unique = $prefix . mt_rand(1, 999999);

        return $unique;
    }

    /**
     * Clean value
     *
     * @param  string $value
     * @param  string $encoding
     *
     * @return string
     */
    public function clean($value, $encoding = self::ENCODING)
    {
        $value = JString::trim($value);
        $value = strip_tags($value);

        return htmlentities($value, ENT_COMPAT, $encoding);
    }

}