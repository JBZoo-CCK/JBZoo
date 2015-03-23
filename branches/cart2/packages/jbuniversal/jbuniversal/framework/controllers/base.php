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


/**
 * Class BaseJBUniversalController
 */
class JBUniversalController extends AppController
{
    /**
     * @var JBRequestHelper
     */
    protected $_jbrequest = null;

    /**
     * @var ParameterData
     */
    protected $_params = null;

    /**
     * @var ParameterData
     */
    protected $params = null;

    /**
     * @var AppView
     */
    protected $_view = null;

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var Application
     */
    public $application;

    /**
     * @var JSite
     */
    public $joomla;

    /**
     * @param array $app
     * @param array $config
     * @throws AppException
     */
    public function __construct($app, $config = array())
    {

        parent::__construct($app, $config);

        $this->_jbrequest = $this->app->jbrequest;

        $task = $this->_jbrequest->getWord('task');

        $ctrl = $this->_jbrequest->getCtrl();

        if (!method_exists($this, $task)) {
            throw new AppException('Action method not found!  ' . $ctrl . ' :: ' . $task . '()');
        }

        // internal vars
        $this->application = $this->app->zoo->getApplication();
        $this->_params     = $this->application->getParams('frontpage');
        $this->joomla      = $this->app->system->application;
        $isSite            = $this->app->jbenv->isSite();

        if (!$isSite) {
            $this->app->document->addStylesheet("root:administrator/templates/system/css/system.css");
            $this->app->jbassets->uikit(true, true);
            $this->_setToolbarTitle();

        } else {
            $this->params  = $this->joomla->getParams();
            $this->pathway = $this->joomla->getPathway();

            $this->app->jbassets->setAppCSS();
            $this->app->jbassets->setAppJS();
        }

        $this->_config = JBModelConfig::model();
    }

    /**
     * Get view for controller && task
     * @param string $name
     * @param string $type
     * @param string $prefix
     * @param array $config
     * @return AppView
     */
    public function getView($name = '', $type = '', $prefix = '', $config = array())
    {
        $config['template_path'] = $this->app->path->path('jbviews:');

        return parent::getView($name, $type, $prefix, $config);
    }

    /**
     * Wrapper for AppView init and rendering
     * @param string $tpl
     */
    protected function renderView($tpl = null)
    {
        $isJoomlaTmpl = $this->_jbrequest->is('tmpl', 'component');

        $ctrl = $this->_jbrequest->getCtrl();
        $task = $this->_jbrequest->getWord('task');
        $path = $this->app->path->path('jbviews:' . $ctrl);

        $view = $this->getView($ctrl);

        // warpper hack
        if (!$isJoomlaTmpl) {
            $jVersion = $this->app->jbversion->joomla('2.7.0') ? '3': '2';

            echo $view->partial('menu');
            echo '<div class="jbzoo box-bottom joomla-' . $jVersion . '">';
        }

        // render view
        $view
            ->addTemplatePath($path)
            ->setLayout($task)
            ->display($tpl);

        // warpper hack
        if (!$isJoomlaTmpl) {
            echo '</div>';
        }
    }

    /**
     * Set Text to
     * @param string $postfix
     */
    protected function _setToolbarTitle($postfix = '')
    {
        $title = JText::_('JBZOO_ADMIN_MENU');
        if (!empty($postfix)) {
            $title .= ': ' . JText::_($postfix);
        }

        $icon = $this->app->path->url('jbapp:application.png');

        $html = array();
        if ($this->app->joomla->version->isCompatible('3.2')) {
            $html[] = '<h1 class="page-title">';
            $html[] = '<img src="' . $icon . '" width="48" height="48" />';
            $html[] = $title;
            $html[] = '</h1>';

        } else {
            $html[] = '<div class="header icon-48-application">';
            $html[] = '<img src="' . $icon . '" width="48" height="48" style="margin-left:-55px;vertical-align:middle;" />';
            $html[] = $title;
            $html[] = '</div>';
        }

        $this->app->system->application->JComponentTitle = implode(PHP_EOL, $html);
    }

}

