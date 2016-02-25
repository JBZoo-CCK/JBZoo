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
 * Class JBDateHelper
 */
class JBDateHelper extends AppHelper
{
    /**
     * @var string
     */
    public $regDate = '#^(\d{4})([- /.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])$#ius';

    /**
     * @var string
     */
    public $regDatetime = '#^(\d{4})([- /.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])\s*([0][0-9]|[1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$#ius';

    /**
     * Validate string as date or datetime
     * @param string $date
     * @return int|null
     */
    public function convertToStamp($date)
    {
        $dates = explode("\n", JString::trim($date));

        $result = array();

        if (!empty($dates)) {
            foreach ($dates as $date) {

                if (!preg_match("#\\d{4}#", $date)) {
                    continue;
                }

                if ($time = strtotime($date)) {
                    $result[] = $this->toMysql($time);
                }
            }
        }

        return $result;
    }

    /**
     * Convert time for mysql
     * @param null|int $time
     * @return string
     */
    public function toMysql($time = null)
    {
        if (is_null($time)) {
            $time = time();
        }

        if (!empty($time)) {
            if (is_numeric($time)) {
                $time = (int)$time;
            } else {
                $time = strtotime($time);
            }
        }

        if ($time) {
            return date('Y-m-d H:i:s', $time);
            //(string)JFactory::getDate($time, $this->app->date->getOffset())
        }

        return null;
    }

    /**
     * @param $date
     * @return bool
     */
    public function isDate($date)
    {
        return strlen($date) > 4 && strtotime($date) > 0;
    }

    /**
     * @param string|int $date
     * @param string     $format
     * @return string
     */
    public function toHuman($date, $format = 'DATE_FORMAT_LC2')
    {
        if (is_numeric($date) && $date > 0) {
            $date = $this->toMysql($date);
        }

        if ($this->isDate($date)) {
            $date = $this->app->html->_('date', $date, JText::_($format), $this->app->date->getOffset());
        }

        return $date;
    }

    /**
     * @param string $default
     * @return string
     */
    public function getCurrent($default = null)
    {
        $tzOffset = $this->app->date->getOffset();

        try {
            $default = $this->app->date->create($default, $tzOffset);
            $current = $default->toSQL();
        } catch (Exception $e) {
            $now     = $this->app->date->create();
            $current = $now->toSQL();
        }

        return $current;
    }
}
