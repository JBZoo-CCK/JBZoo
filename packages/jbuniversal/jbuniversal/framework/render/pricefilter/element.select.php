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
 * Class JBPriceFilterElementSelect
 */
class JBPriceFilterElementSelect extends JBPriceFilterElement
{
    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $values = $this->_getValues();

        return $this->_html->select(
            $this->_createOptionsList($values),
            $this->_getName(true, $this->_isMultiple),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * Get DB values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $values = $this->_getDbValues();
        if (!empty($values)) {
            $values = $this->_sortByArray($values);
        }

        return $values;
    }
}
