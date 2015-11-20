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
 * Class JBModuleHelper
 */
class JBModuleHelper
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @type string
     */
    protected $_moduleType = '';

    /**
     * @var \Joomla\Registry\Registry
     */
    protected $_params = null;

    /**
     * @var stdClass
     */
    protected $_module = null;

    /**
     * @var string
     */
    protected $_itemLayout = 'default';

    /**
     * @type string
     */
    protected $_moduleLayout = 'default';

    /**
     * @type JBHtmlHelper
     */
    protected $_jbhtml = null;

    /**
     * @type JBRequestHelper
     */
    protected $_jbrequest = null;

    /**
     * @type JBAssetsHelper
     */
    protected $_jbassets = null;

    /**
     * @param \Joomla\Registry\Registry $params $params
     * @param stdClass  $module
     */
    public function __construct($params, $module)
    {
        JBZoo::init();

        $this->app = App::getInstance('zoo');

        // vars
        $this->_params       = $params;
        $this->_module       = $module;
        $this->_moduleType   = $this->_module->module;
        $this->_itemLayout   = $this->_params->get('item_layout', 'default');
        $this->_moduleLayout = $this->_params->get('layout', 'default');

        // helpers
        $this->_jbhtml    = $this->app->jbhtml;
        $this->_jbrequest = $this->app->jbrequest;
        $this->_jbassets  = $this->app->jbassets;

        $this->app->jbdebug->mark($this->_module->module . '::init');
    }

    /**
     * Load important asstes files
     */
    protected function _loadAssets()
    {
        $this->_jbassets->setAppCSS();
        $this->_jbassets->setAppJS();
        $this->_jbassets->tools();
        $this->_jbassets->less('jbassets:less/general.less');
    }

    /**
     * Init module JS-widget
     */
    protected function _initWidget()
    {
        // noop
    }

    /**
     * @param string       $type
     * @param string|array $addPath
     * @return AppRenderer
     */
    public function createRenderer($type = 'item', $addPath = null)
    {
        // set renderer
        $renderer = $this->app->renderer
            ->create($type)
            ->addPath(array(
                $this->app->path->path('component.site:'),
                $this->app->path->path('modules:' . $this->_moduleType),
                $this->app->path->path('applications:' . JBZOO_APP_GROUP . '/catalog/renderer') // beta TEST
            ));

        if ($addPath && $renderer) {
            $renderer->addPath($addPath);
        }

        if ($renderer && method_exists($renderer, 'setModuleParams')) {
            $renderer->setModuleParams($this->_params);
        }

        return $renderer;
    }

    /**
     * @return string
     */
    public function getModuleId($random = false)
    {
        $modId = 'jbmodule-' . $this->getItemLayout(true) . '-' . $this->_module->id;
        $modId = $random ? $this->app->jbstring->getId($modId) : $modId;

        return $modId;
    }

    /**
     * @return int
     */
    public function getMenuId()
    {
        return (int)$this->_params->get('menuitem', $this->_jbrequest->get('Itemid'));
    }

    /**
     * @return string
     */
    public function getItemLayout()
    {
        return $this->_itemLayout;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_params->get('type');
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return (int)$this->_params->get('application', 0);
    }

    /**
     * @param bool $onlyName
     * @return string
     */
    public function getModuleLayout($onlyName = false)
    {
        $layout = $this->_moduleLayout;
        if ($onlyName && strpos($this->_moduleLayout, ':') !== false) {
            list($tmpl, $layout) = explode(':', $this->_moduleLayout);
            return $layout;
        }

        return $layout;
    }

    /**
     * @param array $attrs
     * @return string
     */
    public function attrs($attrs)
    {
        return $this->app->jbhtml->buildAttrs($attrs);
    }

    /**
     * @param bool $addNoindex
     * @return string
     */
    public function render($addNoindex = true)
    {
        $this->_loadAssets();
        $this->_initWidget();

        $layout = $this->getModuleLayout();
        if (empty($layout)) {
            return null;
        }

        if ($addNoindex) {
            return '<!--noindex-->' . $this->partial($layout) . '<!--/noindex-->';
        }

        return $this->partial($layout);
    }

    /**
     * @param string $layout
     * @param array  $vars
     * @return string
     */
    public function partial($layout = null, $vars = array())
    {
        $layout = !empty($layout) ? $layout : $this->getModuleLayout();

        $__layout = JPath::clean((string)JModuleHelper::getLayoutPath($this->_moduleType, $layout));

        if (JFile::exists($__layout)) {

            $vars['modHelper']    = $this;
            $vars['unique']       = $this->getModuleId(true);
            $vars['params']       = $this->_params;
            $vars['module']       = $this->_module;
            $vars['itemLayout']   = $this->getItemLayout();
            $vars['moduleLayout'] = $this->getModuleLayout();
            $vars['renderer']     = $this->createRenderer('item'); // for compatible

            if (is_array($vars)) {
                foreach ($vars as $_var => $_value) {
                    $$_var = $_value;
                }
            }

            ob_start();
            include($__layout);
            $__html = ob_get_contents();
            ob_end_clean();

            return $__html;
        }

        return null;
    }
}