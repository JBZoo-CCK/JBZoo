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

/**
 * Class JBZooBasketHelper
 */
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
     * @var JBCartOrder
     */
    protected $_order = null;

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

        $this->_order = JBCart::getInstance()->newOrder();
    }

    /**
     * @return JBCartOrder
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return JBCartOrder
     */
    public function getBasketItems($params = array())
    {
        $_params = array(
            // TODO config from module
            'currency'     => $this->getCurrency(),
            'image_width'  => $this->_params->get('jbcart_item_image_width', 75),
            'image_height' => $this->_params->get('jbcart_item_image_height', 75),
        );

        $params = array_replace_recursive($_params, $params);

        return $this->_order->renderItems($params);
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        $currencyDef = $this->_params->get('currency', 'eur');
        $currencyCur = $this->app->jbrequest->getCurrency($currencyDef);
        return $currencyCur;
    }

    /**
     * Get basket count
     * @return int
     */
    public function getCountSku()
    {
        return count($this->_order->getItems());
    }

    /**
     * @return string
     */
    public function getWidgetParams()
    {
        return array(
            'url_clean'           => $this->app->jbrouter->basketEmpty(),
            'url_reload'          => $this->app->jbrouter->basketReloadModule($this->_module->id),
            'url_item_remove'     => $this->app->jbrouter->basketDelete(),
            'text_delete_confirm' => JText::_('JBZOO_CART_MODULE_DELETE_CONFIRM'),
            'text_empty_confirm'  => JText::_('JBZOO_CART_MODULE_EMPTY_CONFIRM'),
        );
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

}
