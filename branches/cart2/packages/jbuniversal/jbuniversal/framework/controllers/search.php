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
 * Class SearchJBUniversalController
 */
class SearchJBUniversalController extends JBUniversalController
{

    /**
     * Filter action
     */
    function filter()
    {
        $this->app->jbdebug->mark('filter::init');

        $this->app->jbdoc->noindex();

        $type     = $this->_jbrequest->get('type');
        $page     = ($page = $this->_jbrequest->get('page', 1)) ? $page : 1;
        $logic    = strtoupper($this->_jbrequest->getWord('logic', 'and'));
        $order    = $this->_jbrequest->get('order', 'none');
        $exact    = (int)$this->_jbrequest->get('exact', 0);
        $limit    = $this->_jbrequest->get('limit', $this->_params->get('config.items_per_page', 2));
        $offset   = $limit * ($page - 1);
        $elements = $this->_jbrequest->getElements();
        $appId    = $this->_jbrequest->get('app_id');

        // search!
        $searchModel = JBModelFilter::model();
        $items       = $searchModel->search($elements, $logic, $type, $appId, $exact, $offset, $limit, $order);
        $itemsCount  = $searchModel->searchCount($elements, $logic, $type, $appId, $exact);

        // create pagination
        if ($this->_jbrequest->isPost()) {
            $_POST['option'] = 'com_zoo';
            unset($_POST['page']);
            unset($_POST['view']);
            unset($_POST['layout']);
            $this->pagination_link = 'index.php?' . $this->app->jbrouter->query($_POST);

        } else {
            $_GET['option'] = 'com_zoo';
            unset($_GET['page']);
            unset($_GET['view']);
            unset($_GET['layout']);
            $this->pagination_link = 'index.php?' . $this->app->jbrouter->query($_GET);
        }

        $this->pagination = $this->app->pagination->create($itemsCount, $page, $limit, 'page', 'app');
        $this->pagination->setShowAll($limit == 0);
        $this->app->jbdebug->mark('filter::pagination');

        // set template and params
        if (!$this->template = $this->application->getTemplate()) {
            $this->app->jbnotify->error(JText::_('No template selected'));
            return;
        }

        // assign variables
        $this->items      = $items;
        $this->params     = $this->_params;
        $this->itemsCount = $itemsCount;

        // set renderer
        $this->renderer = $this->app->renderer->create('item')->addPath(
            array(
                $this->app->path->path('component.site:'),
                $this->template->getPath()
            )
        );
        $this->app->jbdebug->mark('filter::renderInit');

        // display view
        $this->getView('filter')->addTemplatePath($this->template->getPath())->setLayout('filter')->display();

        $this->app->jbdebug->mark('filter::display');
    }

}