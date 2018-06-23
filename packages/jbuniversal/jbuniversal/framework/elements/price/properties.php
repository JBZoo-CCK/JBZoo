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

require_once __DIR__ . '/price.php';

/**
 * Class JBCSVItemPriceProperties
 */
class JBCSVItemPriceProperties extends JBCSVItemPrice
{
    /**
     * @return mixed|JBCartValue
     */
    public function toCSV()
    {
        $values = (array)$this->_param->data();
        $result = array();
        foreach ($values as $key => $value) {
            $value = JString::trim($value);

            if (!empty($value)) {
                $result[$key] = $value;
            }
        }

        return implode(JBCSVItem::SEP_CELL, $result);
    }

    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = null)
    {
        $value  = JString::trim((string)$value);
        $charts = explode(JBCSVItem::SEP_CELL, $value);

        return array(
            'height' => isset($charts[0]) ? $charts[0] : 0,
            'length' => isset($charts[1]) ? $charts[1] : 0,
            'width'  => isset($charts[2]) ? $charts[2] : 0
        );
    }
}