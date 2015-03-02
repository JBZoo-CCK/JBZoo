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
 * Class JBZooSystemPlugin
 */
class JBZooSystemPlugin
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @var JBModelConfig
     */
    protected $_config = null;

    /**
     * @return JBZooSystemPlugin
     */
    static public function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->app     = App::getInstance('zoo');
        $this->_config = JBModelConfig::model();
    }

    /**
     * Joomla Event onAfterInitialise
     */
    public function onAfterInitialise()
    {
        // JBZoo SEF fix
        $sefEnabled = $this->_config->get('enabled', 0, 'config.sef');
        if ($sefEnabled && $this->app->jbenv->isSite()) {

            // get helper links
            $dispatcher = $this->app->event->dispatcher;
            $jbsef      = $this->app->jbsef;

            // init JBZoo SEF
            $dispatcher->connect('application:sefparseroute', array($jbsef, 'sefParseRoute'));
            $dispatcher->connect('application:sefbuildroute', array($jbsef, 'sefBuildRoute'));
        }
    }

    /**
     * Joomla Event onAfterRoute
     */
    public function onAfterRoute()
    {
        // noop
    }

    /**
     * Joomla Event onAfterDispatch
     */
    public function onAfterDispatch()
    {
    }

    /**
     * Joomla Event onAfterRender
     */
    public function onAfterRender()
    {
    }

    /**
     * Joomla Event onBeforeRender
     */
    public function onBeforeRender()
    {
    }

    /**
     * Joomla Event onAfterInitialise
     */
    public function onBeforeCompileHead()
    {
        // fix Zoo SEF problems
        $sefConfig = $this->_config->getGroup('config.sef');
        $isSite    = $this->app->jbenv->isSite();
        if ($sefConfig->get('enabled') && $sefConfig->get('fix_canonical') && $isSite) {
            $this->app->jbsef->canonicalFix();
        }

        // load assets
        $assetsConfig = $this->_config->getGroup('config.assets');
        $jbassets     = $this->app->jbassets;
        $typeList     = $jbassets->getList();
        $mode         = $assetsConfig->get('mode', 'split');

        foreach ($typeList as $type => $groupList) {
            foreach ($groupList as $group => $files) {

                if ($mode == 'debug'
                    || $group == JBAssetsHelper::GROUP_CORE
                    || ($group == JBAssetsHelper::GROUP_LIBRARY && $type == 'css')
                ) {
                    foreach ($files as $file) {
                        $jbassets->includeFile($file, $type);
                    }
                } else if ($mode == 'split') {
                    $file = $this->app->jbminifier->split($files, $type);
                    $jbassets->includeFile($file, $type);
                }

            }
        }
    }

    /**
     * Joomla Event onSearch
     */
    public function onSearch()
    {
        // noop
    }

    /**
     * Joomla Event onSearchAreas
     */
    public function onSearchAreas()
    {
        // noop
    }

}
