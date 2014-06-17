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
 * Class JBCSVItemUserRelatedCategories
 */
class JBCSVItemUserRelatedCategories extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $result = array();
        if (isset($this->_value['category'])) {
            foreach ($this->_value['category'] as $categoryId) {
                $category = $this->app->table->category->get($categoryId);
                $result[] = $category->alias;
            }

            return implode(JBCSVItem::SEP_ROWS, $result);
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
        $categoriesAlias = $this->_getArray($value, JBCSVItem::SEP_ROWS, 'alias');

        $result = array();
        foreach ($categoriesAlias as $alias) {

            if ($category = JBModelCategory::model()->getByAlias($alias, $this->_item->application_id)) {
                $result[] = $category->id;
            }

            $result = array_unique($result);
        }

        $this->_element->bindData(array('category' => $result));

        return $this->_item;
    }

}
