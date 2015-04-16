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


jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

!defined('JBZOO_APP_GROUP') && define('JBZOO_APP_GROUP', 'jbuniversal');
!defined('DIRECTORY_SEPERATOR') && define('DIRECTORY_SEPERATOR', '/');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);
!defined('JBZOO_CLR') && define('JBZOO_CLR', '<i class="clr"></i>'); // TODO plz, kill me

/**
 * Class plgSystemJBZoo
 */
class plgSystemJBZoo extends JPlugin
{
    /**
     * When these components use, JBZoo doesn't init (backend only)
     * @var array
     */
    protected $_disableInitBackEnd = array(
        'com_seoboss'
    );

    /**
     * When these components use, JBZoo doesn't init (frontend only)
     * @var array
     */
    protected $_disableInitFrontEnd = array(
        'com_seoboss'
    );

    /**
     * @var JBZooSystemPlugin
     */
    protected $_jbzooSystemPlg = null;

    /**
     * Joomla Event onAfterInitialise
     */
    public function onAfterInitialise()
    {
        $this->_initFramework();
        $this->_jbzooSystemPlg->onAfterInitialise();
    }

    /**
     * Init Zoo && JBZoo Framework
     * @return bool|null
     */
    protected function _initFramework()
    {
        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) { // hack for performance test
            $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
        }

        if (!$this->_getExtCompatibility()) {
            return false;
        }

        $compEnabled = JComponentHelper::getComponent('com_zoo', true)->enabled;
        if (!$compEnabled) {
            return null;
        }

        $mainConfig = JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
        if (!JFile::exists($mainConfig)) {
            return null;
        }

        require_once($mainConfig);
        if (!class_exists('App')) {
            return null;
        }

        $zoo = App::getInstance('zoo');
        $zoo->event->dispatcher->connect('zoo:initApp', array('plgSystemJBZoo', 'initApp')); // hack for replace inited application

        if ($id = $zoo->request->getInt('changeapp')) {
            $zoo->system->application->setUserState('com_zooapplication', $id);
        }

        $jbzooBootstrap = JPATH_ROOT . '/media/zoo/applications/' . JBZOO_APP_GROUP . '/framework/jbzoo.php';
        if (JFile::exists($jbzooBootstrap)) {
            require_once($jbzooBootstrap);
            JBZoo::init();

            $this->_jbzooSystemPlg = JBZooSystemPlugin::getInstance();

            if ($zoo->jbrequest->isAjax() || $zoo->jbrequest->is('task', 'emailPreview')) {
                header('Content-Type:text/html; charset=utf-8', true); // fix apache default charset
            }

        }
    }

    /**
     * InitApp event handler - hack for custom JBZoo controllers
     * @param AppEvent $event
     */
    public static function initApp($event)
    {
        $zoo    = App::getInstance('zoo');
        $params = $event->getParameters();
        $curApp = $params['application'];

        $currentCtrl = strtolower($zoo->request->getCmd('controller'));
        $jbzooCtrls  = array('autocomplete', 'basket', 'compare', 'favorite', 'payment', 'search', 'viewed');

        if (empty($curApp) ||
            ($curApp->getGroup() != JBZOO_APP_GROUP && in_array($currentCtrl, $jbzooCtrls))
        ) {

            $jbzooApp = $zoo->table->application->first(array(
                'conditions' => array('application_group="' . JBZOO_APP_GROUP . '"')
            ));

            if ($jbzooApp) {
                $params['application'] = $jbzooApp;
                $event->setReturnValue($params);
            }
        }

    }

    /**
     * Check campatibility with external componetns
     */
    protected function _getExtCompatibility()
    {
        $joomlaApp = JFactory::getApplication();
        $jInput    = $joomlaApp->input;
        $isSite    = $joomlaApp->isSite();
        $compName  = $jInput->getCmd('option', '');

        if (
            ($isSite && in_array($compName, $this->_disableInitFrontEnd, true)) ||
            (!$isSite && in_array($compName, $this->_disableInitBackEnd, true))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Joomla Event onAfterRoute
     */
    public function onAfterRoute()
    {
        $this->_jbzooSystemPlg->onAfterRoute();
    }

    /**
     * Joomla Event onAfterDispatch
     */
    public function onAfterDispatch()
    {
        $this->_jbzooSystemPlg->onAfterDispatch();
    }

    /**
     * Joomla Event onBeforeRender
     */
    public function onBeforeRender()
    {
        $this->_jbzooSystemPlg->onBeforeRender();
    }

    /**
     * Joomla Event onAfterRender
     */
    public function onAfterRender()
    {
        $this->_jbzooSystemPlg->onAfterRender();
    }

    /**
     * Joomla Event onBeforeCompileHead
     */
    public function onBeforeCompileHead()
    {
        $this->_jbzooSystemPlg->onBeforeCompileHead();
    }

    /**
     * Joomla Event onSearch
     */
    public function onSearch()
    {
        $this->_jbzooSystemPlg->onSearch();
    }

    /**
     * Joomla Event onSearchAreas
     */
    public function onSearchAreas()
    {
        $this->_jbzooSystemPlg->onSearchAreas();
    }

}
