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
 * Class JBCSVItemUserImage
 */
class JBCSVItemUserImage extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = '';

        if (isset($this->_value['file'])) {

            $result = $this->_value['file'];

            if (isset($this->_value['title']) && $this->_value['title']) {
                $result .= JBCSVItem::SEP_CELL . $this->_value['title'];
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        if (strpos($value, JBCSVItem::SEP_CELL) === false) {
            $title = '';
            $file  = $value;
        } else {
            list($file, $title) = explode(JBCSVItem::SEP_CELL, $value);
        }

        $this->_element->bindData(array(
            'file'  => $this->_getString($file),
            'title' => $this->_getString($title),
        ));

        return $this->_item;
    }

}
