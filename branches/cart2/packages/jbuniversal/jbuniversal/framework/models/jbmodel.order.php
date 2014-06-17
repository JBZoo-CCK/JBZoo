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
 * Class JBModelOrder
 */
class JBModelOrder extends JBModel
{
    /**
     * Create and return self instance
     * @return JBModelOrder
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Get order by itemid
     * @param $orderId
     */
    public function getById($orderId)
    {
        $orderId = (int)$orderId;
        $order   = $this->app->table->item->get($orderId);

        return $order;
    }

    /**
     * Get JBprice info from item
     * @param Item $item
     * @return null|elementJBBasketItems
     */
    public function getDetails(Item $item)
    {
        $elements = $item->getElements();

        foreach ($elements as $element) {

            if (JString::strtolower(get_class($element)) == 'elementjbbasketitems') {

                return $element;
            }
        }

        return null;
    }

}
