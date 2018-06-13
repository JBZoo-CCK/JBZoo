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
 * Class JBCSVItemUserCountry
 */
class JBCSVItemUserCountry extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        if (isset($this->_value['country'])) {

            if (is_array($this->_value['country'])) {
                return implode(JBCSVItem::SEP_CELL, $this->_value['country']);
            } else {
                return $this->_value['country'];
            }

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
        $options = $this->_getArray($value, JBCSVItem::SEP_CELL);

        foreach ($options as $key => $option) {
            $options[$key] = JString::strtoupper($option);
        }

        $this->_element->bindData(array('country' => $options));

        return $this->_item;
    }

}
