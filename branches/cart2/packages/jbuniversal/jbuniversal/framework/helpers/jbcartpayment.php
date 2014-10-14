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
 * Class JBCartPaymentHelper
 */
class JBCartPaymentHelper extends AppHelper
{

    /**
     * Get request orderId
     * @return int
     */
    public function getInfoByRequest()
    {
        $payments = $this->app->jbcartposition->loadElements(JBCart::ELEMENT_TYPE_PAYMENT);

        $result = $this->app->data->create(array(
            'id'   => 0,
            'type' => 'undefined',
        ));

        foreach ($payments as $payment) {
            $orderId = (int)$payment->getRequestOrderId();
            if ($orderId > 0) {
                $result->set('id', $orderId);
                $result->set('type', $payment->getType());
                break;
            }
        }

        return $result;
    }

}
