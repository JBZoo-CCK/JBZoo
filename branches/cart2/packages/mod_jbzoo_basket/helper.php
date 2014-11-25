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


require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
require_once(JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');


class JBZooBasketHelper
{
    /**
     * @var JRegistry
     */
    protected $_params = null;

    /**
     * @var Object
     */
    protected $_module = null;

    /**
     * @var App
     */
    public $app = null;

    /**
     * Init Zoo
     * @param JRegistry $params
     * @param object    $module
     */
    public function __construct($params, $module)
    {
        $this->app     = App::getInstance('zoo');
        $this->_params = $params;
        $this->_module = $module;

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
     * @return mixed|string
     */
    public function getWidgetParams()
    {
        return json_encode(array(
            'url_clean'  => $this->getBasketEmptyUrl(),
            'url_reload' => $this->getBasketReloadUrl(),
        ));
    }

    /**
     * Reload module url
     */
    public function getBasketReloadUrl()
    {
        return $this->app->jbrouter->basketReloadModule($this->_module->id);
    }

    /**
     * Get total summ
     * @return int
     */
    public function getSumm()
    {
        $currency = $this->_params->get('currency', 'EUR');
        $items    = $this->getBasketItems();

        $total = JBCart::val();
        foreach ($items as $item) {
            $value = JBCart::val($item['total']);
            $value->multiply($item['quantity']);
            $total->add($value);
        }

        return $total->html($currency);
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
        return JBCart::getInstance()->getItems();
    }

    /**
     * Get basket url
     * @return string
     */
    public function getBasketUrl()
    {
        $menuItemId = $this->_params->get('menuitem');

        return $this->app->jbrouter->basket($menuItemId);
    }

    /**
     * Get basket url for empty
     * @return mixed
     */
    public function getBasketEmptyUrl()
    {
        return $this->app->jbrouter->basketEmpty();
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
