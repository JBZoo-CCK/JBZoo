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


require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
require_once(JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');


class JBZooBasketHelper
{
    /**
     * @var JRegistry
     */
    protected $_params = null;

    /**
     * @var App
     */
    protected $app = null;

    /**
     * Init Zoo
     * @param JRegistry $params
     */
    public function __construct(JRegistry $params)
    {
        $this->app     = App::getInstance('zoo');
        $this->_params = $params;

        JBZoo::init();
    }

    /**
     * Get basket count
     * @return int
     */
    public function getCountSku()
    {
        $basketItems = $this->getBasketItems();

        return count($basketItems);
    }

    /**
     * Get total summ
     * @return int
     */
    public function getSumm()
    {
        $currency    = $this->_params->get('currency', 'EUR');
        $basketItems = $this->getBasketItems();

        $summ = 0;
        foreach ($basketItems as $basketItem) {
            $priceValue = $basketItem['quantity'] * $basketItem['price'];
            $summ += $this->app->jbmoney->convert($basketItem['currency'], $currency, $priceValue);
        }

        return $summ;
    }

    /**
     * Get currency
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_params->get('currency', 'EUR');
    }

    /**
     * Get basket items
     * @return mixed
     */
    public function getBasketItems()
    {
        $basketItems = $this->app->jbcart->getAllItems();

        return $basketItems;
    }

    /**
     * Get basket url
     * @return string
     */
    public function getBasketUrl()
    {
        $appId      = $this->_params->get('app_id');
        $menuItemId = $this->_params->get('menuitem');

        return $this->app->jbrouter->basket($menuItemId, $appId);
    }

    /**
     * Get basket url for empty
     * @return mixed
     */
    public function getBasketEmptyUrl()
    {
        $appId = $this->_params->get('app_id');

        return $this->app->jbrouter->basketEmpty($appId);
    }

    /**
     * Get application id
     * @return mixed
     */
    public function getAppId()
    {
        return (int)$this->_params->get('app_id');
    }

    /**
     * Get count SKU
     * @return int
     */
    public function getCount()
    {
        $basketItems = $this->getBasketItems();

        $count = 0;
        foreach ($basketItems as $basketItem) {
            $count += $basketItem['quantity'];
        }

        return $count;
    }
}
