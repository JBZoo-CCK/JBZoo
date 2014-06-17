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
 * Class JBCSVItemConfigCategory_primary
 */
class JBCSVItemConfigCategory_primary extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        if ($category = $this->_item->getPrimaryCategory()) {
            return $category->name . JBCSVItem::SEP_CELL . $category->alias;
        }

        return null;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {

        $value = $this->_getString($value);

        if ($value) {
            $application      = $this->_item->getApplication();
            $appCategories    = $application->getCategories();
            $appCategoryAlias = array_map(create_function('$cat', 'return $cat->alias;'), $appCategories);
            $appCategoryNames = array_map(create_function('$cat', 'return $cat->name;'), $appCategories);

            $primaryCategoryId = null;

            $alias = null;
            $name  = $value;
            if (strpos($value, JBCSVItem::SEP_CELL)) {
                list($name, $alias) = explode(JBCSVItem::SEP_CELL, $value);
            }

            if ($name == '__ROOT__') {
                $primaryCategoryId = 0;

            } else {
                if ($alias && $id = array_search($alias, $appCategoryAlias)) {
                    $primaryCategoryId = $id;

                } else if ($name && $id = array_search($name, $appCategoryNames)) {
                    $primaryCategoryId = $id;
                }
            }

            if (!is_null($primaryCategoryId)) {

                $relatedCategories = JBModelItem::model()->getRelatedCategories($this->_item->id);
                if (!in_array($primaryCategoryId, $relatedCategories)) {
                    $relatedCategories[] = $primaryCategoryId;
                    $relatedCategories   = array_unique($relatedCategories);

                    $this->app->category->saveCategoryItemRelations($this->_item, $relatedCategories);
                }

                $this->_item->getParams()->set('config.primary_category', $primaryCategoryId);
            }

        }

        return $this->_item;
    }

}
