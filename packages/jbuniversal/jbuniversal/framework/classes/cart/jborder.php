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
        $this->app     = App::getInstance('zoo');
        $this->_config = JBModelConfig::model()->getGroup('cart');

        // decorate data as object
        $this->params = $this->app->parameter->create(array(
            JBCart::CONFIG_MODIFIER_ORDER_PRICE => $this->_config->find(JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE . '.' . JBCart::DEFAULT_POSITION, array()),
            JBCart::CONFIG_MODIFIER_ITEM_PRICE  => $this->_config->find(JBCart::ELEMENT_TYPE_MODIFIER_ITEM_PRICE . '.' . JBCart::DEFAULT_POSITION, array()),
        ));

        // decorate data as object
        $this->_elements = $this->app->data->create(array());
        $this->_items    = $this->app->data->create($this->_items);
    }

    /**
     * Get formated order name
     * @param string $format
     * @return string
     */
    public function getName($format = 'short')
    {
        if ($format == 'short') {
            return sprintf('%06d', $this->id);

        } else if ($format == 'full') {

            $created = $this->app->jbdate->toHuman($this->created);
            $name    = JText::sprintf('JBZOO_ORDER_NAME_DATE', $this->getName('short'), $created);

            return $name;
        }

        return $this->getName('short');
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
     * @return \JUser|null
     */
    public function getUser()
    {
        $author = $this->getAuthor();

        if (!$author) {
            $author = JFactory::getUser(0);
        }

        return $author;
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
        $default = $this->_config->get('config.default_currency', $this->app->jbmoney->getDefaultCur());

        return $this->params->find('config.default_currency', $default);
    }

    /**
     * @return JBCartValue
     */
    public function getTotalSum()
    {
        $summa = $this->getTotalForSevices();

        if ($summa->isNegative() || $summa->isEmpty()) {
            $summa->setEmpty();
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

        $itemsSums = array();

        // get Items prices
        foreach ($items as $key => $item) {

            $itemPrice = $this->val($item['total']);
            $itemPrice->multiply($item['quantity']);

            if ($itemPrice->isNegative() || $itemPrice->isEmpty()) {
                $itemPrice->setEmpty();
            }

            $summa->add($itemPrice);

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
     * @return int
     */
    public function getTotalCountSku()
    {
        return count($this->getItems());
    }

    /**
     * @return JBCartValue
     */
    public function getShippingPrice()
    {
        if ($shipping = $this->getShipping()) {

            $rate = $this->val(0);
            try {
                $rate = $shipping->getRate();
            } catch (JBCartElementShippingException $e) {
            }

            return $rate;
        }

        return $this->val(0);
    }

    /**
     * @return JBCartValue
     */
    public function getTotalForSevices()
    {
        $summa = $this->getTotalForItems();
        $user  = $this->getUser();

        // get modifiers
        $modifiers = $this->getModifiersOrderPrice();
        if (!empty($modifiers)) {
            foreach ($modifiers as $modifier) {
                if ($modifier->canAccess($user)) {
                    $modifier->modify($summa);
                }
            }
        }

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
        $newStatus  = $this->app->jbcartstatus->getByCode($statusCode, $type, $this);

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
                $this->app->jbevent->fire($this, 'basket:orderStatus', array(
                    'oldStatus' => (string)$oldStatus,
                    'newStatus' => (string)$newCode,
                ));

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
     * @return array
     */
    public function getModifiersOrderPrice()
    {
        if (!isset($this->_elements[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE])) {

            $elementConfigs = $this->params->get(JBCart::CONFIG_MODIFIER_ORDER_PRICE);

            $user     = $this->getUser();
            $elements = array();
            if (!empty($elementConfigs)) {
                foreach ($elementConfigs as $elementId => $elementConfig) {
                    if ($modifierElement = $this->getModifierOrderPriceElement($elementId)) {
                        if ($modifierElement->canAccess($user)) {
                            $elements[$elementId] = $modifierElement;
                        }
                    }
                }
            }

            $this->_elements[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE] = $elements;
        }

        return $this->_elements[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE];
    }

    /**
     * @param ElementJBPrice $jbPrice
     * @param array          $itemData
     * @return array
     */
    public function getModifiersItemPrice(ElementJBPrice $jbPrice = null, $itemData = array())
    {
        $elementConfigs = $this->params->get(JBCart::CONFIG_MODIFIER_ITEM_PRICE);

        $elements = array();
        if (!empty($elementConfigs)) {
            foreach ($elementConfigs as $elementId => $elementConfig) {
                if ($element = $this->getModifierItemPriceElement($elementId, $jbPrice, $itemData)) {
                    $elements[$elementId] = $element;
                }
            }
        }

        return $elements;
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

            $config = $fieldsConfig[$identifier];
            if ($element = $this->app->jbcartelement->create($config['type'], $config['group'], $config)) {
                $element->setOrder($this);

                if (!isset($this->_elements[$type])) {
                    $this->_elements[$type] = array();
                }

                $this->_elements[$type][$identifier] = $element;

                return $this->_elements[$type][$identifier];
            }

        }

        return null;
    }

    /**
     * @param $identifier
     * @return JBCartElementModifierOrderPrice
     */
    public function getModifierOrderPriceElement($identifier)
    {
        $modifierElement = $this->getElement($identifier, JBCart::CONFIG_MODIFIER_ORDER_PRICE);
        if (!$this->id) {
            $data = JBcart::getInstance()->getModifier($identifier);
            $modifierElement->bindData($data);
        }

        return $modifierElement;
    }

    /**
     * @param                $identifier
     * @param ElementJBPrice $jbPrice
     * @param array          $itemData
     * @return JBCartElementModifierItemPrice
     */
    public function getModifierItemPriceElement($identifier, ElementJBPrice $jbPrice = null, $itemData = array())
    {
        if ($element = $this->getElement($identifier, JBCart::CONFIG_MODIFIER_ITEM_PRICE)) {
            $jbPrice && $element->setPriceElement($jbPrice);
            $itemData && $element->setItemData($itemData);

            // clean memory, hack
            $this->_elements->set(JBCart::CONFIG_MODIFIER_ITEM_PRICE, array());
        }

        return $element;
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

        if (isset($formData[JBCart::ELEMENT_TYPE_SHIPPING])) {
            $errors += $this->_bindShipping($formData[JBCart::ELEMENT_TYPE_SHIPPING]);
        }

        if (isset($formData[JBCart::ELEMENT_TYPE_SHIPPINGFIELD])) {
            $params = $this->app->jbrenderer->create('ShippingFields')->getLayoutParams();
            $errors += $this->_bindElements($formData[JBCart::ELEMENT_TYPE_SHIPPINGFIELD], JBCart::CONFIG_SHIPPINGFIELDS, $params);
        }

        if (isset($formData[JBCart::ELEMENT_TYPE_PAYMENT])) {
            $errors += $this->_bindPayment($formData[JBCart::ELEMENT_TYPE_PAYMENT]);
        }

        if (!isset($formData[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE])) {
            $formData[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE] = array();
        }

        $params = $this->app->jbrenderer->create('ModifierOrderPrice')->getLayoutParams();
        $errors += $this->_bindElements($formData[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE], JBCart::CONFIG_MODIFIER_ORDER_PRICE, $params);

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

            $value = isset($data[$identifier]) ? $data[$identifier] : array();
            $value = $this->app->data->create($value);

            try {

                if (($element = $this->getElement($identifier, $type))) {
                    $params   = $this->app->data->create($elementParam);
                    $elemData = $element->validateSubmission($value, $params);
                    $element->bindData($elemData);
                    $this->params[$type][$identifier] = (array)$element->config;
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

        if (!$this->_config->get('config.freeorder', 0) && $this->getTotalSum()->isEmpty()) {
            $errorMessages[] = 'JBZOO_CART_VALIDATOR_ZERO_SUM';
        }

        $cart = JBCart::getInstance()->checkItems();
        if ($cart->getErrors()) {
            $errorMessages[] = $cart->getErrors();
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

        return $this->app->jbcartstatus->getUndefined();
    }

    /**
     * @return JBCartElementShipping
     */
    public function getShipping()
    {
        if (!$this->id) {
            if ($shippingData = JBCart::getInstance()->getShipping()) {

                $elemId = isset($shippingData['element_id']) ? $shippingData['element_id'] : null;
                if (!$elemId) {
                    $elemId = isset($shippingData['_shipping_id']) ? $shippingData['_shipping_id'] : null;
                }

                if ($shipping = $this->getShippingElement($elemId)) {
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

        return $this->app->jbcartstatus->getUndefined();
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

        $value = array();
        if (isset($data[$elementId])) {
            $value = $data[$elementId];
        }

        $value = $this->app->data->create($value);

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
     * @param array $params
     * @return array
     */
    public function renderItems($params = array())
    {
        $params    = $this->app->data->create($params);
        $items     = $this->getItems(true);
        $editMode  = $params->get('edit', false);
        $emailMode = $params->get('email', false);
        $currency  = $params->get('currency', $this->app->jbrequest->getCurrency());

        /** @type JBHtmlHelper $jbhtml */
        $jbhtml = $this->app->jbhtml;

        $html = array();
        foreach ($items as $cartItem) {
            // get regf to item
            $item    = $cartItem->get('item');
            $itemKey = $cartItem->get('key');

            $quantity = $cartItem->get('quantity', 1);

            // default values
            $itemHtml = array(
                'sku'          => '',
                'image'        => '',
                'imageEmail'   => null,
                'params'       => '',
                'description'  => '',
                // TODO 'margin' => '',
                // TODO 'discount' => '',
                // TODO 'weight' => '',
                'name'         => '<span class="jbcart-item-name ' . $params->find('class.name') . '">'
                    . $cartItem->get('item_name') . '</span>',

                'price4one'    => '<span class="jbcart-item-price4one jsPrice4One jsPrice4One-' . $itemKey . ' ' . $params->find('class.price4one') . '">'
                    . $this->val($cartItem->total)->convert($currency)->html() . '</span>',

                'totalsum'     => '<span class="jbcart-item-totalsum jsSubtotal jsPrice-' . $itemKey . ' ' . $params->find('class.totalsum') . '">'
                    . $this->val($cartItem->total)->multiply($quantity)->convert($currency)->html() . '</span>',

                'quantity'     => '<span class="jbcart-item-quantity ' . $params->find('class.quantity') . '">'
                    . $quantity . ' ' . JText::_('JBZOO_CART_COUNT_ABR') . '</span>',

                'quantityEdit' => '',

                'itemid'       => implode(PHP_EOL, array(
                    '<div class="jbcart-item-itemid ' . $params->find('class.itemid') . '">',
                    '<span class="jbcart-item-itemid-key ' . $params->find('class.itemid-key') . '">' . JText::_('JBZOO_ORDER_ITEM_ID') . ':</span>',
                    '<span class="jbcart-item-itemid-value ' . $params->find('class.itemid-value') . '">' . $cartItem->find('item_id') . '</span>',
                    '</div>',
                )),
            );

            if ($editMode) {
                $itemHtml['quantityEdit'] = $jbhtml->quantity($quantity, $cartItem->find('params._quantity', array()));
            }

            if ($cartItem->find('elements._description')) {
                $itemHtml['description'] = '<div class="jbcart-item-description ' . $params->find('class.description') . '">'
                    . $cartItem->find('elements._description') . '</div>';
            }

            if ($sku = $cartItem->find('elements._sku', $cartItem->get('item_id'))) {
                $itemHtml['sku'] = implode(PHP_EOL, array(
                    '<div class="jbcart-item-sku ' . $params->find('class.sku') . '">',
                    '<span class="jbcart-item-sku-key ' . $params->find('class.sku-key') . '">' . JText::_('JBZOO_CART_ITEM_SKU') . ':</span>',
                    '<span class="jbcart-item-sku-value ' . $params->find('class.sku-value') . '" title="' . $jbhtml->cleanAttrValue($sku) . '">' . $sku . '</span>',
                    '</div>',
                ));
            }

            // render links to item
            $itemUrl = null;
            if ($item) {
                if ((bool)$params->get('admin_url', false) && !$emailMode) {
                    $itemUrl = $this->app->jbrouter->adminItem($item);
                } else {
                    if ($item && $item->isPublished()) {
                        $itemUrl = $this->app->jbrouter->externalItem($item);
                    }
                }
            }

            // render image
            if ($cartItem->find('elements._image')) {
                $image = $this->app->jbimage->resize(
                    $cartItem->find('elements._image'),
                    $params->get('image_width', 75),
                    $params->get('image_height', 75)
                );

                if ($image) {

                    if ($emailMode) {
                        $cid = md5($image->path);
                        if (!$this->app->jbrequest->is('task', 'emailPreview')) {
                            $image->url = 'cid:' . $cid;
                        }

                        $itemHtml['imageEmail'] = array('path' => $image->path, 'cid' => $cid);
                    }

                    $itemHtml['image'] = '<img  ' . $jbhtml->buildAttrs(array(
                            'src'   => $image->url,
                            'class' => 'jbcart-item-image ' . $params->find('class.image') . '',
                            'alt'   => $cartItem->get('item_name'),
                            'title' => $cartItem->get('item_name'),
                        )) . ' />';
                }
            }

            if ($item && $itemUrl) {
                $urlTmpl = '<a ' . $jbhtml->buildAttrs(array(
                        'href'  => $itemUrl,
                        'class' => '%class% jbcart-item-url ' . $params->find('class.url') . '',
                        'title' => $cartItem->get('item_name'),
                    )) . '>%obj%</a>';

                if ((int)$params->get('item_link', 1) && $cartItem->get('item_name')) {
                    $itemHtml['name'] = str_replace(array('%class%', '%obj%'), array('jbcart-item-name', $cartItem->get('item_name')), $urlTmpl);
                }

                if ((int)$params->get('image_link', 1) && $itemHtml['image']) {
                    $itemHtml['image'] = str_replace(array('%class%', '%obj%'), array('jbcart-item-image-url', $itemHtml['image']), $urlTmpl);
                }

            }

            // render param list
            if ($values = (array)$cartItem->get('values')) {
                foreach ($values as $parName => $parValue) {
                    $itemHtml['params'] .= implode(PHP_EOL, array(
                        '<div class="jbcart-item-param ' . $params->find('class.param') . '">',
                        '<span class="jbcart-item-param-key ' . $params->find('class.param-key') . '">' . $parName . ':</span>',
                        '<span class="jbcart-item-param-value ' . $params->find('class.param-value') . '" title="' . $jbhtml->cleanAttrValue($parValue) . '">' . $parValue . '</span>',
                        '</div> ',
                    ));
                }

                $itemHtml['params'] = '<div class="jbcart-item-params ' . $params->find('class.params') . '">' . $itemHtml['params'] . '</div>';
            }

            $html[$cartItem->get('key')] = $itemHtml;
        }

        return $html;
    }

    /**
     * @param string|null $type
     * @param string|null $renderHtml
     * @return null
     */
    public function getUrl($type = null, $renderHtml = null)
    {
        if (is_null($type)) {
            $type = $this->app->jbenv->isSite() ? 'site' : 'admin';
        }

        if ($type == 'admin') {
            $orderUrl = $this->app->jbrouter->orderAdmin($this);
        } else {
            $orderUrl = $this->app->jbrouter->order($this);
        }

        if ($renderHtml && $this->id) {
            $orderUrl = '<a href="' . $orderUrl . '" target="_blank">' . $this->getName($renderHtml) . '</a>';
        }

        return $orderUrl;
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

        $elements = $this->getModifiersOrderPrice();

        if (!empty($elements)) {
            foreach ($elements as $element) {
                $result[$element->identifier] = $element->data();
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

        /** @type JBCartElementPayment $element */
        if ($element = $this->app->jbcartelement->create($config['type'], JBCart::ELEMENT_TYPE_PAYMENT, $config)) {
            $element->bindData($data);
            $element->setOrder($this);
            $element->setStatus($status);
            $element->identifier = $config['identifier'];

            $this->_elements[JBCart::ELEMENT_TYPE_PAYMENT][$element->identifier] = $this->_payment = $element;
        }
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

        /** @type JBCartElementShipping $element */
        if ($element = $this->app->jbcartelement->create($config['type'], JBCart::ELEMENT_TYPE_SHIPPING, $config)) {
            $element->bindData($data);
            $element->setOrder($this);
            $element->setStatus($status);
            $element->identifier = $config['identifier'];

            $this->_elements[JBCart::ELEMENT_TYPE_SHIPPING][$element->identifier] = $this->_shipping = $element;
        }

    }

    /**
     * @param $dataFields
     */
    public function setShippingFieldsData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        foreach ($dataFields as $identifier => $data) {
            if ($element = $this->getShippingFieldElement($identifier)) {
                $element->bindData($data);
                $element->identifier = $identifier;
            }
        }
    }

    /**
     * @param $dataFields
     */
    public function setFieldsData($dataFields)
    {
        $dataFields = $this->app->data->create($dataFields);

        foreach ($dataFields as $identifier => $data) {
            if ($element = $this->getFieldElement($identifier)) {
                $element->bindData($data);
                $element->identifier = $identifier;
            }
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
        foreach ($dataFields as $identifier => $data) {
            if ($element = $this->getModifierOrderPriceElement($identifier)) {
                $element->bindData($data);
                $element->setOrder($this);
                $element->identifier   = $identifier;
                $elements[$identifier] = $element;
            }
        }

        $this->_elements[JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE] = $elements;
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

        if ($currency === null) {
            $currency = $this->params->find('config.migration_currency', null);
        }

        return JBCart::val($data, $currency, $rates);
    }

    /**
     * @param JBCartElementPayment $element
     */
    public function setPaymentElement(JBCartElementPayment $element)
    {
        $this->params[JBCart::CONFIG_PAYMENTS][$element->identifier]    = (array)$element->config;
        $this->_elements[JBCart::CONFIG_PAYMENTS][$element->identifier] = $element;

        $this->_payment = $element;
    }

    /**
     * @param JBCartElementShipping $element
     */
    public function setShippingElement(JBCartElementShipping $element)
    {
        $this->params[JBCart::CONFIG_SHIPPINGS][$element->identifier]    = (array)$element->config;
        $this->_elements[JBCart::CONFIG_SHIPPINGS][$element->identifier] = $element;

        $this->_shipping = $element;
    }

    /**
     * @param JBCartElementOrder $element
     */
    public function addOrderElement(JBCartElementOrder $element)
    {
        $this->params[JBCart::CONFIG_FIELDS][$element->identifier]    = (array)$element->config;
        $this->_elements[JBCart::CONFIG_FIELDS][$element->identifier] = $element;
    }

    /**
     * @param array $statList
     */
    public function setStatusList(array $statList)
    {
        foreach ($statList as $group => $elements) {

            foreach ($elements as $element) {
                $this->params[JBCart::CONFIG_STATUSES][$group][$element->identifier] = (array)$element->config;
            }
        }

    }

}

/**
 * Class JBCartOrderException
 */
class JBCartOrderException extends AppException
{

}