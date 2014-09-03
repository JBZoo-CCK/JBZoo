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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingPickup extends JBCartElementShipping
{

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        return $sum;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        $shipping = $this->config->get('cost', 0);
        $adresses = $this->getAdrreses();

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'shipping' => $shipping,
                'adresses' => $adresses
            ));
        }

        return false;
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return 500;
    }

    /**
     * @return mixed|string
     */
    public function getAdrreses()
    {
        $adresses = $this->config->get('adresses', array());

        $adress   = explode("\n", $adresses);
        $adresses = implode('</br>', $adress);

        return $adresses;
    }


}
