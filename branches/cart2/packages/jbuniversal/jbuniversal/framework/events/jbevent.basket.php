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
 * Class JBEventBasket
 */
class JBEventBasket extends JBEvent
{

    /**
     * On after order saved
     * @param AppEvent $event
     */
    public static function saved($event)
    {
        $app    = self::app();
        $params = $event->getParameters();

        $appParams = $params['appParams'];
        $item      = $params['item'];
        $subject   = JText::_('JBZOO_CART_NEW_ORDER_CREATE');

        if ((int)$appParams->get('notificaction-create', 1)) {

            // to admin
            $adminEmail = $appParams->get('global.jbzoo_cart_config.admin-email');
            if ($adminEmail) {
                $adminLayout = $appParams->get('global.jbzoo_cart_config.email-admin-layout');
                $app->jbemail->sendByItem($adminEmail, $subject, $item, $adminLayout);
            }

            // to user email from profile
            $userEmail = JFactory::getUser()->email;
            if ($userEmail) {
                $userLayout = $appParams->get('global.jbzoo_cart_config.email-user-layout');
                $app->jbemail->sendByItem($userEmail, $subject, $item, $userLayout);
            }

            // to email from order field
            $emailElement = $appParams->get('global.jbzoo_cart_config.element-useremail');
            if ($element = $item->getElement($emailElement)) {
                $data = $element->data();
                if (isset($data[0]['value']) && !empty($data[0]['value'])) {
                    $userLayout = $appParams->get('global.jbzoo_cart_config.email-user-layout');
                    $app->jbemail->sendByItem($data[0]['value'], $subject, $item, $userLayout);
                }
            }

        }


        if ((int)$appParams->get('global.jbzoo_cart_config.is_advance')) {

            // reduce the balance in the item
            $basketElements = $item->getElementsByType('jbbasketitems');
            if (!empty($basketElements)) {
                reset($basketElements);
                $jbbasket = current($basketElements);
                $items    = $jbbasket->getOrderItems();

                foreach ($items as $item) {
                    $good = $app->table->item->get($item['itemId']);
                    if (!$good) {
                        continue;
                    }

                    $jbPrices = $good->getElementsByType('jbpriceadvance');
                    foreach ($jbPrices as $jbPrice) {
                        if (isset($item['hash'])) {
                            $jbPrice->balanceReduce($item['hash'], $item['quantity']);
                        }
                    }
                }
            }
        }
    }

    /**
     * On before order save
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
    }

}