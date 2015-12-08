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
 * Class JBRouterHelper
 */
class JBRouterHelper extends AppHelper
{
    /**
     * @var JBRequestHelper
     */
    protected $_jbrequest = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_jbrequest = $this->app->jbrequest;
    }


    /**
     * Filter link
     * @param string    $elemId
     * @param string    $value
     * @param JRegistry $moduleParams
     * @param int       $mode
     * @return string
     */
    public function filter($elemId, $value, $moduleParams, $mode = 0)
    {
        $urlParams = array(
            'option'     => 'com_zoo',
            'controller' => 'search',
            'task'       => 'filter',
            'app_id'     => $moduleParams->get('application'),
            'Itemid'     => $moduleParams->get('menuitem'),
            'type'       => $moduleParams->get('type'),
            'limit'      => $moduleParams->get('limit', 10),
            'exact'      => 1,
            'order'      => $moduleParams->get('order'),
        );

        if ($mode == 0) {
            $urlParams['e'][$elemId] = $value;

        } elseif ($mode == 1) {
            $urlParams['e']          = $this->_jbrequest->getElements();
            $urlParams['e'][$elemId] = $value;

        } elseif ($mode == 2) {
            $urlParams['e'] = $this->_jbrequest->getElements();
            unset($urlParams['e'][$elemId]);
        }

        if ($moduleParams->get('depend_category', 0)) {
            $categoryId = $this->_jbrequest->getSystem('category');
            if ($categoryId > 0 && !isset($urlParams['e']['_itemcategory'])) {
                $urlParams['e']['_itemcategory'] = $categoryId;
            }
        }

        return $this->_url($urlParams);
    }

    /**
     * Autocomplete link
     * @param array $params
     * @return string
     */
    public function autocomplete(array $params = array())
    {
        $urlParams = array(
            'option'     => 'com_zoo',
            'controller' => 'autocomplete',
            'task'       => 'index',
            'tmpl'       => 'raw',
        );

        $urlParams = array_merge($urlParams, $params);

        return JURI::root() . 'index.php?' . $this->query($urlParams);
    }

    /**
     * Element ajax call
     * @param string $identifier
     * @param int    $itemId
     * @param string $method
     * @param array  $params
     * @return string
     */
    public function element($identifier = null, $itemId = null, $method = 'ajax', array $params = array())
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'default',
            'task'       => 'callelement',
            //'format'     => 'raw',
            'element'    => $identifier,
            'method'     => $method,
            'item_id'    => $itemId,
        );

        if (!empty($params)) {
            $linkParams['args'] = $params;
        }

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Element ajax call
     * @param string $method
     * @param array  $urlParams
     * @param array  $params
     * @return string
     */
    public function elementOrder($method = 'ajax', array $urlParams = array(), array $params = array())
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'callelement',
            'format'     => 'raw',
            'element'    => $urlParams['element'],
            'group'      => $urlParams['group'],
            'order_id'   => $urlParams['order_id'],
            'method'     => $method,
        );

        if (!empty($params)) {
            $linkParams['args'] = $params;
        }

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Element ajax call (for admin)
     * @param string $identifier
     * @param int    $itemId
     * @param string $method
     * @param array  $params
     * @return string
     */
    public function elementAdmin($identifier = null, $itemId = null, $method = 'ajax', array $params = array())
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'item',
            'task'       => 'callelement',
            'format'     => 'raw',
            'element'    => $identifier,
            'elm_id'     => $identifier,
            'method'     => $method,
            'item_id'    => $itemId,
        );

        if (!empty($params)) {
            $linkParams['args'] = $params;
        }

        return $this->_url($linkParams, true);
    }

    /**
     * TODO Delete this method
     * Element ajax call (for admin)
     * @param string $identifier
     * @param string $controller
     * @param string $layout
     * @param string $method
     * @param array  $params
     * @return string
     */
    public function elementAdminOrder($identifier = null, $method = 'ajax', $layout = null, $controller = 'jbcart', array $params = array())
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'layout'     => $layout,
            'controller' => $controller,
            'task'       => 'callelement',
            'format'     => 'raw',
            'element'    => $identifier,
            'elm_id'     => $identifier,
            'method'     => $method,
        );

        if (!empty($params)) {
            $linkParams['args'] = $params;
        }

        return $this->_url($linkParams, true);
    }

    /**
     * Compare link
     * @param int    $menuItemid
     * @param string $layout
     * @param string $itemType
     * @param int    $appId
     * @return string
     */
    public function compare($menuItemid, $layout = 'v', $itemType = null, $appId = null)
    {
        $itemType = ($itemType) ? $itemType : $this->_jbrequest->get('type');
        $appId    = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'compare',
            'task'       => 'compare',
            'app_id'     => (int)$appId,
            'type'       => $itemType,
            'layout'     => $layout,
            'Itemid'     => (int)$menuItemid,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Favorite link
     * @param int $menuItemid
     * @param int $appId
     * @return string
     */
    public function favorite($menuItemid, $appId = null)
    {
        $appId = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'favorite',
            'task'       => 'favorite',
            'app_id'     => (int)$appId,
            'Itemid'     => (int)$menuItemid,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Favorite link (remove)
     * @return string
     */
    public function favoriteClear()
    {
        $appId  = (int)$this->_jbrequest->get('app_id');
        $Itemid = (int)$this->_jbrequest->get('Itemid');

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'favorite',
            'task'       => 'removeAll',
            'app_id'     => (int)$appId,
            'Itemid'     => (int)$Itemid,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Favorite remove item link
     * @param int $itemId
     * @param int $appId
     * @return string
     */
    public function favoriteRemoveItem($itemId, $appId = null)
    {
        $appId = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'favorite',
            'task'       => 'remove',
            'app_id'     => (int)$appId,
            'item_id'    => (int)$itemId,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Url for clear compare items
     * @param      $menuItemid
     * @param null $itemType
     * @param null $appId
     * @return string
     */
    public function compareClear($menuItemid, $itemType = null, $appId = null)
    {
        $itemType = ($itemType) ? $itemType : $this->_jbrequest->get('type');
        $appId    = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');

        $linkParams = array(
            'option'      => 'com_zoo',
            'controller'  => 'compare',
            'task'        => 'clear',
            'app_id'      => (int)$appId,
            'type'        => $itemType,
            'Itemid'      => (int)$menuItemid,
            'back_itemid' => (int)$menuItemid,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to basket
     * @param int $menuItemid
     * @return string
     */
    public function basket($menuItemid = 0)
    {
        if (empty($menuItemid)) {
            $menuItemid = JBModelConfig::model()->getGroup('cart.config')->get('menuitem', 101);
        }

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'index',
            'Itemid'     => $menuItemid,
            'nc'         => rand(1000, 9999), // forced browser no cache
        );

        return JURI::root() . 'index.php?' . $this->query($linkParams);
    }

    /**
     * Basket empty url
     * @return string
     */
    public function basketEmpty()
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'clear',
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Basket reload module url
     * @return string
     */
    public function basketReloadModule($moduleId)
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'reloadModule',
            'moduleId'   => $moduleId,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to success order
     * @param int  $menuItemid
     * @param null $appId
     * @return string
     */
    public function basketSuccess($menuItemid, $appId = null)
    {
        $appId = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');

        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'index',
            'app_id'     => (int)$appId,
            'Itemid'     => (int)$menuItemid,
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to success order
     * @param $menuItemid
     * @param $appId
     * @param $itemId
     * @return string
     */
    public function basketPayment($menuItemid, $appId, $itemId)
    {
        $appId      = ($appId) ? $appId : (int)$this->_jbrequest->get('app_id');
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'payment',
            'task'       => 'index',
            'app_id'     => (int)$appId,
            'Itemid'     => (int)$menuItemid,
            'order_id'   => $itemId,
        );

        $url  = $this->_url($linkParams, true);
        $base = $this->getHostUrl();

        return $base . $url;
    }

    /**
     * Get url to basket
     * @return string
     */
    public function basketDelete()
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'delete',
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to basket clear action
     * @return string
     */
    public function basketClear()
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'clear',
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to basket quantity action
     * @return string
     */
    public function basketQuantity()
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'quantity',
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get url to basket shipping action
     * @return string
     */
    public function basketShipping()
    {
        $linkParams = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'shipping',
        );

        return $this->_url($linkParams, false, JURI::root());
    }

    /**
     * Get item url for back-end
     * @param Item $item
     * @return string
     */
    public function adminItem($item)
    {
        if (empty($item)) {
            return null;
        }

        $linkParams = array(
            'option'     => $this->app->component->self->name,
            'controller' => 'item',
            'changeapp'  => $item->application_id,
            'task'       => 'edit',
            'cid[]'      => $item->id,
        );

        return $this->_url($linkParams, true, JURI::root() . 'administrator/index.php');
    }

    /**
     * Link to auth
     * @param null $return
     * @return string
     */
    public function auth($return = null)
    {
        $linkParams = array(
            'option' => 'com_users',
            'task'   => 'login',
            'return' => $return,
        );

        return $this->_url($linkParams, true);
    }

    /**
     * Generate admin menu
     * @param array $params
     * @return string
     */
    public function admin(array $params = array())
    {
        if (!isset($params['controller'])) {
            $params['controller'] = $this->_jbrequest->getCtrl();
        }

        $task = $this->_jbrequest->getWord('task');
        if (!isset($params['task']) && !empty($task)) {
            $params['task'] = $task;
        }

        if (!isset($params['option'])) {
            $params['option'] = 'com_zoo';
        }

        return $this->_url($params, true, JURI::root() . 'administrator/index.php');
    }

    /**
     * Payment
     * @param $type
     * @return string
     */
    public function payment($type)
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'payment',
            'task'       => 'payment' . ucfirst($type),
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * Payment page for not paid orders
     * @param $Itemid
     * @param $appId
     * @param $orderId
     * @return string
     */
    public function paymentNotPaid($Itemid, $appId, $orderId)
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'payment',
            'task'       => 'paymentNotPaid',
            'app_id'     => (int)$appId,
            'Itemid'     => $Itemid,
            'order_id'   => $orderId
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * @param $Itemid
     * @return string
     */
    public function cartOrderCreate($Itemid = null)
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'index',
            'Itemid'     => $Itemid,
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * @param JBCartOrder $order
     * @return string
     */
    public function order($order)
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'clientarea',
            'task'       => 'order',
            'order_id'   => $order->id,
            'Itemid'     => $this->app->jbrequest->get('Itemid'),
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * @param JBCartOrder $order
     * @return string
     */
    public function orderAdmin($order)
    {
        return $this->admin(array(
            'controller' => 'jborder',
            'task'       => 'edit',
            'cid[]'      => $order->id
        ));
    }

    /**
     * @param $menuItemid
     * @return string
     */
    public function orders($menuItemid)
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'clientarea',
            'task'       => 'orders',
            'Itemid'     => $menuItemid,
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * @return string
     */
    public function removeViewed()
    {
        $params = array(
            'option'     => 'com_zoo',
            'controller' => 'viewed',
            'task'       => 'clear',
            'format'     => 'raw',
            'app_id'     => $this->app->zoo->getApplication()->id,
        );

        return JURI::root() . 'index.php?' . $this->query($params);
    }

    /**
     * Get url by params
     * @param array  $params
     * @param bool   $zooRoute
     * @param string $base
     * @return string
     */
    private function _url(array $params = array(), $zooRoute = false, $base = 'index.php')
    {
        foreach ($params as $key => $param) {
            if (is_null($param)) {
                unset($params[$key]);
            }
        }

        if ($zooRoute) {
            return $this->app->link($params, false);
        } else {
            return JRoute::_($base . '?' . $this->query($params), true);
        }
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function externalItem(Item $item)
    {
        if ($this->app->jbenv->isSite()) {
            return JRoute::_($this->app->route->item($item, false), false, 2);

        } else {
            $root        = JUri::root();
            $application = JApplication::getInstance('site');
            $router      = $application->getRouter();
            $link        = $router->build($this->app->route->item($item, false));

            return $root . preg_replace('/^.*administrator\//', '', $link, 1);
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function query(array $data)
    {
        return http_build_query($data, null, '&');
    }

    /**
     * Add params to custom URL
     * @param       $url
     * @param array $params
     * @return string
     */
    public function addParamsToUrl($url, array $params)
    {
        $add = $this->query($params);

        if (strpos($url, '?') === false) {
            return $url . '?' . $add;
        }

        return $url . '&' . $add;
    }

    /**
     * Get root host
     * @return string
     */
    public function getHostUrl()
    {
        return JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
    }
}
