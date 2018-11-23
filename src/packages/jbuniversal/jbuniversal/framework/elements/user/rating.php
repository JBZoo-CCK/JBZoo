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
 * Class JBCSVItemUserRating
 */
class JBCSVItemUserRating extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        if (isset($this->_value['value'])) {
            return implode(JBCSVItem::SEP_CELL, array(
                $this->_value['votes'],
                $this->_value['value']
            ));
        }

        return '';
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        if (strpos($value, JBCSVItem::SEP_CELL) === false) {
            $votes = 0;
            $rate  = $value;
            if ($rate > 0) {
                $votes = 1;
            }

        } else {
            list($votes, $rate) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'value' => $this->_getString($rate),
            'votes' => $this->_getString($votes),
        ));

        return $this->_item;
    }

}
