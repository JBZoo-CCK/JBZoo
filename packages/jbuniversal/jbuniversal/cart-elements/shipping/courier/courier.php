<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingCourier extends JBCartElementShipping
{
    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M';
    const FORMAT_WEEKDAYS  = 'l, j M';
    const FORMAT_HOURS     = 'H:i';

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return null;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $fulldate = $value->get('fulldate');

        if ((int)$this->config->get('fulldate', '0')) {
            $fulldate = $this->app->validator->create('date')
                ->addOption('date_format', self::EDIT_DATE_FORMAT)
                ->clean($fulldate);
        }

        $date = new JDate($fulldate);

        return array(
            'value'    => $this->getRate(),
            'fulldate' => $date->format('D, d M Y'),
            'hour'     => $value->get('hour', '12:00'),
            'weekday'  => $value->get('weekday'),
        );
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val($this->config->get('cost', 0));
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        parent::loadAssets();
        $this->app->jbassets->calendar();

        return $this;
    }

    /**
     * @return string
     */
    protected function _renderWeekdays()
    {
        $mode = $this->config->get('weekday', 'all');

        $days = array();
        if ($mode == 'all') {
            $days = array(1, 2, 3, 4, 5, 6, 7);

        } else if ($mode == 'weekdays') {
            $days = array(1, 2, 3, 4, 5);

        } else if ($mode == 'weekend') {
            $days = array(6, 7);
        }

        $now  = time();
        $list = array();
        for ($i = 1; $i <= 7; $i++) {
            $time = $now + 86400 * $i;
            $date = new JDate($time);
            if (in_array($date->format('N'), $days)) {
                $dateStr        = $date->format(self::FORMAT_WEEKDAYS);
                $list[$dateStr] = $dateStr;
            }
        }

        $tomorrow = new JDate($now + 86400);
        $weekday  = $this->get('weekday', $tomorrow->format(self::FORMAT_WEEKDAYS));

        return $this->app->jbhtml->select($list, $this->getControlName('weekday'), 'class="jsWeekday"', $weekday);
    }

    /**
     * @return string
     */
    protected function _renderHours()
    {
        $mode = $this->config->get('hour', 'daytime');

        $hours = array();
        if ($mode == 'all') {
            $hours = range(0, 23);

        } else if ($mode == 'daytime') {
            $hours = range(9, 18);
        }

        $list = array();
        for ($i = 0; $i <= 23; $i++) {

            $date = new JDate(mktime($i, 0, 0, 1, 1, 1970));

            if (in_array($date->format('G'), $hours)) {
                $dateStr        = $date->format(self::FORMAT_HOURS);
                $list[$dateStr] = $dateStr;
            }
        }

        ksort($list);

        return $this->app->jbhtml->select($list, $this->getControlName('hour'), 'class="jsHour"', $this->get('hour', '12:00'));
    }

    /**
     * @return string
     */
    protected function _renderCalendar()
    {
        if (!(int)$this->config->get('fulldate', '0')) {
            return null;
        }

        $name   = $this->getControlName('fulldate');
        $uniqid = $this->app->jbstring->getId('fulldate');
        $attrs  = 'class="calendar-input jsFulldate" placeholder="' . JText::_('JBZOO_SHIPPING_COURIER_TIME_DELIVERY') . '"';
        $value  = $this->get('fulldate', date('Y-m-d', time() + 186400));

        return '<div class="shipping-courier">' . $this->app->html->_('zoo.calendar', $value, $name, $uniqid, $attrs, false) . '</div>';
    }

}
