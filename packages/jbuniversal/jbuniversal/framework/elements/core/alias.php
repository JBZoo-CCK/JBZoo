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
 * Class JBCSVItemCoreAlias
 */
class JBCSVItemCoreAlias extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        return $this->_item->alias;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {

        if ($alias = $this->_getAlias($value)) {
            $this->_item->alias = $this->app->alias->item->getUniqueAlias($this->_item->id, $alias);
        }

        return $this->_item;
    }

}
