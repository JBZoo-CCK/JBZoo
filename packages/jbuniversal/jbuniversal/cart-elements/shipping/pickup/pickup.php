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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingPickup extends JBCartElementShipping
{

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
        $address = $this->config->get('address');

        if ($address && $layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'  => $params,
                'address' => $address,
            ));
        }

        return null;
    }

}
