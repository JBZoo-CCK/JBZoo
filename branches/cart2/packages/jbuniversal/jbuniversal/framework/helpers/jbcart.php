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
 * Class JBCartHelper
 */
class JBCartHelper extends AppHelper
{

    protected $_namespace = 'jbzoo';
    protected $_namespaceHelper = 'jbcart';

    /**
     * Get all items from session
     * @return mixed
     */
    public function getAllItems()
    {
        // JBCart::getInstance()
    }

    /**
     * Add item to compare
     * @param Item $item
     * @param array $params
     * @param bool $isAdvance
     */
    public function addItem(Item $item, array $params = array(), $isAdvance = false)
    {
        // JBCart::getInstance()
    }

    /**
     * Remove item from compare
     * @param Item $item
     * @param bool $isAdvance
     * @param string $hash
     * @return bool
     */
    public function removeItem(Item $item, $isAdvance = false, $hash = '')
    {
        // JBCart::getInstance()
    }

    /**
     * Check is item is compared
     * @param Item $item
     * @param bool $isAdvance
     * @param string $hash
     * @return bool
     */
    public function isExists(Item $item, $isAdvance = false, $hash = '')
    {
        // JBCart::getInstance()
    }

    /**
     * Change quantity
     * @param Item $item
     * @param int $value
     * @param string $hash
     * @param bool $isAdvance
     */
    public function changeQuantity($item, $value, $hash = '', $isAdvance = false)
    {
        // JBCart::getInstance()
    }

    /**
     * Remove all items from session
     */
    public function removeItems()
    {
        // JBCart::getInstance()
    }

    /**
     * Get array id list
     * @param bool $isAdvance
     * @return array
     */
    public function getItemIds($isAdvance = false)
    {
        $items  = $this->getAllItems();
        $result = array();

        foreach ($items as $itemRow) {
            $result[] = $itemRow['itemId'];
        }

        return $result;
    }

    /**
     * Get array id list
     * @param ParameterData $appParams
     * @param bool $isAdvance
     * @return array
     */
    public function recount($appParams, $isAdvance = false)
    {
        $itemsPrice = array();
        $count      = 0;
        $total      = 0;

        $items           = $this->getAllItems();
        $currencyConvert = $appParams->get('global.jbzoo_cart_config.currency', 'EUR');

        foreach ($items as $hash => $item) {

            $item['price'] = $this->app->jbmoney->convert($item['currency'], $currencyConvert, $item['price']);

            $itemsPrice[$hash] = $item['price'] * $item['quantity'];

            $count += $item['quantity'];
            $total += $itemsPrice[$hash];

            $itemsPrice[$hash] = $this->app->jbmoney->toFormat($itemsPrice[$hash], $currencyConvert);
        }

        return array(
            'items' => $itemsPrice,
            'count' => $count,
            'total' => $this->app->jbmoney->toFormat($total, $currencyConvert),
        );
    }

    /**
     * Get basket items
     * @param bool $isAdvance
     * @return mixed
     */
    public function getBasketItems($isAdvance = false)
    {
        $items = $this->getAllItems();

        foreach ($items as $hash => $itemRow) {
            if (is_numeric($itemRow['itemId']) && $item = $this->app->table->item->get($itemRow['itemId'])) {
                $items[$hash]['item'] = $item;
            } else {
                unset($items[$hash]);
            }
        }

        return $items;
    }

    /**
     * Get quantity by hash
     * @param $hash
     * @return int
     */
    public function getQuantityByHash($hash)
    {
        $items = $this->getAllItems();

        foreach ($items as $item) {
            if ($item['hash'] == $hash) {
                return $item['quantity'];
            }
        }

        return 0;
    }


}
