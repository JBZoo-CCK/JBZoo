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
 * Class JBCSVItemConfigPublish_Up
 */
class JBCSVItemConfigPublish_Up extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        return $this->_item->publish_up;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        if ($time = $this->_getDate($value)) {
            $this->_item->publish_up = $time;
        }

        return $this->_item;
    }

}
