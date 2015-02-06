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
    public function getBasketItems()
    {
        return $this->_order->renderItems(array(
            // TODO config from module
            'currency' => $this->_params->get('currency'),
        ));
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
            'url_clean'       => $this->app->jbrouter->basketEmpty(),
            'url_reload'      => $this->app->jbrouter->basketReloadModule($this->_module->id),
            'url_item_remove' => $this->app->jbrouter->basketDelete(),
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
