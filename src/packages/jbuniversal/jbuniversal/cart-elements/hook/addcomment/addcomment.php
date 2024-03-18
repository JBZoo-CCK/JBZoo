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
 * Class JBCartElementHookJBAdvert
 */
class JBCartElementHookAddcomment extends JBCartElementHook
{
    /**
     * @param array $params
     */
    public function notify($params = array())
    {
        $order = $this->getOrder();

        // $orderModel = JBModelOrder::model();
        // $orderz = $orderModel->getById($order->id);

        $object = new stdClass();
        $object->id = $order->id;
        $object->comment = $order->id.'-'.date('His',strtotime($order->created));
        // $object->comment = 'text';

        $result = JFactory::getDbo()->updateObject(ZOO_TABLE_JBZOO_ORDER, $object, 'id');

    }
}
