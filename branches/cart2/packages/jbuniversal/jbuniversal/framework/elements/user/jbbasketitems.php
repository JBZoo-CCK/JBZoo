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
 * Class JBCSVItemUserJBBasketItems
 */
class JBCSVItemUserJBBasketItems extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        if (isset($this->_value['is_advance'])) {
            $items     = isset($this->_value['items']) ? $this->_value['items'] : array();
            $orderInfo = isset($this->_value['order_info']) ? $this->_value['order_info'] : array();
        } else {
            $items = $this->_value;
            reset($items);
            $firstKey = key($items);

            $orderInfo = isset($items[$firstKey]['order_info']) ? $items[$firstKey]['order_info'] : array();
        }

        $orderItems = array();
        foreach ($items as $item) {
            $orderItems[] = $this->_packToLine(array(
                'sku/itemid' => $item['sku'] . ' / ' . $item['itemId'],
                'price'      => $item['price'] . ' ' . $item['currency'],
                'quantity'   => $item['quantity']
            ));
        }

        return array($this->_packToLine($orderInfo), implode(self::SEP_ROWS, $orderItems));
    }

    /**
     * TODO import from CSV. Comming soon =)
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        return $this->_item;
    }

}
