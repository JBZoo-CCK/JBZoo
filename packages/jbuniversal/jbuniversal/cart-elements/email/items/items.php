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
 * Class JBCartElementEmailItems
 */
class JBCartElementEmailItems extends JBCartElementEmail
{
    const DEFAULT_TITLE = 'JBZOO_ORDER_ITEMS_TITLE';

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
        if (!empty($order->id) && $order->getItems()) {
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
        $order = $this->getOrder();
        $items = (array)$order->getItems(false);
        
        if ($layout = $this->getLayout('order.php')) {
            return self::renderLayout($layout, array(
                    'title'    => $this->getTitle(self::DEFAULT_TITLE),
                    'items'    => $items,
                    'order'    => $order,
                    'currency' => $order->getCurrency()
                )
            );
        }

        return false;
    }
}
