<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmailStatus
 */
class JBCartElementEmailStatus extends JBCartElementEmail
{

    /**
     * Check elements value.
     * Output element or no.
     *
     * @param  array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $order = $this->getOrder();

        if ($order->id && $order->getStatus()) {
            return true;
        }

        return false;
    }

    /**
     * Render elements data
     *
     * @param  array $params
     *
     * @return null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout('order.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'order'  => $this->getOrder()
            ));
        }

        return false;
    }

}
