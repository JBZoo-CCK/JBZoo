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
 * Class JBCSVItemUserImage
 */
class JBCSVItemUserImage extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = '';

        if (isset($this->_value['file'])) {

            $result = $this->_value['file'];

            if (isset($this->_value['title']) && $this->_value['title']) {
                $result .= JBCSVItem::SEP_CELL . $this->_value['title'];
            }
        }

        return $result;
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
            $file  = $value;
        } else {
            list($file, $title) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'file'  => $this->_getString($file),
            'title' => $this->_getString($title),
        ));

        return $this->_item;
    }

}
