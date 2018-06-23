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
 * Class JBCSVItemUserFlickr
 */
class JBCSVItemUserFlickr extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $result = array();
        if (isset($this->_value['value'])) {
            $result[] = $this->_value['value'];
        }

        if (isset($this->_value['flickrid'])) {
            $result[] = $this->_value['flickrid'];
        }

        return implode(JBCSVItem::SEP_CELL, $result);
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        if (strpos($value, JBCSVItem::SEP_CELL) === false) {
            $tags     = '';
            $flickrid = $value;
        } else {
            list($tags, $flickrid) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'value'    => $this->_getString($tags),
            'flickrid' => $this->_getString($flickrid),
        ));

        return $this->_item;
    }

}
