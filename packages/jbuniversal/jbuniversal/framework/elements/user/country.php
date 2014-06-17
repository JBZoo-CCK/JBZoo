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
 * Class JBCSVItemUserCountry
 */
class JBCSVItemUserCountry extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        if (isset($this->_value['country'])) {

            if (is_array($this->_value['country'])) {
                return implode(JBCSVItem::SEP_CELL, $this->_value['country']);
            } else {
                return $this->_value['country'];
            }

        }

        return null;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $options = $this->_getArray($value, JBCSVItem::SEP_CELL);

        foreach ($options as $key => $option) {
            $options[$key] = JString::strtoupper($option);
        }

        $this->_element->bindData(array('country' => $options));

        return $this->_item;
    }

}
