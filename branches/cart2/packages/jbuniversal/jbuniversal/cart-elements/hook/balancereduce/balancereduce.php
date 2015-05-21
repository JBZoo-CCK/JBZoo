<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementHookBalanceReduce
 */
class JBCartElementHookBalanceReduce extends JBCartElementHook
{

    /**
     * @param array $params
     */
    public function notify($params = array())
    {
        $items = (array)$this->getOrder()->getItems();
        if (empty($items)) {
            return;
        }

        foreach ($items as $orderData) {

            /** @type Item $item */
            $item = $this->app->table->item->get($orderData->get('item_id'));
            if (!$item) {
                continue;
            }

            /**@type ElementJBPrice $element */
            $element = $item->getElement($orderData->get('element_id'));
            if (!$element || !is_a($element, 'ElementJBPrice')) {
                continue;
            }

            // Create variant object
            $element->setTemplate($orderData->get('template'));
            $varList = $element->getList($orderData->get('variations'), array('template' => $orderData->get('template')));
            $variant = $varList->current();

            /** @type JBCartElementPriceBalance $balance */
            $balance = $variant->get('_balance');
            if (!$balance) {
                continue;
            }

            $quantity = $this->app->jbvars->number($orderData->get('quantity'));
            if ($balance->reduce($quantity)) {
                $element->bindVariant($variant);
                $this->app->table->item->save($item);
            }
        }

    }
}
