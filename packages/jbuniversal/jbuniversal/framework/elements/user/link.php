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
 * Class JBCSVItemUserLink
 */
class JBCSVItemUserLink extends JBCSVItem
{
    /**
     * Export data to CSV cell
     * @return string
     */
    public function toCSV()
    {
        $result = array();

        foreach ($this->_item->elements[$this->_identifier] as $key => $self) {

            $value = isset($self['value']) ? trim($self['value']) : null;
            $text  = isset($self['text']) ? trim($self['text']) : null;

            if ($text) {
                $result[$key] = $value . JBCSVItem::SEP_CELL . $text;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param      $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $data   = ($position == 1) ? array() : $data = $this->_element->data();

        if (strpos($value, JBCSVItem::SEP_CELL) === false) {
            $text = '';
            $link = $value;
        } else {
            list($link, $text) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $data[] = array(
            'text'  => $this->_getString($text),
            'value' => $this->_getString($link),
        );

        $this->_element->bindData($data);

        return $this->_item;
    }
}
