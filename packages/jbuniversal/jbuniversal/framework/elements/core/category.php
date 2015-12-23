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
 * Class JBCSVItemCoreCategory
 */
class JBCSVItemCoreCategory extends JBCSVItem
{
    /**
     * @return array|string
     */
    public function toCSV()
    {
        $result = array();

        $categories = JBModelItem::model()->getItemCategories($this->_item->id);

        foreach ($categories as $category) {
            $name = $category->name . JBCSVItem::SEP_CELL . $category->alias;

            while ($category && $category = JBModelCategory::model()->getParent($category->parent)) {
                $name = $category->name . JBCSVItem::SEP_CELL . $category->alias . JBCSVItem::SEP_ROWS . $name;
            }

            $result[] = $name;
        }

        if (!empty($result)) {
            natsort($result);
        }

        return $result;
    }

    /**
     * @param      $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        static $memCache = array();

        $categoryTable = $this->app->table->category;
        $application   = $this->_item->getApplication();

        if (!isset($memCache[$application->id])) {
            $appCategories    = $this->app->table->category->getAll($application->id);
            $appCategoryAlias = array_map(create_function('$cat', 'return $cat->alias;'), $appCategories);
            $appCategoryNames = array_map(create_function('$cat', 'return $cat->name;'), $appCategories);

            $memCache[$application->id] = array(
                'appCategories'    => $appCategories,
                'appCategoryAlias' => $appCategoryAlias,
                'appCategoryNames' => $appCategoryNames,
            );
        } else {
            $appCategories    = &$memCache[$application->id]['appCategories'];
            $appCategoryAlias = &$memCache[$application->id]['appCategoryAlias'];
            $appCategoryNames = &$memCache[$application->id]['appCategoryNames'];
        }

        $itemCategories = $this->_getArray($value, 'simple');

        if ($position == 1) {
            $relatedCategories = array();
        } else {
            $relatedCategories = JBModelItem::model()->getRelatedCategories($this->_item->id);
        }

        try {
            // store categories
            foreach ($itemCategories as $categoryName) {
                $names      = array_filter(explode(JBCSVItem::SEP_ROWS, $categoryName));
                $previousId = 0;
                $found      = true;

                for ($i = 0; $i < count($names); $i++) {

                    list($name, $alias) = array_pad(explode(JBCSVItem::SEP_CELL, $names[$i]), 2, false);

                    // did the alias change?
                    if ($alias && isset($aliasMatches[$alias])) {
                        $alias = $aliasMatches[$alias];
                    }

                    // try to find category through alias, if category is not found, try to match name
                    if (!($id = array_search($alias, $appCategoryAlias)) && !$alias) {
                        $id = array_search($name, $appCategoryNames);
                        foreach (array_keys($appCategoryNames, $name) as $key) {
                            if ($previousId && isset($appCategories[$key]) && $appCategories[$key]->parent == $previousId) {
                                $id = $key;
                            }
                        }
                    }

                    if (!$found || !$id) {

                        $found = false;

                        $category                 = $this->app->object->create('Category');
                        $category->application_id = $application->id;
                        $category->name           = JString::trim($name);
                        $category->parent         = $previousId;

                        // set a valid category alias
                        $categoryAlias   = $this->app->string->sluggify($alias ? $alias : $name);
                        $category->alias = $this->app->alias->category->getUniqueAlias(0, $categoryAlias);

                        try {
                            $categoryTable->save($category);
                            $appCategories[$category->id]    = $category;
                            $appCategoryNames[$category->id] = $category->name;
                            $appCategoryAlias[$category->id] = $aliasMatches[$alias] = $category->alias;
                            $id                              = $category->id;

                        } catch (CategoryTableException $e) {
                        }
                    }

                    if ($id && $i == count($names) - 1) {
                        $relatedCategories[] = $id;
                    } else {
                        $previousId = $id;
                    }
                }
            }

            // add category to item relations
            if (!empty($relatedCategories)) {

                $relatedCategories = array_unique($relatedCategories);
                $this->app->category->saveCategoryItemRelations($this->_item, $relatedCategories);

                // make first category found primary category
                if (!$this->_item->getPrimaryCategoryId()) {
                    $this->_item->getParams()->set('config.primary_category', $relatedCategories[0]);
                }
            }

        } catch (ItemTableException $e) {
        }

        return $this->_item;
    }

}
