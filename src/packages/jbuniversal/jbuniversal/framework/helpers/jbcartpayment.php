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
