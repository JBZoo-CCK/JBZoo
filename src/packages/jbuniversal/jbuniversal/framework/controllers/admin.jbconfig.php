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
        if ($this->zoo->jbrequest->isPost()) {
            $this->_config->setGroup('config.custom', $this->zoo->jbrequest->getAdminForm());
            $this->setRedirect($this->zoo->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.custom', $this->zoo->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for Yandex.Market (YML)
     */
    public function yandexYml()
    {
        if ($this->zoo->jbrequest->isPost()) {
            $this->_config->setGroup('config.yml', $this->zoo->jbrequest->getAdminForm());
            $this->setRedirect($this->zoo->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.yml', $this->zoo->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for Zoo hacks (YML)
     */
    public function zoohack()
    {
        if ($this->zoo->jbrequest->isPost()) {
            $this->_config->setGroup('config.zoohack', $this->zoo->jbrequest->getAdminForm());
            $this->setRedirect($this->zoo->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.zoohack', $this->zoo->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for assets
     */
    public function assets()
    {
        if ($this->zoo->jbrequest->isPost()) {
            $this->_config->setGroup('config.assets', $this->zoo->jbrequest->getAdminForm());
            $this->setRedirect($this->zoo->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.assets', $this->zoo->jbconfig->getList());

        $this->renderView();
    }

    /**
     * Config for SEF
     */
    public function sef()
    {
        if ($this->zoo->jbrequest->isPost()) {
            // save new config
            $this->_config->setGroup('config.sef', $this->zoo->jbrequest->getAdminForm());

            // save route caching state
            // $cacheState = $this->_config->get('zoo_route_caching', 0, 'config.sef');
            // $this->zoo->set('cache_routes', $cacheState);
            // $this->zoo->component->self->save();
            // $this->zoo->route->clearCache();

            //todofixj4
            //WTF (убрана функция старого кеширования ссылок)

            // redirect after submit
            $this->setRedirect($this->zoo->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('config.sef');

        $this->renderView();
    }

}
