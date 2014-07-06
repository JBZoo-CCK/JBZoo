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


!defined('JBZOO_APP_GROUP') && define('JBZOO_APP_GROUP', 'jbuniversal');
!defined('DIRECTORY_SEPERATOR') && define('DIRECTORY_SEPERATOR', '/');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);

/**
 * Class JBZoo
 */
class JBZoo
{
    /**
     * @var Application
     */
    public $app = null;

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

        if (!isset($jbzoo)) {
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
        $this->_initConfig();
        $this->_initModels();
        $this->_initLanguages();
        $this->_initFilterElements();
        $this->_initEvents();
        $this->_initAssets();
        $this->_checkTmpDirectory();
        $this->_checkCacheDirectory();
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
    private function _initModels()
    {
        // defines
        define('ZOO_TABLE_JBZOO_SKU', '#__zoo_jbzoo_sku');
        define('ZOO_TABLE_JBZOO_FAVORITE', '#__zoo_jbzoo_favorite');
        define('ZOO_TABLE_JBZOO_CONFIG', '#__zoo_jbzoo_config');

        // query builder
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/classes';
        require $path . '/database/JBDatabaseQuery.php';
        require $path . '/database/JBDatabaseQueryElement.php';

        // models
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/models';
        require $path . '/jbmodel.php';
        require $path . '/jbmodel.config.php';
        require $path . '/jbmodel.element.php';
        require $path . '/jbmodel.autocomplete.php';
        require $path . '/jbmodel.element.country.php';
        require $path . '/jbmodel.element.itemdate.php';
        require $path . '/jbmodel.element.itemauthor.php';
        require $path . '/jbmodel.element.itemcategory.php';
        require $path . '/jbmodel.element.itemcreated.php';
        require $path . '/jbmodel.element.itemfrontpage.php';
        require $path . '/jbmodel.element.itemmodified.php';
        require $path . '/jbmodel.element.itemname.php';
        require $path . '/jbmodel.element.itempublish_down.php';
        require $path . '/jbmodel.element.itempublish_up.php';
        require $path . '/jbmodel.element.itemtag.php';
        require $path . '/jbmodel.element.jbimage.php';
        require $path . '/jbmodel.element.jbselectcascade.php';
        require $path . '/jbmodel.element.range.php';
        require $path . '/jbmodel.element.rating.php';
        require $path . '/jbmodel.element.jbpriceadvance.php';
        require $path . '/jbmodel.element.jbcomments.php';
        require $path . '/jbmodel.element.textarea.php';
        require $path . '/jbmodel.element.date.php';
        require $path . '/jbmodel.favorite.php';
        require $path . '/jbmodel.filter.php';
        require $path . '/jbmodel.item.php';
        require $path . '/jbmodel.app.php';
        require $path . '/jbmodel.category.php';
        require $path . '/jbmodel.order.php';
        require $path . '/jbmodel.related.php';
        require $path . '/jbmodel.searchindex.php';
        require $path . '/jbmodel.values.php';
        require $path . '/jbmodel.sku.php';
    }

    /**
     * Load lang files
     */
    private function _initLanguages()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_zoo');
        $lang->load('com_jbzoo', $this->app->path->path('jbapp:'), null, true);
        $lang->load('com_zoo', JPATH_ADMINISTRATOR);
    }

