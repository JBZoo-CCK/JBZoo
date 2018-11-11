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

        $this->app->event->register('JBEventSubmission');
        $this->app->event->dispatcher->connect('submission:redirect', array('JBEventSubmission', 'redirect'));

        // code for Zoo submision controller
        // $this->app->event->dispatcher->notify($this->app->event->create($this->submission, 'submission:redirect', array('item' => $this->item, 'new' => !$edit, 'msg' => &$msg)));
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
        if ($sefConfig->get('enabled') && $isSite) {

            if ($sefConfig->get('fix_canonical')) {
                $this->app->jbsef->canonicalFix();
            }

            if ($sefConfig->get('canonical_redirect')) {
                $this->app->jbsef->canonicalRedirect();
            }
        }

        // load all assets
        $this->app->jbassets->loadAll();
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
    
    /**
     * Joomla Event onBeforeRespond
     */
    public function onBeforeRespond()
    {
        // noop
    }
}
