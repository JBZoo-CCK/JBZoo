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
            if (($this->app->jbconfig->getList('config.custom')->get('del_empty_category_fromcsv') == 0)) {
                $parentCategory          = JBModelCategory::model()->createEmpty($this->_category->application_id);
                $this->_category->parent = $parentCategory->id;
            }
        }

        return $this->_category;
    }

}
