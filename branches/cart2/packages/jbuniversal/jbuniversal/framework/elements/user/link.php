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
        foreach ($this->_item->elements[$this->_identifier] as $key => $self) {
            $this->_item->elements[$this->_identifier][$key] = isset($self['value']) ? array('value' => rtrim($self['value'], '/')) : null;
        }
        return parent::toCSV();
    }
}
