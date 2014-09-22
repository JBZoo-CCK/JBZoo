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
        $adresses = $this->getAddress();

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
     * @return mixed|string
     */
    public function getAddress()
    {
        $adresses = $this->config->get('adresses', array());

        $adress   = explode("\n", $adresses);
        $adresses = implode('</br>', $adress);

        return $adresses;
    }

}
