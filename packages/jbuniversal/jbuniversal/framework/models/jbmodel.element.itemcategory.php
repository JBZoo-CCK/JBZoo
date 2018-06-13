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
 * Class JBModelElementItemcategory
 */
class JBModelElementItemcategory extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        $select->leftJoin(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem ON tCategoryItem.item_id = tItem.id');
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Prepare and validate value
     * @param array|string $value
     * @param bool $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $newValue = array();

        foreach ($value as $categoryId) {
            $categoryId = (int)$categoryId;
            if ($categoryId) {
                $newValue[] = $categoryId;
            }
        }

        $value = $this->_attachSubcategories($newValue);

        return $value;
    }

    /**
     * Add subcategories ids for selected categories
     * TODO Use JBModelCategory
     * @param $parentCategories
     * @return array
     */
    protected function _attachSubcategories($parentCategories)
    {
        if (empty($parentCategories)) {
            $parentCategories = array(0);
        }

        $select = $this->_getSelect()
            ->select('tCategory.id')
            ->from(ZOO_TABLE_CATEGORY . ' AS tCategory')
            ->where('tCategory.parent IN (' . implode(', ', $parentCategories) . ')');

        $subcategories   = $this->fetchAll($select);
        $subcategoriesId = $this->_groupBy($subcategories, 'id');

        $result = array_merge($subcategoriesId, $parentCategories);

        return $result;
    }

    /**
     * @param $value
     * @param $elementId
     * @return array
     */
    protected function _getWhere($value, $elementId)
    {
        $values = $this->_prepareValue($value);

        if (!empty($values)) {
            return array('tCategoryItem.category_id IN (' . implode(',', $values) . ')');
        }

        return array();
    }


}