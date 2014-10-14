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
 * Class JBCartElementPaymentManual
 */
class JBCartElementPaymentManual extends JBCartElementPayment
{

    /**
     * @return null|string
     */
    public function getRedirectUrl()
    {
        if ($url = JString::trim($this->config->get('redirect_url'))) {
            return $url;
        }

        return null;
    }

    /**
     * @return int
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('order_id');
    }

}
