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

/**
 * Class JBZoo
 */
class JBZoo
{
    /**
     * @var Application
     */
    public $app;

    /**
     * App group name
     * @var string
     */
    private $_group = JBZOO_APP_GROUP;

    /**
     * Init JBZoo application
     * @static
     * @return JBZoo
     */
    public static function init()
    {
        static $jbzoo;

        if ($jbzoo === null) {
            $jbzoo = new self();
        }

        return $jbzoo;
    }

    /**
     * Initialization JBZoo App
     */
    private function __construct()
    {
        $this->app = App::getInstance('zoo');

        $this->_initPaths();
        $this->_initClasses();
        $this->_initEvents();
        $this->_initAssets();
    }

    /**
     * Add directory path
     */
    private function _initPaths()
    {
        $this->_addPath('applications:' . $this->_getGroup(), 'jbapp');
        $this->_addPath('jbapp:framework', 'jbzoo');
        $this->_addPath('jbapp:assets', 'jbassets');
        $this->_addPath('jbassets:zoo', 'assets');
        $this->_addPath('jbapp:config', 'jbconfig');
        $this->_addPath('jbzoo:elements', 'jbelements');
        $this->_addPath('jbapp:cart-elements', 'cart-elements');
        $this->_addPath('jbapp:types', 'jbtypes');

        $this->_addPath('jbzoo:helpers', 'helpers');
        $this->_addPath('jbzoo:helpers-std', 'helpers');
        $this->_addPath('helpers:fields', 'fields');

        $this->_addPath('jbzoo:tables', 'tables');
        $this->_addPath('jbzoo:classes-std', 'classes');
        $this->_addPath('jbzoo:render', 'renderer');
        $this->_addPath('jbzoo:views', 'jbviews');
        $this->_addPath('jbapp:config', 'jbxml');
        $this->_addPath('jbviews:', 'partials');
        $this->_addPath('jbapp:joomla/elements', 'fields');
        $this->_addPath('jbapp:templates', 'jbtmpl');

        $this->_addPath('modules:mod_jbzoo_search', 'mod_jbzoo_search');
        $this->_addPath('modules:mod_jbzoo_props', 'mod_jbzoo_props');
        $this->_addPath('modules:mod_jbzoo_basket', 'mod_jbzoo_basket');
        $this->_addPath('modules:mod_jbzoo_category', 'mod_jbzoo_category');
        $this->_addPath('modules:mod_jbzoo_item', 'mod_jbzoo_item');
        $this->_addPath('modules:mod_jbzoo_currency', 'mod_jbzoo_currency');

        $this->_addPath('plugins:/system/jbzoo', 'plugin_jbzoo');

        if ($this->app->jbenv->isSite()) {
            $this->_addPath('jbzoo:controllers', 'controllers');
        }

        require $this->app->path->path('jbzoo:controllers/base.php');

        JLoader::register('AppView', $this->app->path->path('classes:view.php'), true);
    }

    /**
     * Include models classes
     */
    private function _initClasses()
    {
        jimport('joomla.html.parameter.element');

        $this->app->loader->register('AppValidator', 'classes:validator.php');

        // defines
        define('ZOO_TABLE_JBZOO_SKU', '#__zoo_jbzoo_sku');
        define('ZOO_TABLE_JBZOO_FAVORITE', '#__zoo_jbzoo_favorite');
        define('ZOO_TABLE_JBZOO_CONFIG', '#__zoo_jbzoo_config');
        define('ZOO_TABLE_JBZOO_ORDER', '#__zoo_jbzoo_orders');

        require_once dirname(__DIR__) . '/vendor/autoload.php';
    }

    /**
     * Get domain name
     * @param bool $isAny
     * @return string
     */
    static function getDomain($isAny = false)
    {
        if (defined('STDIN') || PHP_SAPI === 'cli') {
            return 'console';
        }

        $domain = '';
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $domain = isset($headers['Host']) ? $headers['Host'] : '';
        }

