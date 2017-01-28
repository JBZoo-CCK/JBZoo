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
 * Class JBCSVItemUserJoomlaModule
 */
class JBCSVItemUserJoomlaModule extends JBCSVItem
{
    protected $_autoType = 'int';

    /**
     * Export data to CSV cell
     * @return string
     */
    public function toCSV()
    {

        if ($this->_element && isset($this->_item->elements[$this->_identifier]['value'])) {
            return (int)$this->_item->elements[$this->_identifier]['value'];
        }

        return 'none';
    }

}
