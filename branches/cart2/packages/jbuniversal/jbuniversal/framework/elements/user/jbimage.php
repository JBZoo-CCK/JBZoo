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
 * Class JBCSVItemUserJBImage
 */
class JBCSVItemUserJBImage extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();

        if (!empty($this->_value)) {
            foreach ($this->_value as $self) {

                $resTmp = isset($self['file']) ? $self['file'] : '';

                if (isset($self['title']) && $self['title']) {
                    $resTmp .= JBCSVItem::SEP_CELL . $self['title'];
                }

                $result[] = $resTmp;
            }
        }

        if ((int)$this->_exportParams->get('merge_repeatable')) {
            return implode(JBCSVItem::SEP_ROWS, $result);
        } else {
            return $result;
        }
    }

    /**
     * @param $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        $data = ($position == 1) ? array() : $this->_element->data();

        if (strpos($value, JBCSVItem::SEP_ROWS)) {
            foreach (explode(JBCSVItem::SEP_ROWS, $value) as $val) {
                if (strpos($val, JBCSVItem::SEP_CELL) === false) {
                    $title = '';
                    $file  = $val;
                } else {
                    list($file, $title) = explode(JBCSVItem::SEP_CELL, $val);
                }

                $values[] = array(
                    'title' => $this->_getString($title),
                    'file'  => $this->_getString($file),
                );
            }
            $data = $values;
        } else {

            if (strpos($value, JBCSVItem::SEP_CELL) === false) {
                $title = '';
                $file  = $value;
            } else {
                list($file, $title) = explode(JBCSVItem::SEP_CELL, $value);
            }

            if (!empty($file)) {
                $values = array(
                    'title' => $this->_getString($title),
                    'file'  => $this->_getString($file),
                );

                $data[] = $values;
            }
        }
        $this->_element->bindData($data);

        return $this->_item;
    }

}
