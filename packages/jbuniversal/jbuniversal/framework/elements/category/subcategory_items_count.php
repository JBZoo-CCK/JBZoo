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
 * Class JBCSVCategorySubcategory_Items_count
 */
class JBCSVCategorySubcategory_items_count extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return (int)$this->_category->params->get('template.subcategory_items_count');
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function fromCSV($value)
    {
        $this->_category->params->set('template.subcategory_items_count', (int)$value);

        return $this->_category;
    }

}