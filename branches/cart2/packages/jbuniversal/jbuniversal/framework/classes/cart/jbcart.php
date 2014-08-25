<?php

/**
 * Class JBCart
 */
class JBCart
{
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
        $items = $this->app->jbsession->get('items', $this->_sessionNamespace);

        $items = array(
            array(
                "sku"         => "SKU98100",
                "itemId"      => "90",
                "quantity"    => 2,
                "price"       => 10,
                "currency"    => "EUR",
                "priceDesc"   => "Red color",
                "priceParams" => array(),
                "name"        => "Acer Aspire Z1811 "
            ),
            array(
                "sku"         => "SKU98100",
                "itemId"      => "90",
                "quantity"    => 2,
                "price"       => 10,
                "currency"    => "EUR",
                "priceDesc"   => "Red color",
                "priceParams" => array(),
                "name"        => "Acer Aspire Z1811 "
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
