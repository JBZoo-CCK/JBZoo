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
 * Class JBCSVCategoryTitle
 */
class JBCSVCategoryTitle extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return $this->_category->params->get('content.category_title');
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function fromCSV($value)
    {
        $this->_category->params->set('content.category_title', $this->_getString($value));

        return $this->_category;
    }

}
