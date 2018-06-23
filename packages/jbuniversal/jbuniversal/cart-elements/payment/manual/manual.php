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
