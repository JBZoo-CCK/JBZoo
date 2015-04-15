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
 * Class JBPriceFilterElementDate
 */
class JBPriceFilterElementDate extends JBPriceFilterElement
{
    /**
     * Check is has value
     */
    public function hasValue()
    {
        return false;
    }

    /**
     * Render HTML
     * @return string
     */
    function html()
    {
        return $this->_html->calendar(
            $this->_getName(),
            $this->_value,
            $this->_attrs,
            $this->_getId(),
            $this->_getPickerParams()
        );
    }

    /**
     * Get date format from config
     * @return string
     */
    protected function _getTimeformat()
    {
        return $this->_params->get('jbzoo_date_timeformat', 'hh:mm:ss');
    }

    /**
     * Get time format from config
     * @return string
     */
    protected function _getDateformat()
    {
        return $this->_params->get('jbzoo_date_dateformat', 'yy-mm-dd');
    }

    /**
     * Get params fo JS widget in JSON format
     * @return array
     */
    protected function _getPickerParams()
    {
        $result = array();

        if ($this->_getDateformat()) {
            $result['dateFormat'] = $this->_getDateformat();
        } else {
            $result['dateFormat'] = false;
        }

        if ($this->_getTimeformat()) {
            $result['timeFormat'] = $this->_getTimeformat();
        } else {
            $result['timeFormat'] = '';
            $result['showSecond'] = false;
            $result['showMinute'] = false;
            $result['showHour']   = false;
        }

        return $result;
    }

    /**
     * Get element attrs
     * @param array $attrs
     * @return mixed
     */
    protected function _getAttrs(array $attrs)
    {
        $attrs['class'][] = 'element-datepicker';

        return $attrs;
    }
}