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
 * Class JBCSVItemUserJBSliderNivo
 */
class JBCSVItemUserJBSliderNivo extends JBCSVItem
{
    /**
     * @param $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        $value = JString::trim($value, '/\\');
        $value = JString::trim($value);
        $this->_element->bindData(array('value' => $value));

        return $this->_item;
    }

}
