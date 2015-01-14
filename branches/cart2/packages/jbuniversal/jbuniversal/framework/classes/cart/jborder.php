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
 * Class JBCartOrder
 */
class JBCartOrder
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $modified;

    /**
     * @var int
     */
    public $created_by;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var ParameterData
     */
    public $params = array();

    /**
     * @var App
     */
    public $app;

    /**
     * The list of elements of the Order
     * @var array
     */
    protected $_elements = array();

    /**
     * @var JSONData
     */
    protected $_config = null;

    /**
     * @var JBCartElementShipping
     */
    protected $_shipping = null;

    /**
     * @var JBCartElementPayment
     */
    protected $_payment = null;

    /**
     * Order published state
     * @var JBCartElementStatus
     */
    protected $_status = null;

    /**
     * Order items list
     * @var array
     */
    protected $_items = array();

    /**
     * Class Constructor
     */
    public function __construct()
    {
        // get app instance
        $this->app = App::getInstance('zoo');

        // decorate data as object
        $this->params = $this->app->parameter->create($this->params);

        // decorate data as object
        $this->_elements = $this->app->data->create($this->_elements);
        $this->_items    = $this->app->data->create($this->_items);

        $this->_config = JBModelConfig::model()->getGroup('cart');

        $this->_elements = $this->app->data->create(array(
            JBCart::ELEMENT_TYPE_MODIFIERS     => $this->_loadCurrentModifiers(),
            JBCart::ELEMENT_TYPE_ORDER         => array(),
            JBCart::ELEMENT_TYPE_SHIPPINGFIELD => array(),
            JBCart::ELEMENT_TYPE_VALIDATOR     => array(),
        ));
    }

    /**
     * Get formated order name
     */
    public function getName()
    {
        return sprintf('%06d', $this->id);
    }

    /**
     * @return mixed
     */
    protected function _loadCurrentModifiers()
    {
        $elementsGroups = $this->app->jbcartposition->loadPositions(JBCart::CONFIG_MODIFIERS);

        $result = array();
        foreach ($elementsGroups as $groupName => $elements) {

            $this->params[$groupName] = array();

            foreach ($elements as $element) {
                $element->setOrder($this);
                $orderdata = $element->getOrderData();

                $result[$groupName][$element->identifier]       = $element;
                $this->params[$groupName][$element->identifier] = $orderdata['config'];
            }
        }

        return $result;
    }

    /**
     * @return JUser|null
     */
    public function getAuthor()
    {
        $user = $this->app->user->get($this->created_by);
        if (empty($user)) {
            return null;
        }

        return $user;
    }

    /**
     * Get the Order published state
     * @return JBCartElementStatus
     */
    public function getStatus()
    {
        if (!empty($this->_status)) {
            return $this->_status;
        }

        $status = JBCart::getInstance()->getDefaultStatus();

        return $status;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_config->get('config.default_currency', $this->app->jbmoney->getDefaultCur());
    }

    /**
     * @return JBCartValue
     */
    public function getTotalSum()
    {
        $items = $this->getItems();

        $summa = $this->getTotalForSevices();
        if (count($items) > 0) {
            // get modifiers
            $modifiers = $this->getModifiers(JBCart::MODIFIER_ORDER);
            foreach ($modifiers as $modifier) {
                $summa->addModify($modifier);
            }
        }

        return $summa;
    }

    /**
     * @param bool $devideByItem
     * @return array|JBCartValue
     */
    public function getTotalForItems($devideByItem = false)
    {
        $items = $this->getItems();
        $summa = $this->val();

        $modifiers = $this->getModifiers(JBCart::MODIFIER_ITEM);

        $itemsSums = array();

        // get Items prices
        foreach ($items as $key => $item) {
            $itemPrice = $this->val($item['total'])->multiply($item['quantity']);

            $summa->add($itemPrice);

            foreach ($modifiers as $modifier) {
                $summa->addModify($modifier);
            }

            $itemsSums[$key] = $itemPrice;
        }

        if ($devideByItem) {
            return $itemsSums;
        }

        return $summa;
    }

    /**
     * @return float|int
     */
    public function getTotalCount()
    {
        $items  = $this->getItems();
        $result = 0;
        foreach ($items as $item) {
            $result += (float)$item['quantity'];
        }

        return $result;
    }

    /**
     * @return JBCartValue
     */
    public function getShippingPrice()
    {
        if ($shipping = $this->getShipping()) {
            return $shipping->getRate();
        }

        return $this->val(0);
    }

    /**
     * @return JBCartValue
     */
    public function getTotalForSevices()
    {
        $summa = $this->getTotalForItems();

        if (!$summa->isEmpty()) {

            // check payment rate
            if ($payment = $this->getPayment()) {
                $summa = $payment->modify($summa);
            }

            // check shipping rate
            if ($shipping = $this->getShipping()) {
                $summa = $shipping->modify($summa);
            }
        }

        return $summa;
    }

    /**
     * @return int
     */
    public function getTotalWeight()
    {
        $items = $this->getItems();

        $result = 0;
        foreach ($items as $item) {
            if (isset($item->elements['_weight'])) {
                $result += (float)$item->elements['_weight'] * (float)$item->get('quantity', 0);
            }
        }

        return $result;
    }

    /**
     * Set the Order published state
     * @param string $statusCode The new Order state code
     * @param string $type       Status type
     * @return $this
     */
    public function setStatus($statusCode, $type = JBCart::STATUS_ORDER)
    {
        $statusCode = JString::trim($statusCode);
        $newStatus  = $this->app->jbcartstatus->getByCode($statusCode, $type);

        if (!$statusCode || !$newStatus) {
            return $this;
        }

        $newCode = $newStatus->getCode();
        if ($type == JBCart::STATUS_ORDER) {

            if (!$this->_status) {
                $this->_status = $newStatus;

                return $this;

            } else if ((string)$this->_status != (string)$newCode) {

                $oldStatus     = $this->_status;
                $this->_status = $newStatus;
                $this->app->event->dispatcher->notify($this->app->event->create($this, 'basket:orderStatus', array(
                    'oldStatus' => (string)$oldStatus,
                    'newStatus' => (string)$newCode,
                )));
            }

        } else if ($type == JBCart::STATUS_PAYMENT) {

            if ($payment = $this->getPayment()) {
                $payment->setStatus($newCode);
            }

        } else if ($type == JBCart::STATUS_SHIPPING) {

            if ($shipping = $this->getShipping()) {
                $shipping->setStatus($newCode);
            }

        }

        return $this;
    }

    /**
     * @param        $identifier
     * @param string $type
     * @return JBCartElement
     */
    public function getElement($identifier, $type = JBCart::CONFIG_FIELDS)
    {
        if (isset($this->_elements[$type]) && isset($this->_elements[$type][$identifier])) {
            return $this->_elements[$type][$identifier];
        }

        if ($this->id == 0) {
            $fieldsConfig = $this->_config->get($type . '.' . JBCart::DEFAULT_POSITION);
        } else {
            $fieldsConfig = $this->params->get($type);
        }

        if (isset($fieldsConfig[$identifier])) {

            $config  = $fieldsConfig[$identifier];
            $element = $this->app->jbcartelement->create($config['type'], $config['group'], $config);
            $element->setOrder($this);

            $this->_elements[$type][$identifier] = $element;

            return $this->_elements[$type][$identifier];
        }

        return null;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getModifiers($type = JBCart::MODIFIER_ORDER)
    {
        $elements  = $this->_elements[JBCart::ELEMENT_TYPE_MODIFIERS];
        $modifiers = isset($elements[$type]) ? $elements[$type] : array();

        return $modifiers;
    }

    /**
     * @param $identifier
     * @return JBCartElementOrder
     */
    public function getFieldElement($identifier)
    {
        return $this->getElement($identifier, JBCart::CONFIG_FIELDS);
    }

    /**
     * @param $identifier
     * @param $groupName
     * @return JBCartElementModifierPrice
     */
    public function getModifierElement($identifier, $groupName)
    {
        return $this->getElement($identifier, $groupName);
    }

    /**
     * @param $identifier
     * @return JBCartElementShipping
     */
    public function getShippingElement($identifier)
    {
        return $this->getElement($identifier, JBCart::CONFIG_SHIPPINGS);
    }

    /**
     * @param $identifier
     * @return JBCartElementPayment
     */
    public function getPaymentElement($identifier)
    {
        return $this->getElement($identifier, JBCart::CONFIG_PAYMENTS);
    }

    /**
     * @param $identifier
     * @return JBCartElementShippingField
     */
    public function getShippingFieldElement($identifier)
    {
        return $this->getElement($identifier, JBCart::CONFIG_SHIPPINGFIELDS);
    }

    /**
     * @param $identifier
     * @return JBCartElementValidator
     */
    public function getValidatorElement($identifier)
    {
        return $this->getElement($identifier, JBCart::CONFIG_VALIDATORS);
    }

    /**
     * Bind data and validate order
     * @param $formData
     * @return int
     */
    public function bind($formData)
    {
        $errors = 0;

        if (isset($formData[JBCart::ELEMENT_TYPE_ORDER])) {
            $params = $this->app->jbrenderer->create('Order')->getLayoutParams();
            $errors += $this->_bindElements($formData[JBCart::ELEMENT_TYPE_ORDER], JBCart::CONFIG_FIELDS, $params);
        }

        if (isset($formData[JBCart::ELEMENT_TYPE_SHIPPINGFIELD])) {
            $params = $this->app->jbrenderer->create('ShippingFields')->getLayoutParams();
            $errors += $this->_bindElements($formData[JBCart::ELEMENT_TYPE_SHIPPINGFIELD], JBCart::CONFIG_SHIPPINGFIELDS, $params);
        }

        if (isset($formData[JBCart::ELEMENT_TYPE_SHIPPING])) {
            $errors += $this->_bindShipping($formData[JBCart::ELEMENT_TYPE_SHIPPING]);
        }

        if (isset($formData[JBCart::ELEMENT_TYPE_PAYMENT])) {
            $errors += $this->_bindPayment($formData[JBCart::ELEMENT_TYPE_PAYMENT]);
        }


        return $errors;
    }

    /**
     * @param        $data
     * @param string $type
     * @param array  $elementsParams
     * @return int
     */
    protected function _bindElements($data, $type = JBCart::CONFIG_FIELDS, $elementsParams = array())
    {
        $errors = 0;

        foreach ($elementsParams as $elementParam) {

            $identifier = $elementParam['identifier'];
            $value      = isset($data[$identifier]) ? $this->app->data->create($data[$identifier]) : array();

            try {

                if (($element = $this->getElement($identifier, $type))) {
                    $params    = $this->app->data->create($elementParam);
                    $elemData  = $element->validateSubmission($value, $params);
                    $orderData = $element->getOrderData();
                    $element->bindData($elemData);
                    $this->params[$type][$identifier] = $orderData['config'];
                }

            } catch (AppValidatorException $e) {

                if (isset($element)) {
                    $element->error = $e;
                    $element->bindData($value);
                }

                $errors++;
            }
        }

        return $errors;
    }

    /**
     * Validate cart
     * @return array
     */
    public function isValid()
    {
        $errorMessages = array();

        $errorMessages += $this->_isOrderValidByStd();
        $errorMessages += $this->_isOrderValidByElements();

        return $errorMessages;
    }

    /**
     *
     */
    protected function _isOrderValidByElements()
    {
        $errorMessages = array();

        $validators = $this->app->jbcartvalidator->getByEvent(JBCartValidatorHelper::EVENT_BEFORE_CREATE, $this);

        if (empty($validators)) {
            return $errorMessages;
        }

        foreach ($validators as $validator) {

            try {
                $validator->isValid();

            } catch (JBCartElementValidatorException $e) {
                $errorMessages[] = $e->getMessage();
            }
        }

        return $errorMessages;
    }

    /**
     * @return array
     */
    protected function _isOrderValidByStd()
    {
        $errorMessages = array();

        if ($this->getItems()->count() == 0) {
            $errorMessages[] = 'JBZOO_CART_VALIDATOR_EMPTY';
        }

        if ($this->getTotalSum()->compare('0', '<=')) {
            $errorMessages[] = 'JBZOO_CART_VALIDATOR_ZERO_SUM';
        }

        return $errorMessages;
    }

    /**
     * return JBCartElementPayment
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * @return null|string
     */
    public function getPaymentStatus()
    {
        if ($payment = $this->getPayment()) {
            return $payment->getStatus();
        }

        return null;
    }

    /**
     * @return JBCartElementShipping
     */
    public function getShipping()
    {
        if (!$this->id) {
            if ($shippingData = JBCart::getInstance()->getShipping()) {

                $shipping = $this->getShippingElement($shippingData['_shipping_id']);

                unset($shippingData['_shipping_id']);
                if ($shipping) {
                    $shipping->bindData($shippingData);

                    return $shipping;
                }
            }
        }

        return $this->_shipping;
    }

    /**
     * @return null|string
     */
    public function getShippingStatus()
    {
        if ($shipping = $this->getShipping()) {
            return $shipping->getStatus();
        }

        return null;
    }

    /**
     * @param $data
     * @return int
     */
    protected function _bindShipping($data)
    {
        $errors = 0;

        $elementId = $data['_shipping_id'];
        unset($data['_shipping_id']);
        $value = $this->app->data->create($data);

        try {

            if ($element = $this->getElement($elementId, JBCart::CONFIG_SHIPPINGS)) {
                $elemData  = $element->validateSubmission($value, array());
                $orderData = $element->getOrderData();
                $element->bindData($elemData);

                $this->_shipping = $element;

                $this->params[JBCart::CONFIG_SHIPPINGS][$elementId] = $orderData['config'];
            }

        } catch (AppValidatorException $e) {

            if (isset($element)) {
                $element->error = $e;
                $element->bindData($value);
            }

            $errors++;
        }

        return $errors;
    }

    /**
     * @param $data
     * @return int
     */
    protected function _bindPayment($data)
    {
        $errors = 0;

        $elementId = $data['_payment_id'];
        unset($data['_payment_id']);
        $value = $this->app->data->create($data);

        try {

            if ($element = $this->getElement($elementId, JBCart::CONFIG_PAYMENTS)) {
                $elemData  = $element->validateSubmission($value, array());
                $orderData = $element->getOrderData();
                $element->bindData($elemData);
                $this->_payment = $element;

                $this->params[JBCart::CONFIG_PAYMENTS][$elementId] = $orderData['config'];
            }

        } catch (AppValidatorException $e) {

            if (isset($element)) {
                $element->error = $e;
                $element->bindData($value);
            }

            $errors++;
        }

        return $errors;
    }

    /**
     * Get order item list
     * @param bool $loadItem
     * @return JSONData
     */
    public function getItems($loadItem = true)
    {
        $items = $this->_items;

        if (!$this->id) {
            $items = JBCart::getInstance()->getItems();
        }

        $result = array();
        foreach ($items as $key => $data) {
            if ($loadItem) {
                $item         = $this->app->table->item->get($data['item_id']);
                $data['item'] = $item;
            }
            $result[$key] = $this->app->data->create($data);
        }

        $result = $this->app->data->create($result);

        return $result;
    }

    /**
     * Get order item list
     * @return mixed
     */
    public function getFields()
    {
        $result = array();
        if (isset($this->_elements[JBCart::CONFIG_FIELDS])) {
            foreach ($this->_elements[JBCart::CONFIG_FIELDS] as $element) {
                $result[$element->identifier] = $element->data();
            }
        }

        return $this->app->data->create($result);
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getShippingFields()
    {
        $result = array();

        if (isset($this->_elements[JBCart::ELEMENT_TYPE_SHIPPINGFIELD])) {
            foreach ($this->_elements[JBCart::ELEMENT_TYPE_SHIPPINGFIELD] as $element) {
                $result[$element->identifier] = $element->data();
            }
        }

        return $this->app->data->create($result);
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getModifiersData()
    {
        $result = array();

        if (isset($this->_elements[JBCart::ELEMENT_TYPE_MODIFIERS])) {

            $elementsGroups = $this->_elements[JBCart::ELEMENT_TYPE_MODIFIERS];
            foreach ($elementsGroups as $groupName => $elements) {
                foreach ($elements as $element) {
                    $result[$groupName][$element->identifier] = $element->data();
                }
            }
        }

        return $this->app->data->create($result);
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getCurrencyList()
    {
        $params = $this->getParams();
        $list   = $params->get(JBCart::ELEMENT_TYPE_CURRENCY, $this->app->jbmoney->getData());

        return $this->app->data->create($list);
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getParams()
    {
        return $this->app->data->create($this->params);
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $data = $this->app->data->create($data);

        $this->id         = (int)$data->get('id');
        $this->created    = $data->get('created');
        $this->modified   = $data->get('modified');
        $this->created_by = $data->get('created_by');
        $this->comment    = $data->get('comment');
        $this->params     = $this->app->data->create($data->get('params'));

        $this->setStatus($data->get('status'));
        $this->setItemsData($data->get('items'));
        $this->setPaymentData($data->get('payment'), $data->get('status_payment'));
        $this->setShippingData($data->get('shipping'), $data->get('status_shipping'));
        $this->setShippingFieldsData($data->get('shippingfields'));
        $this->setFieldsData($data->get('fields'));
        $this->setModifiersData($data->get('modifiers'));
        $this->setCurrencyData($data->get('currency'));

    }

    /**
     * @param $items
     */
    public function setItemsData($items)
    {
        $this->_items = json_decode($items, true);
    }

    /**
     * @param $data
     * @param $status
     * @return null
     */
    public function setPaymentData($data, $status = 'undefined')
    {
        $data = $this->app->data->create($data);

        $payments = $this->params->get(JBCart::ELEMENT_TYPE_PAYMENT);
        if (empty($payments)) {
            return null;
        }

        $config = current($payments);

        $element = $this->app->jbcartelement->create($config['type'], JBCart::ELEMENT_TYPE_PAYMENT, $config);
        $element->bindData($data);
        $element->setOrder($this);
        $element->setStatus($status);
        $element->identifier = $config['identifier'];

        $this->_elements[JBCart::ELEMENT_TYPE_PAYMENT][$element->identifier] = $this->_payment = $element;
    }

    /**
     * @param $data
     * @param $status
     * @return null
     */
    public function setShippingData($data, $status = 'undefined')
    {
        $data = $this->app->data->create($data);

        $shippings = $this->params->get(JBCart::ELEMENT_TYPE_SHIPPING);
        if (empty($shippings)) {
            return null;
        }

        $config = current($shippings);

        $element = $this->app->jbcartelement->create($config['type'], JBCart::ELEMENT_TYPE_SHIPPING, $config);
        $element->bindData($data);
        $element->setOrder($this);
        $element->setStatus($status);
        $element->identifier = $config['identifier'];

        $this->_elements[JBCart::ELEMENT_TYPE_SHIPPING][$element->identifier] = $this->_shipping = $element;
    }

    /**
     * @param $dataFields
     */
    public function setShippingFieldsData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        foreach ($dataFields as $identifier => $data) {
            $element = $this->getShippingFieldElement($identifier);
            $element->bindData($data);
            $element->identifier = $identifier;
        }
    }

    /**
     * @param $dataFields
     */
    public function setFieldsData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        foreach ($dataFields as $identifier => $data) {
            $element = $this->getFieldElement($identifier);
            $element->bindData($data);
            $element->identifier = $identifier;
        }

    }

    /**
     * @param $dataFields
     */
    public function setCurrencyData($dataFields)
    {
        $this->_elements[JBCart::ELEMENT_TYPE_CURRENCY] = $this->app->data->create($dataFields);
    }

    /**
     * @param $dataFields
     */
    public function setModifiersData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        $elements = array();
        foreach ($dataFields as $groupName => $items) {
            foreach ($items as $identifier => $data) {

                $data    = $this->app->data->create($data);
                $element = $this->getModifierElement($identifier, $groupName);
                $element->bindData($data);
                $element->setOrder($this);
                $element->identifier = $identifier;

                $elements[$groupName][$identifier] = $element;
            }
        }

        $this->_elements[JBCart::ELEMENT_TYPE_MODIFIERS] = $elements;
    }

    /**
     * @param $data
     */
    public function updateData($data)
    {
        $data = $this->app->data->create($data);

        if ($paymentData = $data->get('payment')) {
            if (isset($paymentData['status'])) {
                $this->setStatus($paymentData['status'], JBCart::STATUS_PAYMENT);
            }
        }

        if ($shippingData = $data->get('shipping')) {
            if (isset($shippingData['status'])) {
                $this->setStatus($shippingData['status'], JBCart::STATUS_SHIPPING);
            }
        }

        $this->comment = $data->get('comment');
        $this->setStatus($data->get('status'), JBCart::STATUS_ORDER);
    }

    /**
     * @param string $data
     * @param string $currency
     * @return JBCartValue
     */
    public function val($data = '0', $currency = null)
    {
        $rates = (array)$this->getCurrencyList();

        return JBcart::val($data, $currency, $rates);
    }

}

/**
 * Class JBCartOrderException
 */
class JBCartOrderException extends AppException
{

}