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
 * Class JBCartOrder
 */
class JBCartOrder
{
    const ELEMENT_TYPE_DEFAULT       = 'elements';
    const ELEMENT_TYPE_CURRENCY      = 'currency';
    const ELEMENT_TYPE_SHIPPING      = 'shipping';
    const ELEMENT_TYPE_SHIPPINGFIELD = 'shippingfield';
    const ELEMENT_TYPE_MODIFIERITEM  = 'modifieritem';
    const ELEMENT_TYPE_MODIFIERPRICE = 'modifierprice';
    const ELEMENT_TYPE_MODIFIERS     = 'modifiers';
    const ELEMENT_TYPE_NOTIFICATION  = 'notification';
    const ELEMENT_TYPE_ORDER         = 'order';
    const ELEMENT_TYPE_PAYMENT       = 'payment';
    const ELEMENT_TYPE_PRICE         = 'price';
    const ELEMENT_TYPE_STATUS        = 'status';
    const ELEMENT_TYPE_VALIDATOR     = 'validator';

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
    public $params;

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
            self::ELEMENT_TYPE_CURRENCY      => $this->app->jbmoney->getData(),
            self::ELEMENT_TYPE_MODIFIERS     => $this->_loadModifiers(),
            self::ELEMENT_TYPE_ORDER         => array(),
            self::ELEMENT_TYPE_SHIPPINGFIELD => array(),
            self::ELEMENT_TYPE_VALIDATOR     => array(),
        ));
    }

    /**
     *
     */
    public function getName()
    {
        return sprintf('%06d', $this->id);
    }

    /**
     * @return mixed
     */
    protected function _loadModifiers()
    {
        $elementsGroups = $this->app->jbcartposition->loadPositions('modifierevents');

        $result = array();
        foreach ($elementsGroups as $elements) {
            foreach ($elements as $element) {
                $result[$element->getElementGroup()][$element->identifier] = $element;
            }
        }

        return $result;
    }

    /**
     * Get the name of the user that created the Order
     * @return string The name of the author
     */
    /**
     * @return JUser
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
     * @return string The Order state
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_config->get('config.default_currency', $this->app->jbmoney->getDefaultCur());
    }

    /**
     * @param bool $toFormat
     * @return float|int
     */
    public function getTotalSum($toFormat = false)
    {
        $baseCur = $this->getCurrency();
        $sum     = $this->getTotalForSevices();

        // get modifiers
        $modifiers = $this->getPriceModifiers();
        foreach ($modifiers as $modifier) {
            $sum = $modifier->modify($sum, $baseCur, $this);
        }

        if ((int)$toFormat) {
            $sum = $this->app->jbmoney->toFormat($sum, $baseCur);
        }

        return $sum;
    }

    /**
     * @param $toFormat
     * @return int
     */
    public function getTotalForItems($toFormat = false)
    {
        $items   = $this->getItems();
        $jbmoney = $this->app->jbmoney;
        $sum     = 0;
        $baseCur = $this->getCurrency();

        // get Items prices
        foreach ($items as $item) {
            $price = $item['price'] * $item['quantity'];
            $sum += $jbmoney->calc($sum, $baseCur, $price, $item['currency']);
        }

        if ((int)$toFormat) {
            $sum = $this->app->jbmoney->toFormat($sum, $baseCur);
        }

        return $sum;
    }

    /**
     * @param $toFormat
     * @return float|int
     */
    public function getTotalForSevices($toFormat = false)
    {
        $sum     = $this->getTotalForItems(false);
        $baseCur = $this->getCurrency();

        // check payment rate
        if ($payment = $this->getPayment()) {
            $sum = $payment->modify($sum, $baseCur, $this);
        }

        // check shipping rate
        if ($shipping = $this->getShipping()) {
            $sum = $shipping->modify($sum, $baseCur, $this);
        }

        if ((int)$toFormat) {
            $sum = $this->app->jbmoney->toFormat($sum, $baseCur);
        }

        return $sum;
    }

    /**
     * Set the Order published state
     * @param string $statusCode The new Order state code
     * @return $this
     */
    public function setStatus($statusCode)
    {
        $statusCode = JString::trim($statusCode);
        $newStatus  = $this->app->jbcartstatus->getByCode($statusCode);

        if (!$statusCode || !$newStatus) {
            return $this;
        }

        if (!$this->_status || $this->_status->getCode() != $newStatus->getCode()) {

            // set state
            $oldState      = $this->_status;
            $this->_status = $newStatus;

            // fire event
            $this->app->event->dispatcher->notify($this->app->event->create($this, 'Order:stateChanged', compact('oldState')));
        }

        return $this;
    }

    /**
     * @param $identifier
     * @param string $type
     * @return JBCartElement
     */
    public function getElement($identifier, $type = self::ELEMENT_TYPE_ORDER)
    {
        if (isset($this->_elements[$type][$identifier])) {
            return $this->_elements[$type][$identifier];
        }

        $fieldsConfig = $this->_config->get($type . '.list');

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
     * @return mixed
     */
    public function getPriceModifiers()
    {
        $modifiers = $this->_elements[self::ELEMENT_TYPE_MODIFIERS][self::ELEMENT_TYPE_MODIFIERPRICE];
        return $modifiers;
    }

    /**
     * @param $identifier
     * @return JBCartElementOrder
     */
    public function getFieldElement($identifier)
    {
        return $this->getElement($identifier, self::ELEMENT_TYPE_ORDER);
    }

    /**
     * @param $identifier
     * @return JBCartElementShipping
     */
    public function getShippingElement($identifier)
    {
        return $this->getElement($identifier, self::ELEMENT_TYPE_SHIPPING);
    }

    /**
     * @param $identifier
     * @return JBCartElementPayment
     */
    public function getPaymentElement($identifier)
    {
        return $this->getElement($identifier, self::ELEMENT_TYPE_PAYMENT);
    }

    /**
     * @param $identifier
     * @return JBCartElementShippingField
     */
    public function getShippingFieldElement($identifier)
    {
        return $this->getElement($identifier, self::ELEMENT_TYPE_SHIPPINGFIELD);
    }

    /**
     * @param $identifier
     * @return JBCartElementValidator
     */
    public function getValidatorElement($identifier)
    {
        return $this->getElement($identifier, self::ELEMENT_TYPE_VALIDATOR);
    }

    /**
     * Bind data and validate order
     * @param $formData
     * @return int
     */
    public function bind($formData)
    {
        $errors = 0;
        if (isset($formData[self::ELEMENT_TYPE_ORDER])) {
            $params = $this->app->jbrenderer->create('OrderSubmission')->getLayoutParams();
            $errors += $this->_bindElements($formData[self::ELEMENT_TYPE_ORDER], self::ELEMENT_TYPE_ORDER, $params);
        }

        if (isset($formData[self::ELEMENT_TYPE_SHIPPINGFIELD])) {
            $params = $this->app->jbrenderer->create('ShippingFields')->getLayoutParams();
            $errors += $this->_bindElements($formData[self::ELEMENT_TYPE_SHIPPINGFIELD], self::ELEMENT_TYPE_SHIPPINGFIELD, $params);
        }

        if (isset($formData[self::ELEMENT_TYPE_SHIPPING])) {
            $errors += $this->_bindShipping($formData[self::ELEMENT_TYPE_SHIPPING]);
        }

        if (isset($formData[self::ELEMENT_TYPE_PAYMENT])) {
            $errors += $this->_bindPayment($formData[self::ELEMENT_TYPE_PAYMENT]);
        }

        return $errors;
    }

    /**
     * @param $data
     * @param string $type
     * @param array $elementsParams
     * @return int
     */
    protected function _bindElements($data, $type = self::ELEMENT_TYPE_ORDER, $elementsParams = array())
    {
        $errors = 0;

        foreach ($elementsParams as $elementParam) {

            $identifier = $elementParam['identifier'];
            $value      = isset($data[$identifier]) ? $this->app->data->create($data[$identifier]) : array();

            try {
                if (($element = $this->getElement($identifier, $type))) {

                    $params   = $this->app->data->create($elementParam);
                    $elemData = $element->validateSubmission($value, $params);
                    $element->bindData($elemData);
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

        if ($this->getTotalSum() <= 0) {
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
     * @return JBCartElementShipping
     */
    public function getShipping()
    {
        return $this->_shipping;
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

            if ($element = $this->getElement($elementId, self::ELEMENT_TYPE_SHIPPING)) {
                $elemData = $element->validateSubmission($value, array());
                $element->bindData($elemData);
                $this->_shipping = $element;
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

            if ($element = $this->getElement($elementId, self::ELEMENT_TYPE_PAYMENT)) {
                $elemData = $element->validateSubmission($value, array());
                $element->bindData($elemData);
                $this->_payment = $element;
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
     * @return JSONData
     */
    public function getItems()
    {
        $items = $this->_items;

        if (!$this->id) {
            $items = JBCart::getInstance()->getItems();
        }

        $result = array();
        foreach ($items as $key => $item) {
            $itemData = $this->app->data->create($item);
            $item     = $this->app->table->item->get($itemData->get('item_id'));
            $itemData->set('item', $item);
            $result[$key] = $itemData;
        }

        return $result;
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getFields()
    {
        $result = array();
        foreach ($this->_elements[self::ELEMENT_TYPE_ORDER] as $element) {
            $result[$element->identifier] = $element->data();
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
        foreach ($this->_elements[self::ELEMENT_TYPE_SHIPPINGFIELD] as $element) {
            $result[$element->identifier] = $element->data();
        }

        return $this->app->data->create($result);
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getModifiers()
    {
        return $this->_elements[self::ELEMENT_TYPE_MODIFIERS];
    }

    /**
     * Get order item list
     * @return JSONData
     */
    public function getModifiersData()
    {
        $elementsGroups = $this->_elements[self::ELEMENT_TYPE_MODIFIERS];

        $result = array();
        foreach ($elementsGroups as $elements) {
            foreach ($elements as $element) {
                $result[$element->getElementGroup()][$element->identifier] = $element->getOrderData();
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
        $list = $this->app->jbmoney->getData();

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

        $this->setItemsData($data->get('items'));
        $this->setPaymentData($data->get('payment'));
        $this->setShippingData($data->get('shipping'));
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
     */
    public function setPaymentData($data)
    {
        $data = $this->app->data->create($data);

        $config  = $data->get('config');
        $element = $this->app->jbcartelement->create($config['type'], self::ELEMENT_TYPE_PAYMENT, $config);
        $element->bindData($data->get('data', array()));
        $element->setOrder($this);
        $element->identifier = $config['identifier'];

        $this->_elements[self::ELEMENT_TYPE_PAYMENT][$element->identifier] = $this->_payment = $element;
    }

    /**
     * @param $data
     */
    public function setShippingData($data)
    {
        $data = $this->app->data->create($data);

        $config  = $data->get('config');
        $element = $this->app->jbcartelement->create($config['type'], self::ELEMENT_TYPE_SHIPPING, $config);
        $element->bindData($data->get('data', array()));
        $element->setOrder($this);
        $element->identifier = $config['identifier'];

        $this->_elements[self::ELEMENT_TYPE_SHIPPING][$element->identifier] = $this->_shipping = $element;
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
        $this->_elements[self::ELEMENT_TYPE_CURRENCY] = $this->app->data->create($dataFields);
    }

    /**
     * @param $dataFields
     */
    public function setModifiersData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        $elements = array();
        foreach ($dataFields as $group => $items) {
            foreach ($items as $identifier => $data) {

                $data = $this->app->data->create($data);

                $config  = $data->get('config');
                $element = $this->app->jbcartelement->create($config['type'], $config['group'], $config);
                $element->bindData($data->get('data', array()));
                $element->setOrder($this);
                $element->identifier = $identifier;

                $elements[$group][$identifier] = $element;
            }
        }

        $this->_elements[self::ELEMENT_TYPE_MODIFIERS] = $elements;
    }

}

/**
 * Class JBCartOrderException
 */
class JBCartOrderException extends AppException
{

}