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

        $document       = JFactory::getDocument();
        $session        = JFactory::getSession();

        $params         = $this->joomla->getParams();
        $type           = $this->_jbrequest->get('type', $params->get('type'));
        $view           = $this->_jbrequest->get('view', '');
        $page           = ($page = $this->_jbrequest->get('page', 1)) ? $page : 1;
        $logic          = strtoupper($this->_jbrequest->getWord('logic', $params->get('logic', 'and')));
        $order          = $this->_jbrequest->get('order', $params->get('order_default', 'none'));
        $exact          = (int)$this->_jbrequest->get('exact', $params->get('exact', 0));
        $limit          = $this->_jbrequest->get('limit', $this->_params->get('config.items_per_page', 2));
        $offset         = $limit * ($page - 1);
        $elements       = $this->_jbrequest->getElements();
        $appId          = $this->_jbrequest->get('app_id', $params->get('application'));
        $sessionAppId   = $session->get('orderApp', 0);
        $sessionOrder   = $session->get('order', '');

        if ($view == 'filter') {
            if ($sessionAppId == $appId) {
                if (is_string($order)) {
                    $orderString = $order;
                } else {
                    $orderString = $sessionOrder;
                }

                $order          = array();
                $order['field'] = str_replace(array('_asc', '_desc'), array('', ''), $orderString);

                if (strpos($orderString, '_desc')) {
                    $order['reverse'] = 1;
                }

                $session->set('order', $orderString);
            }
        } else {
            $session->set('order', $order['field'].'_'.(isset($order['reverse']) ? '_desc' : '_asc'));
        }

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
        $this->items        = $items;
        $this->params       = $this->_params;
        $this->itemsCount   = $itemsCount;
        $this->description  = '';
        $this->title        = '';
        $this->count        = true;

        // get metadata
        $title              = '';
        $keywords           = '';
        $metaDescription    = '';
        
        // Set Menu Meta
        $menu           = $this->app->menu->getActive();
        $menu_params    = $menu ? $this->app->parameter->create($menu->params) : '';

        if ($menu and in_array(@$menu->query['view'], array('filter')) and $menu_params) {

            $condElements = $this->app->jbconditions->getValue($menu_params->get('conditions'));

            if ($condElements == $elements) {
                $metaDescription    = $menu_params->get('menu-meta_description') ? $menu_params->get('menu-meta_description') : $this->description;
                $title              = $menu_params->get('page_title') ? $menu_params->get('page_title') : $menu->title;
                $keywords           = $menu_params->get('menu-meta_keywords');

                $this->description  = $menu_params->get('description');
                $this->title        = $menu_params->get('page_heading') ? $menu_params->get('page_heading') : $title;
                $this->count        = $menu_params->get('count');
            } else {
                // Set noindex
                $this->app->jbdoc->noindex();
            }
        } else {
            // Set noindex
            $this->app->jbdoc->noindex();
        }

        if ($title) {
            $this->app->document->setTitle($this->app->zoo->buildPageTitle($title));
        }

        if ($metaDescription) {
            $this->app->document->setDescription($metaDescription);
        }

        if ($keywords) {
            $this->app->document->setMetadata('keywords', $keywords);
        }

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