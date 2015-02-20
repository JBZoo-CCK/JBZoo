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
        if ($this->app->jbstorage->has('assets')) {
            $cache  = $this->app->jbcache->get('assets', 'storage', true);
            $assets = $this->app->jbstorage->get('assets');

            if (count((array)$assets)) {
                if ($cache) {
                    $assets = array_merge((array)$assets, (array)$cache);
                    $assets = array_filter($assets);
                }

                $this->app->jbcache->set('assets', $assets, 'storage', true);
            }
        }

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
        $assets = $this->app->jbcache->get('assets', 'storage', true);
        if (!empty($assets)) {
            foreach ($assets as $file) {
                $ext = JFile::getExt($file);
                $this->app->jbassets->$ext($file);
            }
        }
    }

    /**
     * Joomla Event onAfterInitialise
     */
    public function onBeforeCompileHead()
    {
        $sefConfig = $this->_config->getGroup('config.sef');
        $isSite    = $this->app->jbenv->isSite();
        if ($sefConfig->get('enabled') && $sefConfig->get('fix_canonical') && $isSite) {
            $this->app->jbsef->canonicalFix();
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
