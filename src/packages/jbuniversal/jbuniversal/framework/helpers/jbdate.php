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
        $dates = explode("\n", StringHelper::trim($date));

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
        if ($date !== null && strlen($date) > 4 && strtotime($date) > 0) {
            // Your logic here
            return true;
        } else {
            return false;
        }
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