    /**
     * Load others libraries
     */
    private function _initFilterElements()
    {
        jimport('joomla.html.parameter.element');

        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/render/filter';
        require $path . '/element.php';
        require $path . '/element.author.php';
        require $path . '/element.author.checkbox.php';
        require $path . '/element.author.radio.php';
        require $path . '/element.author.select.php';
        require $path . '/element.author.select.chosen.php';
        require $path . '/element.author.text.php';
        require $path . '/element.category.php';
        require $path . '/element.category.chosen.php';
        require $path . '/element.checkbox.php';
        require $path . '/element.country.php';
        require $path . '/element.country.checkbox.php';
        require $path . '/element.country.radio.php';
        require $path . '/element.country.select.php';
        require $path . '/element.country.select.chosen.php';
        require $path . '/element.date.php';
        require $path . '/element.date.range.php';
        require $path . '/element.frontpage.php';
        require $path . '/element.frontpage.jqueryui.php';
        require $path . '/element.hidden.php';
        require $path . '/element.imageexists.php';
        require $path . '/element.imageexists.jqueryui.php';
        require $path . '/element.jbselectcascade.php';
        require $path . '/element.jqueryui.php';
        require $path . '/element.name.php';
        require $path . '/element.name.checkbox.php';
        require $path . '/element.name.radio.php';
        require $path . '/element.name.select.php';
        require $path . '/element.name.select.chosen.php';
        require $path . '/element.radio.php';
        require $path . '/element.rating.php';
        require $path . '/element.rating.ranges.php';
        require $path . '/element.rating.slider.php';
        require $path . '/element.select.php';
        require $path . '/element.select.chosen.php';
        require $path . '/element.slider.php';
        require $path . '/element.tag.php';
        require $path . '/element.tag.checkbox.php';
        require $path . '/element.tag.radio.php';
        require $path . '/element.tag.select.php';
        require $path . '/element.tag.select.chosen.php';
        require $path . '/element.text.php';
        require $path . '/element.text.range.php';
    }

