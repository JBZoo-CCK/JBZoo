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
 * Class JBCSVItemCoreAuthor
 */
class JBCSVItemCoreAuthor extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        return $this->_item->getAuthor();
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        $value = $this->_getString($value);

        if ($id = $this->_getInt($value)) {
            $user = $this->app->user->get($id);

            if ($user && $user->id) {
                $this->_item->created_by = $user->id;
            }

        } else {
            $this->_item->created_by_alias = $value;
        }

        return $this->_item;
    }

}
