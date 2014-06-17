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
 * Class JBCSVItemUserGallery
 */
class JBCSVItemUserGallery extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $result = array();
        if (isset($this->_value['value'])) {
            $result[] = $this->_value['value'];

            if (isset($this->_value['title'])) {
                $result[] = $this->_value['title'];
            }
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
            $title = '';
            $path  = $value;
        } else {
            list($path, $title) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'value' => $this->_getString(rtrim($path, '/\\')),
            'title' => $this->_getString($title),
        ));

        return $this->_item;
    }

}