    /**
     * Register and connect events
     */
    private function _initEvents()
    {
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/events';
        require $path . '/jsystem.php';
        require $path . '/jbevent.php';
        require $path . '/jbevent.application.php';
        require $path . '/jbevent.basket.php';
        require $path . '/jbevent.category.php';
        require $path . '/jbevent.comment.php';
        require $path . '/jbevent.element.php';
        require $path . '/jbevent.item.php';
        require $path . '/jbevent.jbzoo.php';
        require $path . '/jbevent.layout.php';
        require $path . '/jbevent.submission.php';
        require $path . '/jbevent.tag.php';
        require $path . '/jbevent.type.php';
        require $path . '/jbevent.payment.php';

        $event      = $this->app->event;
        $dispatcher = $event->dispatcher;

        $event->register('JBEventApplication');
        $dispatcher->connect('application:init', array('JBEventApplication', 'init'));
        $dispatcher->connect('application:saved', array('JBEventApplication', 'saved'));
        $dispatcher->connect('application:deleted', array('JBEventApplication', 'deleted'));
        $dispatcher->connect('application:addmenuitems', array('JBEventApplication', 'addmenuitems'));
        $dispatcher->connect('application:installed', array('JBEventApplication', 'installed'));
        $dispatcher->connect('application:configparams', array('JBEventApplication', 'configparams'));
        $dispatcher->connect('application:sefbuildroute', array('JBEventApplication', 'sefbuildroute'));
        $dispatcher->connect('application:sefparseroute', array('JBEventApplication', 'sefparseroute'));
        $dispatcher->connect('application:sh404sef', array('JBEventApplication', 'sh404sef'));

        $event->register('JBEventCategory');
        $dispatcher->connect('category:init', array('JBEventCategory', 'init'));
        $dispatcher->connect('category:saved', array('JBEventCategory', 'saved'));
        $dispatcher->connect('category:deleted', array('JBEventCategory', 'deleted'));
        $dispatcher->connect('category:stateChanged', array('JBEventCategory', 'stateChanged'));

        $event->register('JBEventItem');
        $dispatcher->connect('item:init', array('JBEventItem', 'init'));
        $dispatcher->connect('item:saved', array('JBEventItem', 'saved'));
        $dispatcher->connect('item:deleted', array('JBEventItem', 'deleted'));
        $dispatcher->connect('item:stateChanged', array('JBEventItem', 'stateChanged'));
        $dispatcher->connect('item:beforedisplay', array('JBEventItem', 'beforeDisplay'));
        $dispatcher->connect('item:afterdisplay', array('JBEventItem', 'afterDisplay'));
        $dispatcher->connect('item:orderquery', array('JBEventItem', 'orderQuery'));
        $dispatcher->connect('item:beforeSaveCategoryRelations', array('JBEventItem', 'beforeSaveCategoryRelations'));
        $dispatcher->connect('item:beforeRenderLayout', array('JBEventItem', 'beforeRenderLayout'));
        $dispatcher->connect('item:afterRenderLayout', array('JBEventItem', 'afterRenderLayout'));

        $event->register('JBEventComment');
        $dispatcher->connect('comment:init', array('JBEventComment', 'init'));
        $dispatcher->connect('comment:saved', array('JBEventComment', 'saved'));
        $dispatcher->connect('comment:deleted', array('JBEventComment', 'deleted'));
        $dispatcher->connect('comment:stateChanged', array('JBEventComment', 'stateChanged'));

        $event->register('JBEventSubmission');
        $dispatcher->connect('submission:init', array('JBEventSubmission', 'init'));
        $dispatcher->connect('submission:saved', array('JBEventSubmission', 'saved'));
        $dispatcher->connect('submission:deleted', array('JBEventSubmission', 'deleted'));
        $dispatcher->connect('submission:beforesave', array('JBEventSubmission', 'beforeSave'));

        $event->register('JBEventElement');
        $dispatcher->connect('element:download', array('JBEventElement', 'download'));
        $dispatcher->connect('element:configform', array('JBEventElement', 'configForm'));
        $dispatcher->connect('element:configparams', array('JBEventElement', 'configParams'));
        $dispatcher->connect('element:configxml', array('JBEventElement', 'configXML'));
        $dispatcher->connect('element:afterdisplay', array('JBEventElement', 'afterDisplay'));
        $dispatcher->connect('element:beforedisplay', array('JBEventElement', 'beforeDisplay'));
        $dispatcher->connect('element:aftersubmissiondisplay', array('JBEventElement', 'afterSubmissionDisplay'));
        $dispatcher->connect('element:beforesubmissiondisplay', array('JBEventElement', 'beforeSubmissionDisplay'));
        $dispatcher->connect('element:beforeedit', array('JBEventElement', 'beforeEdit'));
        $dispatcher->connect('element:afteredit', array('JBEventElement', 'afterEdit'));

        $event->register('JBEventLayout');
        $dispatcher->connect('layout:init', array('JBEventLayout', 'init'));

        $event->register('JBEventTag');
        $dispatcher->connect('tag:saved', array('JBEventTag', 'saved'));
        $dispatcher->connect('tag:deleted', array('JBEventTag', 'deleted'));

        $event->register('JBEventType');
        $dispatcher->connect('type:beforesave', array('JBEventType', 'beforesave'));
        $dispatcher->connect('type:aftersave', array('JBEventType', 'aftersave'));
        $dispatcher->connect('type:copied', array('JBEventType', 'copied'));
        $dispatcher->connect('type:deleted', array('JBEventType', 'deleted'));
        $dispatcher->connect('type:editdisplay', array('JBEventType', 'editDisplay'));
        $dispatcher->connect('type:coreconfig', array('JBEventType', 'coreconfig'));
        $dispatcher->connect('type:assignelements', array('JBEventType', 'assignelements'));

        $event->register('JBEventJBZoo');
        $dispatcher->connect('jbzoo:beforeInit', array('JBEventJBZoo', 'beforeInit'));
        $dispatcher->notify($event->create($this, 'jbzoo:beforeInit'));

        $event->register('JBEventBasket');
        $dispatcher->connect('basket:beforesave', array('JBEventBasket', 'beforeSave'));
        $dispatcher->connect('basket:saved', array('JBEventBasket', 'saved'));

        $event->register('JBEventPayment');
        $dispatcher->connect('payment:callback', array('JBEventPayment', 'callback'));
    }

    /**
     * Init assets for admin path
     */
    private function _initAssets()
    {
        if (!$this->app->jbenv->isSite()) {
            $this->app->jbassets->admin();
            $this->_initAdminMenu();
        }
    }

    /**
     * Get hash
     * @param $string
     * @return string
     */
    static public function getHash($string)
    {
        $k  = base64_decode('amJ' . '6b28' . 'tc2Fs' . 'dC0' . 'xN' . 'Tk3N' . 'TN8');
        $k2 = base64_decode('a' . 'mJ6' . 'b28' . 'tc2Fs' . 'dC0' . '0NT' . 'Y4NT' . 'J8');

        return sha1($k . md5($k2 . sha1(serialize($string))));
    }

