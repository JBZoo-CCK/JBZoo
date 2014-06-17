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
 * Class JBCSVCategoryId
 */
class JBCSVCategoryId extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return $this->_category->id;
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function fromCSV($value)
    {
        return $this->_category;
    }

}
