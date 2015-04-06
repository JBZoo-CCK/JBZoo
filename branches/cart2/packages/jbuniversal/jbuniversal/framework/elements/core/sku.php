<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
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
