<?php
use Joomla\String\StringHelper;
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
 * Class JBStringHelper
 */
class JBStringHelper extends AppHelper
{

    const MAX_LENGTH = 30;

    /**
     * Get sub string (by words)
     * @param $text
     * @param $searchword
     * @return mixed|string
     */
    public function smartSubstr($text, $searchword)
    {
        $length      = self::MAX_LENGTH;
        $textlen     = StringHelper::strlen($text);
        $lsearchword = StringHelper::strtolower($searchword);
        $wordfound   = false;
        $pos         = 0;
        $chunk       = '';

        while ($wordfound === false && $pos < $textlen) {

            if (($wordpos = @StringHelper::strpos($text, ' ', $pos + $length)) !== false) {
                $chunk_size = $wordpos - $pos;
            } else {
                $chunk_size = $length;
            }

            $chunk     = StringHelper::substr($text, $pos, $chunk_size);
            $wordfound = StringHelper::strpos(StringHelper::strtolower($chunk), $lsearchword);

            if ($wordfound === false) {
                $pos += $chunk_size + 1;
            }
        }

        if ($wordfound !== false) {
            return (($pos > 0) ? '...' : '') . $chunk;

        } elseif (($wordpos = @StringHelper::strpos($text, ' ', $length)) !== false) {
            return StringHelper::substr($text, 0, $wordpos) . '...';

        } else {
            return StringHelper::substr($text, 0, $length);
        }
    }


    /**
     * Get truncated string (by words)
     * @param $string
     * @param $maxlen
     * @return string
     */
    public function cutByWords($string, $maxlen = 255)
    {
        $len = (StringHelper::strlen($string) > $maxlen)
            ? StringHelper::strrpos(StringHelper::substr($string, 0, $maxlen), ' ')
            : $maxlen;

        $cutStr = StringHelper::substr($string, 0, $len);

        return (StringHelper::strlen($string) > $maxlen) ? $cutStr . '...' : $cutStr;
    }

    /**
     * Parse text by lines
     * @param string $text
     * @return array
     */
    public function parseLines($text)
    {
        $text = StringHelper::trim($text);
        $text = htmlspecialchars_decode($text);
        $text = strip_tags($text);

        //$text  = addslashes($text);
        $lines = explode("\n", $text);

        $result = array();
        if (!empty($lines)) {

            foreach ($lines as $line) {

                $line          = StringHelper::trim($line);
                $result[$line] = $line;

            }
        }

        //$result = array_filter($result);
        $result = array_unique($result);

        return $result;
    }

    /**
     * Get unique string
     * @param  string $prefix
     * @return string
     */
    public function getId($prefix = 'unique')
    {
        $prefix = rtrim(trim($prefix), '-');
        $random = mt_rand(100000, 999999);

        $result = $random;
        if ($prefix) {
            $result = $prefix . '-' . $random;
        }

        return $result;
    }

    /**
     * Clean value
     * @param  string $value
     * @param  string $encoding
     * @return string
     */
    public function clean($value, $encoding = 'UTF-8')
    {
        $value = StringHelper::trim($value);
        $value = strip_tags($value);

        return htmlentities($value, ENT_COMPAT, $encoding);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return null|string
     */
    public function _($key, $default = null)
    {
        $key    = trim(strtoupper($key));
        $result = JText::_($key);

        if (!JFactory::getLanguage()->hasKey($key) || !$result) {
            $result = $key;

            if ($default) {
                $result = $default;
            }
        }

        return $result;
    }

}