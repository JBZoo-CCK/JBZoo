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
 * Class JBFilterElementDateRange
 */
class JBPriceFilterElementDateRange extends JBPriceFilterElementDate
{
    /**
     * Render HTML
     * @return string
     */
    function html()
    {
        $html = array();

        if (is_string($this->_value)) {
            $value = array($this->_value, $this->_value);
        } else {
            $value = (isset($this->_value['range-date'])) ? $this->_value['range-date'] : array('', '');
        }

        $html[] = '<label for="' . $this->_getId('1') . '">' . JText::_('JBZOO_FROM') . '</label>';
        $html[] = $this->html->calendar(
            $this->_getName('0', '0'),
            $value[0],
            $this->_attrs,
            $this->_getId('1', true),
            $this->_getPickerParams()
        );

        $html[] = '<br />';

        $html[] = '<label for="' . $this->_getId('2') . '">' . JText::_('JBZOO_TO') . '</label>';
        $html[] = $this->html->calendar(
            $this->_getName('1', '0'),
            $value[1],
            $this->_attrs,
            $this->_getId('2', true),
            $this->_getPickerParams()
        );

        return implode("\n\r", $html);
    }

}