    /**
     * Init Admin menu
     */
    private function _initAdminMenu()
    {
        $config = JBModelConfig::model()->getGroup('config.custom', $this->app->jbconfig->getList());
        if (!$config->get('adminmenu_show', 1)) {
            return false;
        }

        $curApp = $this->app->system->application->getUserState('com_zooapplication', 0);

        $appList      = JBModelApp::model()->getSimpleList();
        $findJBZooApp = false;
        $dispatched   = false;
        foreach ($appList as $app) {
            if ($app->application_group == JBZOO_APP_GROUP) {
                $findJBZooApp = true;
                if ($curApp == $app->id) {
                    $dispatched = true;
                }
            }
        }

        if (!$findJBZooApp) {
            return false;
        }

        $router = $this->app->jbrouter;

        $menuItems = array();

        if (!empty($appList)) {
            foreach ($appList as $app) {

                $menuItems['app-' . $app->alias] = array(
                    'name'     => $app->name,
                    'url'      => $router->admin(array('changeapp' => $app->id, 'controller' => 'item')),
                    'children' => array(
                        'add-item'   => array(
                            'name' => JText::_('JBZOO_ADMINMENU_ADD_ITEM'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'item', 'task' => 'add'))
                        ),
                        'sep-1'      => 'divider',
                        'items'      => array(
                            'name' => JText::_('JBZOO_ADMINMENU_ITEMS'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'item', 'task' => ''))
                        ),
                        'categories' => array(
                            'name' => JText::_('JBZOO_ADMINMENU_CATEGORIES'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'category', 'task' => ''))
                        ),
                        'frontpage'  => array(
                            'name' => JText::_('JBZOO_ADMINMENU_FRONTPAGE'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'frontpage', 'task' => ''))
                        ),
                        'comments'   => array(
                            'name' => JText::_('JBZOO_ADMINMENU_COMMENTS'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'comment', 'task' => ''))
                        ),
                        'sep-1'      => 'divider',
                        'config'     => array(
                            'name' => JText::_('JBZOO_ADMINMENU_CONFIG'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'configuration', 'task' => ''))
                        ),
                    )
                );
            }
        }

        $menuItems['sep-1'] = 'divider';

        $menuItems['item-config'] = array(
            'name' => JText::_('JBZOO_ADMINMENU_MAINCONFIG'),
            'url'  => $this->app->jbrouter->admin(array('task' => 'types', 'group' => 'jbuniversal', 'controller' => 'manager')),
        );

        $types = $this->app->jbtype->getSimpleList();
        if (!empty($types)) {
            $children = array();
            foreach ($types as $alias => $type) {
                $children['type-' . $alias] = array(
                    'name' => $type,
                    'url'  => $router->admin(array(
                            'controller' => 'manager',
                            'group'      => 'jbuniversal',
                            'task'       => 'editelements',
                            'cid'        => array('0' => $alias)
                        ))
                );
            }

            $menuItems['item-config']['children'] = $children;
        }

        if ($dispatched) {
            $menuItems['sep-2'] = 'divider';

            $menuItems['jbzoo-admin'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_JBZOOPAGE'),
                'url'      => $router->admin(array('controller' => 'jbindex', 'task' => 'index')),
                'children' => array(
                    'performance'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_TOOLS'),
                        'url'  => $router->admin(array('controller' => 'jbtools', 'task' => 'index')),
                    ),
                    'systemreport' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_CONFIGS'),
                        'url'  => $router->admin(array('controller' => 'jbconfig', 'task' => 'index')),
                    ),
                ),
            );

            $menuItems['jbzoo-import'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_IMPORT'),
                'url'      => $router->admin(array('controller' => 'jbimport', 'task' => 'index')),
                'children' => array(
                    'items'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_ITEMS'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'items')),
                    ),
                    'categories' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_CATEGORIES'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'categories')),
                    ),
                    'stdandart'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_STANDART'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'standart')),
                    ),
                ),
            );

            $menuItems['jbzoo-export'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_EXPORT'),
                'url'      => $router->admin(array('controller' => 'jbexport', 'task' => 'index')),
                'children' => array(
                    'items'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ITEMS'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'items')),
                    ),
                    'categories' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_CATEGORIES'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'categories')),
                    ),
                    'types'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_TYPES'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'types')),
                    ),
                    'yandexyml'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_YANDEXYML'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'yandexyml')),
                    ),
                    'stdandart'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_STANDART'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'standart')),
                    ),
                    'zoobackup'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ZOOBACKUP'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'zoobackup')),
                    ),
                ),
            );

            $menuItems['sep-3'] = 'divider';

            $menuItems['jbzoo-info'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_INFO'),
                'url'      => $router->admin(array('controller' => 'jbinfo', 'task' => 'index')),
                'children' => array(
                    'performance'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_PERFORMANCE'),
                        'url'  => $router->admin(array('controller' => 'jbinfo', 'task' => 'performance')),
                    ),
                    'systemreport' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_SYSTEMREPORT'),
                        'url'  => $router->admin(array('controller' => 'jbinfo', 'task' => 'systemreport')),
                    ),
                    'licence'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_LICENCE'),
                        'url'  => $router->admin(array('controller' => 'jblicence', 'task' => 'index')),
                    ),
                    'server'       => array(
                        'name'   => JText::_('JBZOO_ADMINMENU_SERVER'),
                        'url'    => 'http://server.jbzoo.com/',
                        'target' => '_blank',
                    ),
                )
            );
        }

        $menuItems['jbzoo-support'] = array(
            'name'   => JText::_('JBZOO_ADMINMENU_SUPPORT'),
            'url'    => 'http://forum.jbzoo.com/',
            'target' => '_blank',
        );

        $this->app->jbassets->addVar('JBAdminItems', array(
            'name'  => JText::_('JBZOO_ADMINMENU_CAPTION'),
            'items' => $menuItems,
        ));
    }

    /**
     * Init config file
     */
    private function _initConfig()
    {
        $fn = base64_decode('b' . 'Glj' . 'ZW5j' . 'ZS4' . '=');
        $fp = base64_decode('amJh' . 'cHA6' . 'Y29u' . 'Zmln');
        $f  = $this->app->path->path($fp) . '/' . $fn . self::getDomain(true) . '.' . base64_decode('cGhw');
        if (JFile::exists($f)) {
            require($f);
        }
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

    /**
     * Check temporary directory
     */
    private function _checkTmpDirectory()
    {
        $tmpDisr = JPATH_ROOT . DS . 'tmp';
        $filters = array('jbzoo', 'jbuniversal');

        foreach ($filters as $filter) {

            // files
            $fileList = JFolder::files($tmpDisr, $filter);
            if (!empty($fileList)) {
                foreach ($fileList as $file) {
                    if ($file && is_string($file)) {
                        @chmod($tmpDisr . '/' . $file, 0777);
                        JFile::delete($tmpDisr . '/' . $file);
                    }
                }
            }

            return;

            // folders
            $folderList = JFolder::folders($tmpDisr, $filter);
            if (!empty($folderList)) {
                foreach ($folderList as $folder) {
                    if ($folder && is_string($folder)) {
                        @chmod($tmpDisr . '/' . $folder, 0777);
                        if (is_writable($tmpDisr . '/' . $folder)) {
                            JFolder::delete($tmpDisr . '/' . $folder);
                        }
                    }
                }
            }
        }

    }

    /**
     * Check cache directory
     */
    private function _checkCacheDirectory()
    {
        $cachePath = JPATH_ROOT . DS . 'cache' . DS . 'jbzoo';
        $htaccess  = $cachePath . DS . '.htaccess';
        $index     = $cachePath . DS . 'index.html';

        if (!JFolder::exists($cachePath)) {
            JFolder::create($cachePath);
        }

        if (!JFile::exists($htaccess)) {
            $buffer = "deny from all \n";
            JFile::write($htaccess, $buffer);
        }

        if (!JFile::exists($index)) {
            $buffer = '<!DOCTYPE html><title></title>';
            JFile::write($index, $buffer);
        }
    }

    /**
     * Get domain name
     * @param bool $isAny
     * @return string
     */
    static function getDomain($isAny = false)
    {
        $domain = '';
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $domain  = isset($headers['Host']) ? $headers['Host'] : '';
        }

        if ($isAny && !$domain) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        $domain = preg_replace('#^www\.#', '', $domain);
        list($domain) = explode(':', $domain);

        return $domain;
    }

}
