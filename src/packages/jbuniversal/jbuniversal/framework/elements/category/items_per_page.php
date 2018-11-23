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
 * Class JBCSVCategoryItems_per_page
 */
class JBCSVCategoryItems_per_page extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return (int)$this->_category->params->get('config.items_per_page');
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function fromCSV($value)
    {
        $this->_category->params->set('config.items_per_page', (int)$value);

        return $this->_category;
    }

}