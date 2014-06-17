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
 * Class JBCSVItemUserFlickr
 */
class JBCSVItemUserFlickr extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $result = array();
        if (isset($this->_value['value'])) {
            $result[] = $this->_value['value'];
        }

        if (isset($this->_value['flickrid'])) {
            $result[] = $this->_value['flickrid'];
        }

        return implode(JBCSVItem::SEP_CELL, $result);
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        if (strpos($value, JBCSVItem::SEP_CELL) === false) {
            $tags     = '';
            $flickrid = $value;
        } else {
            list($tags, $flickrid) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'value'    => $this->_getString($tags),
            'flickrid' => $this->_getString($flickrid),
        ));

        return $this->_item;
    }

}
