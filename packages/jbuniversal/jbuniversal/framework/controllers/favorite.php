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
 * Class FavoriteJBUniversalController
 */
class FavoriteJBUniversalController extends JBUniversalController
{

    /**
     * Favorite list of curret user
     * @throws AppException
     */
    function favorite()
    {
        // init
        $this->app->jbdebug->mark('favorite::init');

        $this->app->jbdoc->noindex();

        $type   = $this->_jbrequest->get('type');
        $appId  = $this->_jbrequest->get('app_id');
        $itemId = $this->_jbrequest->get('Itemid');

        if (!$appId) {
            throw new AppException('Type or AppId is no set');
        }

        if (!JFactory::getUser()->id) {
            $this->app->jbnotify->notice(JText::_('JBZOO_FAVORITE_NOTAUTH_NOTICE'));
        }

        // get items
        $searchModel = JBModelFilter::model();
        $items       = $this->app->jbfavorite->getAllItems();

        $items        = $searchModel->getZooItemsByIds(array_keys($items));
        $this->items  = $items;
        $this->params = $this->_params;
        $this->appId  = $appId;
        $this->itemId = $itemId;

        if (!$this->template = $this->application->getTemplate()) {
            throw new AppException('No template selected');
        }

        // set renderer
        $this->renderer = $this->app->renderer->create('item')->addPath(
            array(
                $this->app->path->path('component.site:'),
                $this->template->getPath()
            )
        );

        $this->app->jbdebug->mark('favorite::renderInit');

        // display view
        $this->getView('favorite')->addTemplatePath($this->template->getPath())->setLayout('favorite')->display();

        $this->app->jbdebug->mark('favorite::display');
    }

    /**
     * Clear action
     */
    public function remove()
    {
        $itemId = (int)$this->_jbrequest->get('item_id');
        $item   = $this->app->table->item->get($itemId);

        $this->app->jbfavorite->toggleState($item);

        $this->app->jbajax->send();
    }

    /**
     * Remove all action
     */
    public function removeAll()
    {
        $this->app->jbfavorite->removeItems();
        $this->app->jbajax->send();
    }

}
