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
        $this->zoo->jbdebug->mark('favorite::init');

        $this->zoo->jbdoc->noindex();

        $type   = $this->_jbrequest->get('type');
        $appId  = $this->_jbrequest->get('app_id');
        $itemId = $this->_jbrequest->get('Itemid');

        if (!$appId) {
            $appId = 0;
        }

        if (!JFactory::getUser()->id) {
            $this->zoo->jbnotify->notice(JText::_('JBZOO_FAVORITE_NOTAUTH_NOTICE'));
        }

        // get items
        $searchModel = JBModelFilter::model();
        $items       = $this->zoo->jbfavorite->getAllItems();

        $items        = $searchModel->getZooItemsByIds(array_keys($items));
        $this->items  = $items;
        $this->params = $this->_params;
        $this->appId  = $appId;
        $this->itemId = $itemId;

        if (!$this->template = $this->application->getTemplate()) {
            throw new AppException('No template selected');
        }

        // set renderer
        $this->renderer = $this->zoo->renderer->create('item')->addPath(
            array(
                $this->zoo->path->path('component.site:'),
                $this->template->getPath()
            )
        );

        $this->zoo->jbdebug->mark('favorite::renderInit');

        // display view
        $this->getView('favorite')->addTemplatePath($this->template->getPath())->setLayout('favorite')->display();

        $this->zoo->jbdebug->mark('favorite::display');
    }

    /**
     * Clear action
     */
    public function remove()
    {
        $itemId = (int)$this->_jbrequest->get('item_id');
        $item   = $this->zoo->table->item->get($itemId);

        $this->zoo->jbfavorite->toggleState($item);

        $this->zoo->jbajax->send();
    }

    /**
     * Remove all action
     */
    public function removeAll()
    {
        $this->zoo->jbfavorite->removeItems();
        $this->zoo->jbajax->send();
    }

}
