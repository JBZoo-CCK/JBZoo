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
 * Class JBToolsJBuniversalController
 * JBZoo tools controller for back-end
 */
class JBConfigJBuniversalController extends JBUniversalController
{

    /**
     * Index page
     */
    public function index()
    {
        if ($this->app->jbrequest->isPost()) {
            $this->_config->setGroup('config.custom', $this->app->jbrequest->getAdminForm());
            $this->setRedirect($this->app->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.custom', $this->app->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for Yandex.Market (YML)
     */
    public function yandexYml()
    {
        if ($this->app->jbrequest->isPost()) {
            $this->_config->setGroup('config.yml', $this->app->jbrequest->getAdminForm());
            $this->setRedirect($this->app->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.yml', $this->app->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for assets
     */
    public function assets()
    {
        if ($this->app->jbrequest->isPost()) {
            $this->_config->setGroup('config.assets', $this->app->jbrequest->getAdminForm());
            $this->setRedirect($this->app->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.assets', $this->app->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for SEF
     */
    public function sef()
    {
        if ($this->app->jbrequest->isPost()) {
            // save new config
            $this->_config->setGroup('config.sef', $this->app->jbrequest->getAdminForm());

            // save route caching state
            $cacheState = $this->_config->get('zoo_route_caching', 0, 'config.sef');
            $this->app->set('cache_routes', $cacheState);
            $this->app->component->self->save();
            $this->app->route->clearCache();

            // redirect after submit
            $this->setRedirect($this->app->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.sef');

        $this->renderView();
    }

}
