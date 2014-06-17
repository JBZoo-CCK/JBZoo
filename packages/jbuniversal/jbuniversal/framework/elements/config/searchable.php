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
 * Class JBCSVItemConfigSearchable
 */
class JBCSVItemConfigSearchable extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return $this->_item->searchable;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        $this->_item->searchable = $this->_getBool($value);

        return $this->_item;
    }

}
