<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBMigrateOrderHelper
 */
class JBMigrateOrderHelper extends AppHelper
{
    /**
     * @var AppData
     */
    protected $_params;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * @var array
     */
    protected $_statusList;

    /**
     * Old cart configs
     * @var AppData
     */
    protected $_cartConfig;

    /**
     * @var JBMigrateHelper
     */
    protected $_migrate;

    /**
     * Convert order to new format
     * @param $page
     * @return bool
     */
    public function convertItems($page)
    {
        $this->_migrate = $this->app->jbmigrate;
        $params         = $this->_migrate->getParams();

        $this->_fields     = $this->_migrate->getOrderFields();
        $this->_statusList = $this->_getStatusList();

        $realStep = $page - $params->find('steps.system_steps');
        $size     = $params->find('steps.step');

        if ($realStep <= 0) {
            return -1;
        }

        $orders = JBModelItem::model()->getList($params->get('app'), null, $params->get('type'), array(
            'limit'     => array(($realStep - 1) * $size, $size),
            'published' => 0,
            'state'     => -1,
            'order'     => 'id',
        ));

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                if ($newOrder = $this->_convertOrder($order)) {
                    JBModelOrder::model()->save($newOrder, true);
                }
            }
            return $page + 1;
        }

        return false;
    }

    /**
     * @param Item $order
     * @return JBCartOrder
     */
    protected function _convertOrder(Item $order)
    {
        $newOrder = new JBCartOrder();

        $cartElement = current($order->getElementsByType('jbbasketitems'));
        if (!$cartElement) {
            return null;
        }

        $orderItems = $cartElement->getOrderItems();
        $data       = (array)$cartElement->data();

        $newOrder->id         = -1;
        $newOrder->modified   = $order->modified;
        $newOrder->created    = $order->created;
        $newOrder->created_by = $order->created_by;

        $newOrder->params = new ParameterData();
        $newOrder->params->set('config', array(
            'default_currency'   => $this->_migrate->getCurrency(),
            'migration_currency' => $this->_migrate->getCurrency(),
        ));

        $paymentElement = $this->_createPayment($data);
        $paymentElement->setOrder($newOrder);
        $newOrder->setPaymentElement($paymentElement);

        $fieldsElement = $this->_createOrderElement($order);
        $fieldsElement->setOrder($newOrder);
        $newOrder->addOrderElement($fieldsElement);

        $newOrder->setStatusList($this->_statusList);

        $newList  = array();
        $totalSum = $newOrder->val(0);
        foreach ($orderItems as $orderItem) {
            $key       = md5(serialize($orderItem));
            $orderItem = $this->app->data->create($orderItem);

            $totalPrice = $newOrder->val($orderItem->get('price') . ' ' . $orderItem->get('currency'));

            $newList[$key] = array(
                'key'        => $key,
                'item_id'    => $orderItem->get('itemId'),
                'item_name'  => $orderItem->get('name'),
                'element_id' => '',
                'total'      => $totalPrice->data(true),
                'quantity'   => $orderItem->get('quantity'),
                'template'   => array(),
                'values'     => (array)$orderItem->get('priceParams'),
                'elements'   => array(
                    '_value'       => $totalPrice->data(true),
                    '_sku'         => $orderItem->get('sku'),
                    '_description' => $orderItem->get('priceDesc'),
                ),
                'params'     => array(
                    'quantity' => array(
                        'min'      => 1,
                        'max'      => 1000,
                        'step'     => 1,
                        'default'  => 1,
                        'decimals' => 0,
                    ),
                ),
                'modifiers'  => array(),
                'variations' => array(),
                'variant'    => 0,
            );

            $totalSum->add($totalPrice);
        }

        $newOrder->setItemsData(json_encode($newList));
        $newOrder->total = $totalSum->val();

        if (isset($data['order_info'])) {

            if (isset($data['order_info']['status'])) {
                $newOrder->setStatus($data['order_info']['status'], JBCart::STATUS_ORDER);
                $newOrder->setStatus($data['order_info']['status'], JBCart::STATUS_PAYMENT);
            }

            if (isset($data['order_info']['description'])) {
                $newOrder->comment = $data['order_info']['description'];
            }
        }

        return $newOrder;
    }

    /**
     * @param array $orderInfo
     * @return JBCartElementPaymentMigration
     */
    protected function _createPayment($orderInfo)
    {
        /** @var JBCartElementPaymentMigration $paymentElement */
        $paymentElement = $this->app->jbcartelement->create('migration', JBCart::ELEMENT_TYPE_PAYMENT, array(
            'name'       => JText::_('JBZOO_MIGRATE_PAYMENT_NAME'),
            'identifier' => $this->app->utility->generateUUID(),
        ));

        $data = isset($orderInfo['order_info']) ? $orderInfo['order_info'] : array();
        $paymentElement->bindData($data);

        return $paymentElement;
    }

    /**
     * @param Item $order
     * @return JBCartElementOrderMigration
     */
    protected function _createOrderElement(Item $order)
    {
        /** @var JBCartElementOrderMigration $orderElement */
        $orderElement = $this->app->jbcartelement->create('migration', JBCart::ELEMENT_TYPE_ORDER, array(
            'name'       => JText::_('JBZOO_MIGRATE_FIELDS_NAME'),
            'identifier' => $this->app->utility->generateUUID(),
        ));

        $result = array();
        foreach ($this->_fields as $elemId => $elemParams) {
            if ($element = $order->getElement($elemId)) {

                $name = $element->config->name;
                if ($elemParams['_value_map']) {
                    $data    = $this->app->data->create($element->data());
                    $content = $data->find($elemParams['_value_map']);
                } else {
                    $content = $element->getSearchData();
                }

                $result[$name] = $content;
            }
        }

        $orderElement->bindData(array('content' => $result));

        return $orderElement;
    }

    /**
     * @return array
     */
    protected function _getStatusList()
    {
        $list   = array('nodata', 'cancel', 'nopaid', 'progress', 'paid');
        $groups = array(JBCart::STATUS_ORDER, JBCart::STATUS_PAYMENT);

        $result = array();
        foreach ($groups as $group) {
            foreach ($list as $code) {

                /** @var JBCartElementStatusCustom $status */
                $status = $this->app->jbcartelement->create('custom', JBCart::ELEMENT_TYPE_STATUS, array(
                    'code'       => $code,
                    'name'       => JText::_('JBZOO_PAYMENT_STATUS_' . $code),
                    'identifier' => $code,
                ));

                $result[$group][$status->identifier] = $status;
            }
        }

        $result[JBCart::STATUS_SHIPPING] = array();

        return $result;
    }

}