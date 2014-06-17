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


// register ElementRepeatable class
App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');

/**
 * Class ElementJBBasketItems
 */
class ElementJBBasketItems extends Element implements iSubmittable
{
    public $renderMode = null;

    const ORDER_STATUS_NODATA   = 'nodata';
    const ORDER_STATUS_PAID     = 'paid';
    const ORDER_STATUS_NOPAID   = 'nopaid';
    const ORDER_STATUS_CANCEL   = 'cancel';
    const ORDER_STATUS_PROGRESS = 'progress';

    const SYSTEM_ROBOX  = 'Robokassa.ru';
    const SYSTEM_IKASSA = 'Interkassa.com';
    const SYSTEM_MANUAL = 'Manual';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCallback('paymentCallback');
        $this->registerCallback('ajaxSaveData');
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return count($this->getOrderItems());
    }

    /**
     * Show basket items in admin panel
     * @return mixed
     */
    public function edit()
    {
        $basketItems   = $this->getOrderItems();
        $basketItemsId = $this->getOrderItemsId();

        JFactory::getSession()->set('items-' . $this->getItem()->id . '-' . $this->identifier, $this->data(), __CLASS__);

        if (!empty($basketItems)) {

            $items = JBModelFilter::model()->getZooItemsByIds($basketItemsId);

            if (!empty($items) && $layout = $this->getLayout('jbbasketitems.php')) {
                return self::renderLayout($layout, array(
                    'items'       => $items,
                    'basketItems' => $basketItems,
                    'params'      => isset($this->params) ? $this->params : null,
                    'renderMode'  => $this->renderMode,
                ));
            }
        }

        return '<p>' . JText::_('JBZOO_CART_ITEMS_NOT_FOUND') . '</p>';
    }

    /**
     * Render action
     * @param array $params
     * @return mixed|string
     * @throws JException
     */
    public function render($params = array())
    {
        $params       = $this->app->data->create($params);
        $this->params = $params; // hack for saving
        $template     = $params->get('template', 'default');

        if ($template == 'default') {
            return $this->edit();

        } else if ($template == 'table') {

            $this->renderMode = 'nopayment';

            return $this->edit();

        } else if ($template == 'totalprice') {
            return $this->getTotalPrice(true);

        } else if ($template == 'method') {
            return $this->getPaymentType();

        } else if ($template == 'status') {

            $summa = $this->getTotalPrice();
            if ($summa) {
                return '<span class="order-status ' . $this->getOrderStatus(false) . '">' . $this->getOrderStatus(true) . '</span>';
            } else {
                return JText::_('JBZOO_PAYMENT_STATUS_PAID');
            }

        } else if ($template == 'paymentlink') {

            $summa = $this->getTotalPrice();
            if ($this->getOrderStatus() == self::ORDER_STATUS_NOPAID && $summa) {

                $appId = $this->app->zoo->getApplication()->id;
                $href  = $this->app->jbrouter->basketPayment($params->get('basket-menuitem'), $appId, $this->getItem()->id);

                $html = '<p><a style="display:inline-block;" href="' . $href . '" class="jsGoto add-to-cart">'
                    . JText::_('JBZOO_PAYMENT_LINKTOFORM') . '</a></p>';

                return $html;
            }
        }

        return null;
    }

    /**
     * Render submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return '<input type="hidden" name="' . $this->getControlName('value') . '" value="_jbbaskteitems_" />';
    }

    /**
     * Validate submission
     * @param $value
     * @param $params
     * @return mixed
     * @throws JException
     */
    public function validateSubmission($value, $params)
    {
        $items = $this->app->jbcart->getAllItems();

        if (empty($items)) {
            throw new JException(JText::_('JBZOO_CART_VALIDATE_EMPTY_BASKET'));
        }

        foreach ($items as $key => $item) {
            $item = $this->app->table->item->get($item['itemId']);

            if ($item) {
                $items[$key]['name'] = $item->name;
            } else {
                unset($items[$key]);
            }
        }

        $appParams = $this->getItem()->getApplication()->getParams();
        if ((int)$appParams->get('global.jbzoo_cart_config.is_advance', 0)) {
            return array(
                'is_advance' => true,
                'items'      => $items,
                'order_info' => array(),
            );
        }

        return $items;
    }

    /**
     * Get total price
     */
    public function getTotalPrice($isFormated = false)
    {
        //return 5; // for interkassa debug

        $basketItems = $this->getOrderItems();

        $i        = 0;
        $summa    = 0;
        $count    = 0;
        $currency = '';

        if (!empty($basketItems)) {

            foreach ($basketItems as $basketInfo) {

                $count += $basketInfo['quantity'];

                $currency = $basketInfo['currency'];

                $subtotal = $basketInfo['quantity'] * $basketInfo['price'];
                $summa += $subtotal;
            }

            if ($isFormated) {
                return $this->app->jbmoney->toFormat($summa, $currency);
            }

            return round($summa, 2);
        }

        return null;
    }

    /**
     * Ajax call - paymentCallback
     */
    public function paymentCallback($date, $system = null, $additionalStatus = null, $comment = null)
    {
        $orderInfo = $this->getOrderInfo();
        $item      = $this->getItem();
        $appParams = $item->getApplication()->getParams();

        if (!isset($orderInfo['description'])) {
            $orderInfo['description'] = '';
        }

        $orderInfo = array(
            'payment_date'      => $date,
            'payment_system'    => $system,
            'additional_status' => $additionalStatus,
            'description'       => $orderInfo['description'] . "\n\n" . $comment,
        );

        if ($system == self::SYSTEM_MANUAL) {
            $orderInfo['status'] = self::ORDER_STATUS_NOPAID;
        } else {
            $orderInfo['status'] = self::ORDER_STATUS_PAID;
        }

        $this->bindOrderInfo($orderInfo);

        // save item
        $this->app->table->item->save($item);

        // notify Zoo dispatcher
        $this->app->event->dispatcher->notify($this->app->event->create($item, 'payment:callback', array(
            'item'      => $item,
            'appParams' => $appParams
        )));
    }

    /**
     * @return Int
     */
    protected function _getFirstElementId()
    {
        $basketItems = $this->data();

        reset($basketItems);
        $firstKey = key($basketItems);

        return $firstKey;
    }

    /**
     * Get current order status
     * @param bool $isFormated
     * @return mixed
     */
    public function getOrderStatus($isFormated = false)
    {
        $orderInfo = $this->app->data->create($this->getOrderInfo());
        $status    = $orderInfo->get('status', self::ORDER_STATUS_NODATA);

        if ($isFormated) {
            return JText::_('JBZOO_PAYMENT_STATUS_' . JString::strtoupper($status));
        }

        return $status;
    }

    /**
     * Get payment data
     * @deprecated
     * @return null
     */
    public function getPaymentData()
    {
        return $this->getOrderInfo();
    }

    /**
     * Get payment data
     * @return null
     */
    public function getPaymentType()
    {
        $orderInfo = $this->app->data->create($this->getOrderInfo());
        return $orderInfo->get('payment_system');
    }

    /**
     * Bind data on save
     * Hack for save from admin
     * @param array $data
     */
    public function bindData($data = array())
    {
        $saveData = $data;

        if ($this->getItem()) {
            $newData = JFactory::getSession()->get('items-' . $this->getItem()->id . '-' . $this->identifier, null, __CLASS__);
        }

        if (!empty($newData)) {
            $saveData = $newData;
        }

        // for administator only
        if (isset($data['order_info_admin'])) {

            $orderInfo = array(
                'status'      => $data['order_info_status'],
                'description' => $data['order_info_description'],
                //'payment_date'      => $this->app->date->create()->toSQL(),
                //'payment_system'    => 'Admin Edit',
                //'additional_status' => 'none',
            );

            if (isset($saveData['is_advance'])) {
                $saveData['order_info'] = array_merge($saveData['order_info'], $orderInfo);

            } else {
                reset($saveData);
                $firstKey = key($saveData);

                if (!isset($saveData[$firstKey]['order_info'])) {
                    $saveData[$firstKey]['order_info'] = array();
                }

                $saveData[$firstKey]['order_info'] = array_merge($saveData[$firstKey]['order_info'], $orderInfo);
            }
        }

        parent::bindData($saveData);
    }

    /**
     * Render HTML for admin edit form
     * @return null
     */
    public function getOrderSubForm()
    {
        if ($this->app->jbenv->isSite()) {
            return null;
        }

        if ($layout = $this->getLayout('adminform.php')) {

            $orderInfo = $this->getOrderInfo();

            return self::renderLayout($layout, array(
                'description' => isset($orderInfo['description']) ? $orderInfo['description'] : '',
                'status'      => $this->getOrderStatus(),
            ));

        }

        return null;
    }

    /**
     * Get status list
     * @return array
     */
    protected function _getStatusList()
    {
        return array(
            self::ORDER_STATUS_NODATA   => JText::_('JBZOO_PAYMENT_STATUS_' . self::ORDER_STATUS_NODATA),
            self::ORDER_STATUS_CANCEL   => JText::_('JBZOO_PAYMENT_STATUS_' . self::ORDER_STATUS_CANCEL),
            self::ORDER_STATUS_NOPAID   => JText::_('JBZOO_PAYMENT_STATUS_' . self::ORDER_STATUS_NOPAID),
            self::ORDER_STATUS_PROGRESS => JText::_('JBZOO_PAYMENT_STATUS_' . self::ORDER_STATUS_PROGRESS),
            self::ORDER_STATUS_PAID     => JText::_('JBZOO_PAYMENT_STATUS_' . self::ORDER_STATUS_PAID),
        );
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->basketItems();
        return parent::loadAssets();
    }

    /**
     * Check is element in advance mode
     * @return int
     */
    public function isAdvance()
    {
        $data = $this->data();
        return isset($data['is_advance']);
    }

    /**
     * Get order items
     * @return array
     */
    public function getOrderItems()
    {
        $data = $this->data();

        if ($this->isAdvance() && isset($data['items'])) {
            return $data['items'];
        }

        return $data;
    }

    /**
     * Get order item ID list
     * @return array
     */
    public function getOrderItemsId()
    {
        $items = $this->getOrderItems();

        $result = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $result[] = $item['itemId'];
            }
        }

        return $result;
    }

    /**
     * Get information about the order
     * @return array
     */
    public function getOrderInfo()
    {
        if ($this->isAdvance()) {
            $data = $this->data();
            if (isset($data['order_info'])) {
                return $data['order_info'];
            }

        } else {
            $first = $this->_getFirstElementId();
            if ($first) {
                $data = $this->get($first);
                if (isset($data['order_info'])) {
                    return $data['order_info'];
                }
            }
        }

        return array();
    }

    /**
     * Set new order information
     * @param array $data
     */
    public function bindOrderInfo(array $data)
    {
        $orderInfo = array_merge($this->getOrderInfo(), $data);

        if ($this->isAdvance()) {
            $this->set('order_info', $orderInfo);
        } else {
            $id   = $this->_getFirstElementId();
            $data = $this->data();

            $data[$id]['order_info'] = $orderInfo;
            $this->set($id, $data[$id]);
        }
    }

}
