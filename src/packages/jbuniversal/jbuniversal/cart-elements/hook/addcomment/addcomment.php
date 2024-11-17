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
