<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/classes/jbmodulehelper.php'; // TODO move to bootstrap

/**
 * Class JBModuleHelperBasket
 */
class JBModuleHelperBasket extends JBModuleHelper
{
    /**
     * @type JBCartOrder
     */
    protected $_order = null;

    /**
     * Init Zoo
     * @param JRegistry $params
     * @param object    $module
     */
    public function __construct(JRegistry $params, $module)
    {
        parent::__construct($params, $module);

        $this->_order = JBCart::getInstance()->newOrder();
    }

    /**
     * Load important assets files
     */
    protected function _loadAssets()
    {
        parent::_loadAssets();
        $this->_jbassets->js('mod_jbzoo_basket:assets/js/cart-module.js');
        $this->_jbassets->less('mod_jbzoo_basket:assets/less/cart-module.less');
    }

    /**
     * Init cart widget
     */
    protected function _initWidget()
    {
        $this->_jbassets->widget('#' . $this->getModuleId(), 'JBZoo.CartModule', $this->getWidgetParams());
    }

    /**
     * @param bool $addNoindex
     * @return string
     */
    public function render($addNoindex = true)
    {
        if (!JBCart::getInstance()->canAccess($this->app->user->get())) {
            return JText::_('JBZOO_CART_UNABLE_ACCESS');
        }

        return parent::render($addNoindex);
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
    public function getBasketItems($params = [])
    {
        $_params = [
            // TODO config from module
            'currency'     => $this->getCurrency(),
            'item_link'    => (int)$this->_params->get('jbcart_item_link', 1),
            'image_width'  => $this->_params->get('jbcart_item_image_width', 75),
            'image_height' => $this->_params->get('jbcart_item_image_height', 75),
            'image_link'   => (int)$this->_params->get('jbcart_item_image_link', 1),
        ];

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
        return [
            'url_clean'           => $this->app->jbrouter->basketEmpty(),
            'url_reload'          => $this->app->jbrouter->basketReloadModule($this->_module->id),
            'url_item_remove'     => $this->app->jbrouter->basketDelete(),
            'text_delete_confirm' => JText::_('JBZOO_CART_MODULE_DELETE_CONFIRM'),
            'text_empty_confirm'  => JText::_('JBZOO_CART_MODULE_EMPTY_CONFIRM'),
        ];
    }

    /**
     * Get basket url
     * @return string
     */
    public function getBasketUrl()
    {
        return $this->app->jbrouter->basket();
    }
}
