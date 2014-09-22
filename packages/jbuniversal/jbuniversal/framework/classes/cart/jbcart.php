<?php

/**
 * Class JBCart
 */
class JBCart
{
    const DEFAULT_POSITION = 'list';

    const NOTIFY_ORDER_CREATE  = 'order_create';
    const NOTIFY_ORDER_EDIT    = 'order_edit';
    const NOTIFY_ORDER_STATUS  = 'order_status';
    const NOTIFY_ORDER_PAYMENT = 'order_payment';

    const MODIFIER_ORDER = 'modifier_order';
    const MODIFIER_ITEM  = 'modifier_item';

    const CONFIG_NOTIFICATION      = 'notification';
    const CONFIG_MODIFIERS         = 'modifier';
    const CONFIG_VALIDATORS        = 'validator';
    const CONFIG_PAYMENTS          = 'payment';
    const CONFIG_SHIPPINGS         = 'shipping';
    const CONFIG_STATUS_EVENTS     = 'status-events';
    const CONFIG_CURRENCIES        = 'currency';
    const CONFIG_STATUSES          = 'status';
    const CONFIG_EMAIL_TMPL        = 'email_tmpl';
    const CONFIG_SHIPPINGFIELDS    = 'shippingfield';
    const CONFIG_FIELDS            = 'field';
    const CONFIG_FIELDS_TMPL       = 'field_tmpl';
    const CONFIG_PRICE             = 'price';
    const CONFIG_PRICE_TMPL        = 'price_tmpl';
    const CONFIG_PRICE_TMPL_FILTER = 'price_tmpl_filter';

    const ELEMENT_TYPE_DEFAULT       = 'element';
    const ELEMENT_TYPE_CURRENCY      = 'currency';
    const ELEMENT_TYPE_SHIPPING      = 'shipping';
    const ELEMENT_TYPE_SHIPPINGFIELD = 'shippingfield';
    const ELEMENT_TYPE_MODIFIERITEM  = 'modifieritem';
    const ELEMENT_TYPE_MODIFIERPRICE = 'modifierprice';
    const ELEMENT_TYPE_MODIFIERS     = 'modifier';
    const ELEMENT_TYPE_NOTIFICATION  = 'notification';
    const ELEMENT_TYPE_ORDER         = 'order';
    const ELEMENT_TYPE_PAYMENT       = 'payment';
    const ELEMENT_TYPE_PRICE         = 'price';
    const ELEMENT_TYPE_STATUS        = 'status';
    const ELEMENT_TYPE_VALIDATOR     = 'validator';

    /**
     * @var string
     */
    protected $_sessionNamespace = 'jbcart';

    /**
     * @var App
     */
    public $app = null;

    /**
     * @var JSONData
     */
    protected $_config = null;

    /**
     * @return JBCart
     */
    public static function getInstance()
    {
        static $instance;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Constructor
     */
    function __construct()
    {
        $this->app = App::getInstance('zoo');

        $this->_config = JBModelConfig::model()->getGroup('cart');
    }

    /**
     * @return array
     */
    public function getItems()
    {
        //$items = $this->app->jbsession->get('items', $this->_sessionNamespace);

        $items = array(
            array(
                "sku"         => "SKU98100",
                "itemId"      => "90",
                "quantity"    => 2,
                "price"       => 10,
                "currency"    => "EUR",
                "priceDesc"   => "Red color",
                "priceParams" => array(),
                "name"        => "Acer Aspire Z1811 ",
                'params'      => array(
                    'weight' => 0,
                    'height' => 0,
                    'length' => 0,
                    'width'  => 0,
                ),
            ),
            array(
                "sku"         => "SKU98100",
                "itemId"      => "90",
                "quantity"    => 2,
                "price"       => 10,
                "currency"    => "EUR",
                "priceDesc"   => "Red color",
                "priceParams" => array(),
                "name"        => "Acer Aspire Z1811 ",
                'params'      => array(
                    'weight' => 0,
                    'height' => 0,
                    'length' => 0,
                    'width'  => 0,
                ),
            ),
        );

        $items = $this->app->data->create($items);

        return $items;
    }

    /**
     * @return JBCartOrder
     */
    public function newOrder()
    {
        $order = new JBCartOrder();

        $order->id         = 0;
        $order->created    = $this->app->jbdate->toMySql();
        $order->created_by = (int)JFactory::getUser()->id;

        if ($status = $this->getDefaultStatus()) {
            $order->setStatus($status->getCode());
        }

        return $order;
    }

    /**
     *
     */
    public function removeItems()
    {
        $this->app->jbsession->set('items', array(), $this->_sessionNamespace);
    }

    /**
     *
     */
    public function getDefaultStatus()
    {
        $statusCode = $this->_config->get('config.default_status');
        if ($statusCode) {
            return $this->app->jbcartstatus->getByCode($statusCode);
        }

        return null;
    }

}
