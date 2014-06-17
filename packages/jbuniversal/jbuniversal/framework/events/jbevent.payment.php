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
 * Class JBEventPayment
 */
class JBEventPayment extends JBEvent
{

    /**
     * After order saved event
     * @param AppEvent $event
     */
    public static function callback($event)
    {
        $app    = self::app();
        $params = $event->getParameters();

        $appParams = $params['appParams'];
        $item      = $params['item'];
        $subject   = JText::_('JBZOO_CART_ORDER_PAYMENT_COMPLITE');

        if ((int)$appParams->get('notificaction-payment', 1)) {

            // to admin
            $adminEmail = $params['appParams']->get('global.jbzoo_cart_config.admin-email');
            if ($adminEmail) {
                $adminLayout = $appParams->get('global.jbzoo_cart_config.email-admin-layout-payment');
                $app->jbemail->sendByItem($adminEmail, $subject, $item, $adminLayout);
            }

            // to user email from profile
            $userEmail = JFactory::getUser($item->created_by)->email;
            if ($userEmail) {
                $userLayout = $appParams->get('global.jbzoo_cart_config.email-user-layout-payment');
                $app->jbemail->sendByItem($userEmail, $subject, $item, $userLayout);
            }

            // to email from order field
            $emailElement = $appParams->get('global.jbzoo_cart_config.element-useremail');
            if ($element = $item->getElement($emailElement)) {
                $data = $element->data();
                if (isset($data[0]['value']) && !empty($data[0]['value'])) {
                    $userLayout = $appParams->get('global.jbzoo_cart_config.email-user-layout-payment');
                    $app->jbemail->sendByItem($data[0]['value'], $subject, $item, $userLayout);
                }
            }
        }

    }

}