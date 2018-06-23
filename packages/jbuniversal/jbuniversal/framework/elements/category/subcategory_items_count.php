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