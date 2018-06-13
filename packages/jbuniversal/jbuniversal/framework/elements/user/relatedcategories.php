<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
                if ($category) {
                    $result[] = $category->alias;
                }
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
