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
 * Class CompareJBUniversalController
 */
class CompareJBUniversalController extends JBUniversalController
{
    /**
     * Filter action
     * @throws AppException
     * @return void
     */
    function compare()
    {
        // init
        $this->app->jbdebug->mark('compare::init');

        $this->app->jbdoc->noindex();

        $type   = $this->_jbrequest->get('type');
        $appId  = $this->_jbrequest->get('app_id');
        $itemId = $this->_jbrequest->get('Itemid');
        $layout = $this->_jbrequest->get('layout', 'v');

        if (!$type || !$appId) {
            throw new AppException('Type or AppId is no set');
        }

        // get items
        $searchModel = JBModelFilter::model();
        $itemIds     = $this->app->jbcompare->getItemsByType($type);
        $items       = $searchModel->getZooItemsByIds($itemIds);

        $this->items      = $items;
        $this->params     = $this->_params;
        $this->itemType   = $type;
        $this->appId      = $appId;
        $this->layoutType = $layout;
        $this->itemId     = $itemId;

        if (!$this->template = $this->application->getTemplate()) {
            $this->app->jbnotify->error(JText::_('No template selected'));
            return;
        }

        // set renderer
        $this->renderer = $this->app->renderer->create('compare')->addPath(
            array(
                $this->app->path->path('component.site:'),
                $this->template->getPath()
            )
        );

        $this->app->jbdebug->mark('compare::renderInit');

        // display view
        $this->getView('compare')->addTemplatePath($this->template->getPath())->setLayout('compare')->display();

        $this->app->jbdebug->mark('compare::display');
    }

    /**
     * Clear action
     */
    public function clear()
    {
        $this->app->jbcompare->removeItems();

        $type   = $this->_jbrequest->get('type');
        $appId  = $this->_jbrequest->get('app_id');
        $itemId = $this->_jbrequest->get('back_itemid');

        $compareUrl = $this->app->jbrouter->compare($itemId, 'v', $type, $appId);

        JFactory::getApplication()->redirect($compareUrl, JText::_('JBZOO_COMPARE_CLEAR'));
    }

}
