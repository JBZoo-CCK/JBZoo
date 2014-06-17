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
 * Class JBCSVItemConfigFrontpage
 */
class JBCSVItemConfigFrontpage extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $categories = $this->_item->getRelatedCategoryIds();

        return (int)in_array('0', $categories, true);
    }

    /**
     * @param $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        $relatedCategories = JBModelItem::model()->getRelatedCategories($this->_item->id);

        if ($this->_getBool($value)) {

            $relatedCategories[] = 0;
            $this->app->category->saveCategoryItemRelations($this->_item, $relatedCategories);

        } else {

            $index = array_search(0, $relatedCategories);
            if (false !== $index) {
                unset($relatedCategories[$index]);
                $this->app->category->saveCategoryItemRelations($this->_item, $relatedCategories);
            }

        }

        return $this->_item;
    }

}
