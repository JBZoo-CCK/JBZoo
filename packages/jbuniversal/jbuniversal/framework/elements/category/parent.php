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
 * Class JBCSVCategoryParent
 */
class JBCSVCategoryParent extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        if ($this->_category->parent) {

            if ($parent = JBModelCategory::model()->getParent($this->_category->parent)) {
                return $parent->alias;
            }

        }

        return '';
    }

    /**
     * @param $value
     * @return int|null
     */
    public function fromCSV($value)
    {
        if ($value && $category = JBModelCategory::model()->getByAlias($value)) {
            $this->_category->parent = $category->id;
        } else {
            $parentCategory          = JBModelCategory::model()->createEmpty($this->_category->application_id);
            $this->_category->parent = $parentCategory->id;
        }

        return $this->_category;
    }

}
