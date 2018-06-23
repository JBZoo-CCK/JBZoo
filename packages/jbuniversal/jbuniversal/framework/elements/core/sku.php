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
 * Class JBCSVItemCoreSku
 */
class JBCSVItemCoreSku extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        $elements = $this->app->jbprice->getItemPrices($this->_item);
        if (count($elements) === 0) {
            return $this->_item->id;
        }
        $current = current($elements);

        $itemId  = $this->_item->id;
        $variant = $current->getVariant();
        if ($variant) {
            $itemId = $variant->getValue(true, '_sku', $this->_item->id);
            $variant->clear();
        }

        return $itemId;
    }

    /**
     * @param      $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        return $this->_item;
    }
}