        if ($isAny && !$domain) {
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        }

        $domain = preg_replace('#^www\.#', '', $domain);
        list($domain) = explode(':', $domain);

        return $domain;
    }

    /**
     * Load lang files
     */
    private function _initLanguages()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_jbzoo', $this->app->path->path('jbapp:'), null, true);
    }

    /**
     * Register and connect events
     */
    private function _initEvents()
    {
        /** @var EventHelper $event */
        /** @var AppEventDispatcher $dispatcher */
        $event = $this->app->event;
        $dispatcher = $event->dispatcher;

        $event->register('JBEventApplication');
        $dispatcher->connect('application:init', ['JBEventApplication', 'init']);
        $dispatcher->connect('application:saved', ['JBEventApplication', 'saved']);
        $dispatcher->connect('application:deleted', ['JBEventApplication', 'deleted']);
        $dispatcher->connect('application:addmenuitems', ['JBEventApplication', 'addmenuitems']);
        $dispatcher->connect('application:installed', ['JBEventApplication', 'installed']);
        $dispatcher->connect('application:configparams', ['JBEventApplication', 'configparams']);
        $dispatcher->connect('application:sefbuildroute', ['JBEventApplication', 'sefbuildroute']);
        $dispatcher->connect('application:sefparseroute', ['JBEventApplication', 'sefparseroute']);
        $dispatcher->connect('application:sh404sef', ['JBEventApplication', 'sh404sef']);

        $event->register('JBEventCategory');
        $dispatcher->connect('category:init', ['JBEventCategory', 'init']);
        $dispatcher->connect('category:saved', ['JBEventCategory', 'saved']);
        $dispatcher->connect('category:deleted', ['JBEventCategory', 'deleted']);
        $dispatcher->connect('category:stateChanged', ['JBEventCategory', 'stateChanged']);

        $event->register('JBEventItem');
        $dispatcher->connect('item:init', ['JBEventItem', 'init']);
        $dispatcher->connect('item:saved', ['JBEventItem', 'saved']);
        $dispatcher->connect('item:deleted', ['JBEventItem', 'deleted']);
        $dispatcher->connect('item:stateChanged', ['JBEventItem', 'stateChanged']);
        $dispatcher->connect('item:beforedisplay', ['JBEventItem', 'beforeDisplay']);
        $dispatcher->connect('item:afterdisplay', ['JBEventItem', 'afterDisplay']);
        $dispatcher->connect('item:orderquery', ['JBEventItem', 'orderQuery']);
        $dispatcher->connect('item:beforeSaveCategoryRelations', ['JBEventItem', 'beforeSaveCategoryRelations']);
        $dispatcher->connect('item:beforeRenderLayout', ['JBEventItem', 'beforeRenderLayout']);
        $dispatcher->connect('item:afterRenderLayout', ['JBEventItem', 'afterRenderLayout']);

        $event->register('JBEventComment');
        $dispatcher->connect('comment:init', ['JBEventComment', 'init']);
        $dispatcher->connect('comment:saved', ['JBEventComment', 'saved']);
        $dispatcher->connect('comment:deleted', ['JBEventComment', 'deleted']);
        $dispatcher->connect('comment:stateChanged', ['JBEventComment', 'stateChanged']);

        $event->register('JBEventSubmission');
        $dispatcher->connect('submission:init', ['JBEventSubmission', 'init']);
        $dispatcher->connect('submission:saved', ['JBEventSubmission', 'saved']);
        $dispatcher->connect('submission:deleted', ['JBEventSubmission', 'deleted']);
        $dispatcher->connect('submission:beforesave', ['JBEventSubmission', 'beforeSave']);

        $event->register('JBEventElement');
        $dispatcher->connect('element:download', ['JBEventElement', 'download']);
        $dispatcher->connect('element:configform', ['JBEventElement', 'configForm']);
        $dispatcher->connect('element:configparams', ['JBEventElement', 'configParams']);
        $dispatcher->connect('element:configxml', ['JBEventElement', 'configXML']);
        $dispatcher->connect('element:afterdisplay', ['JBEventElement', 'afterDisplay']);
        $dispatcher->connect('element:beforedisplay', ['JBEventElement', 'beforeDisplay']);
        $dispatcher->connect('element:aftersubmissiondisplay', ['JBEventElement', 'afterSubmissionDisplay']);
        $dispatcher->connect('element:beforesubmissiondisplay', ['JBEventElement', 'beforeSubmissionDisplay']);
        $dispatcher->connect('element:beforeedit', ['JBEventElement', 'beforeEdit']);
        $dispatcher->connect('element:afteredit', ['JBEventElement', 'afterEdit']);

        $event->register('JBEventLayout');
        $dispatcher->connect('layout:init', ['JBEventLayout', 'init']);

        $event->register('JBEventTag');
        $dispatcher->connect('tag:saved', ['JBEventTag', 'saved']);
        $dispatcher->connect('tag:deleted', ['JBEventTag', 'deleted']);

        $event->register('JBEventType');
        $dispatcher->connect('type:beforesave', ['JBEventType', 'beforesave']);
        $dispatcher->connect('type:aftersave', ['JBEventType', 'aftersave']);
        $dispatcher->connect('type:copied', ['JBEventType', 'copied']);
        $dispatcher->connect('type:deleted', ['JBEventType', 'deleted']);
        $dispatcher->connect('type:editdisplay', ['JBEventType', 'editDisplay']);
        $dispatcher->connect('type:coreconfig', ['JBEventType', 'coreconfig']);
        $dispatcher->connect('type:assignelements', ['JBEventType', 'assignelements']);

        $event->register('JBEventJBZoo');
        $dispatcher->connect('jbzoo:beforeInit', ['JBEventJBZoo', 'beforeInit']);
        $dispatcher->notify($event::create($this, 'jbzoo:beforeInit'));

        $event->register('JBEventBasket');
        $dispatcher->connect('basket:beforeSave', ['JBEventBasket', 'beforeSave']);
        $dispatcher->connect('basket:saved', ['JBEventBasket', 'saved']);
        $dispatcher->connect('basket:afterSave', ['JBEventBasket', 'afterSave']);
        $dispatcher->connect('basket:updated', ['JBEventBasket', 'updated']);

        $dispatcher->connect('basket:addItem', ['JBEventBasket', 'addItem']);
        $dispatcher->connect('basket:updateItem', ['JBEventBasket', 'updateItem']);
        $dispatcher->connect('basket:changeQuantity', ['JBEventBasket', 'changeQuantity']);
        $dispatcher->connect('basket:removeItem', ['JBEventBasket', 'removeItem']);
        $dispatcher->connect('basket:removeItems', ['JBEventBasket', 'removeItems']);
        $dispatcher->connect('basket:recount', ['JBEventBasket', 'recount']);

        $dispatcher->connect('basket:orderStatus', ['JBEventBasket', 'orderStatus']);
        $dispatcher->connect('basket:paymentStatus', ['JBEventBasket', 'paymentStatus']);
        $dispatcher->connect('basket:shippingStatus', ['JBEventBasket', 'shippingStatus']);

        $dispatcher->connect('basket:paymentSuccess', ['JBEventBasket', 'paymentSuccess']);
        $dispatcher->connect('basket:paymentFail', ['JBEventBasket', 'paymentFail']);
        $dispatcher->connect('basket:paymentCallback', ['JBEventBasket', 'paymentCallback']);
    }

    /**
     * Init assets for admin path
     */
    private function _initAssets()
    {
        if (!$this->app->jbenv->isSite() && !$this->app->jbrequest->isAjax()) {
            $this->app->jbassets->admin();
            $this->_initLanguages();
            $this->_initAdminMenu();
        }
    }

    /**
     * Init Admin menu
     */
    private function _initAdminMenu()
    {
        $isNotJ35 = version_compare(JVERSION, '3.6', '<=');

        if ($isNotJ35) {
            $this->app->jbassets->addVar('JBAdminItems', [
                'name'  => JText::_('JBZOO_ADMINMENU_CAPTION'),
                'items' => $this->getAdminMenu(),
            ]);
        }
    }

    /**
     * Init Admin menu
     */
    public function getAdminMenu()
    {
        $config = JBModelConfig::model()->getGroup('config.custom', $this->app->jbconfig->getList());
        if (!$config->get('adminmenu_show', 1)) {
            return false;
        }

        $curApp = $this->app->system->application->getUserState('com_zooapplication', 0);

        $appList = JBModelApp::model()->getSimpleList();

        $findJBZooApp = false;
        $dispatched = false;
        foreach ($appList as $app) {
            if ($app->application_group === JBZOO_APP_GROUP) {
                $findJBZooApp = true;
                if ($curApp === $app->id) {
                    $dispatched = true;
                }
            }
        }

        if (!$findJBZooApp) {
            return false;
        }

        /** @var JBRouterHelper $router */
        $router = $this->app->jbrouter;

        $menuItems = [];

        if (!empty($appList)) {
            foreach ($appList as $app) {

                $menuItems['app-' . $app->alias] = [
                    'name'     => $app->name,
                    'url'      => $router->admin(['changeapp' => $app->id, 'controller' => 'item']),
                    'children' => [
                        'add-item'   => [
                            'name' => JText::_('JBZOO_ADMINMENU_ADD_ITEM'),
                            'url'  => $router->admin([
                                'changeapp'  => $app->id,
                                'controller' => 'item',
                                'task'       => 'add'
                            ]),
                        ],
                        'sep-1'      => 'divider',
                        'items'      => [
                            'name' => JText::_('JBZOO_ADMINMENU_ITEMS'),
                            'url'  => $router->admin(['changeapp' => $app->id, 'controller' => 'item', 'task' => '']),
                        ],
                        'categories' => [
                            'name' => JText::_('JBZOO_ADMINMENU_CATEGORIES'),
                            'url'  => $router->admin([
                                'changeapp'  => $app->id,
                                'controller' => 'category',
                                'task'       => ''
                            ]),
                        ],
                        'frontpage'  => [
                            'name' => JText::_('JBZOO_ADMINMENU_FRONTPAGE'),
                            'url'  => $router->admin([
                                'changeapp'  => $app->id,
                                'controller' => 'frontpage',
                                'task'       => ''
                            ]),
                        ],
                        'comments'   => [
                            'name' => JText::_('JBZOO_ADMINMENU_COMMENTS'),
                            'url'  => $router->admin([
                                'changeapp'  => $app->id,
                                'controller' => 'comment',
                                'task'       => ''
                            ]),
                        ],
                        'sep-2'      => 'divider',
                        'config'     => [
                            'name' => JText::_('JBZOO_ADMINMENU_CONFIG'),
                            'url'  => $router->admin([
                                'changeapp'  => $app->id,
                                'controller' => 'configuration',
                                'task'       => ''
                            ]),
                        ],
                    ],
                ];
            }
        }

        $menuItems['sep-1'] = 'divider';

        if ($dispatched) {
            if ((int)JBModelConfig::model()->get('enable', 1, 'cart.config')) {
                $menuItems['orders'] = [
                    'name'     => JText::_('JBZOO_ADMINMENU_ORDERS'),
                    'url'      => $this->app->jbrouter->admin(['task' => 'index', 'controller' => 'jborder']),
                    'children' => [
                        'cart-configs' => [
                            'name' => JText::_('JBZOO_ADMINMENU_CART_CONFIG'),
                            'url'  => $this->app->jbrouter->admin(['task' => 'index', 'controller' => 'jbcart']),
                        ],
                    ],
                ];

                $menuItems['sep-2'] = 'divider';
            }
        }

        $menuItems['item-config'] = [
            'name' => JText::_('JBZOO_ADMINMENU_MAINCONFIG'),
            'url'  => $this->app->jbrouter->admin([
                'task'       => 'types',
                'group'      => 'jbuniversal',
                'controller' => 'manager'
            ]),
        ];

        $types = $this->app->jbtype->getSimpleList();
        if (!empty($types)) {
            $children = [];
            foreach ($types as $alias => $type) {
                $children['type-' . $alias] = [
                    'name' => $type,
                    'url'  => $router->admin([
                        'controller' => 'manager',
                        'group'      => 'jbuniversal',
                        'task'       => 'editelements',
                        'cid'        => ['0' => $alias]
                    ]),
                ];
            }

            $menuItems['item-config']['children'] = $children;
        }

        if ($dispatched) {
            $menuItems['sep-3'] = 'divider';

            $menuItems['jbzoo-admin'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_JBZOOPAGE'),
                'url'      => $router->admin(['controller' => 'jbindex', 'task' => 'index']),
                'children' => [
                    'performance'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_TOOLS'),
                        'url'  => $router->admin(['controller' => 'jbtools', 'task' => 'index']),
                    ],
                    'systemreport' => [
                        'name' => JText::_('JBZOO_ADMINMENU_CONFIGS'),
                        'url'  => $router->admin(['controller' => 'jbconfig', 'task' => 'index']),
                    ],
                ],
            ];

            $menuItems['jbzoo-import'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_IMPORT'),
                'url'      => $router->admin(['controller' => 'jbimport', 'task' => 'index']),
                'children' => [
                    'items'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_ITEMS'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'items']),
                    ],
                    'categories' => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_CATEGORIES'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'categories']),
                    ],
                    'stdandart'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_STANDARD'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'standart']),
                    ],
                ],
            ];

            $menuItems['jbzoo-export'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_EXPORT'),
                'url'      => $router->admin(['controller' => 'jbexport', 'task' => 'index']),
                'children' => [
                    'items'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ITEMS'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'items']),
                    ],
                    'categories' => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_CATEGORIES'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'categories']),
                    ],
                    'types'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_TYPES'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'types']),
                    ],
                    'yandexyml'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_YANDEXYML'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'yandexyml']),
                    ],
                    'stdandart'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_STANDARD'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'standart']),
                    ],
                    'zoobackup'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ZOOBACKUP'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'zoobackup']),
                    ],
                ],
            ];

            $menuItems['sep-4'] = 'divider';

            $menuItems['jbzoo-info'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_INFO'),
                'url'      => $router->admin(['controller' => 'jbinfo', 'task' => 'index']),
                'children' => [
                    'performance'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_PERFORMANCE'),
                        'url'  => $router->admin(['controller' => 'jbinfo', 'task' => 'performance']),
                    ],
                    'systemreport' => [
                        'name' => JText::_('JBZOO_ADMINMENU_SYSTEMREPORT'),
                        'url'  => $router->admin(['controller' => 'jbinfo', 'task' => 'systemreport']),
                    ],
                ],
            ];
        }

        $menuItems['jbzoo-support'] = [
            'name'   => JText::_('JBZOO_ADMINMENU_SUPPORT'),
            'url'    => 'http://forum.jbzoo.com/',
            'target' => '_blank',
        ];

        return $menuItems;
    }

    /**
     * Get group name
     * @return string
     */
    private function _getGroup()
    {
        return $this->_group;
    }

    /**
     * Register new path in system
     * @param string $path
     * @param string $pathName
     * @return mixed
     */
    private function _addPath($path, $pathName)
    {
        if ($fullPath = $this->app->path->path($path)) {
            return $this->app->path->register($fullPath, $pathName);
        }

        return null;
    }
}
