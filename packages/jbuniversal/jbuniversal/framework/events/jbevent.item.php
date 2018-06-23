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
 * Class JBEventItem
 */
class JBEventItem extends JBEvent
{

    /**
     * On item init
     * @param AppEvent $event
     */
    public static function init($event)
    {
    }

    /**
     * On item saved event
     * @static
     * @param $event AppEvent
     * @return null
     */
    public static function saved($event)
    {
        // vars
        $app      = self::app();
        $item     = $event->getSubject();
        $itemType = $item->getType()->id;

        // hack for JBZoo import optimization
        if ($item->getParams()->get('jbzoo.no_index', 0) == 1) {
            return null;
        }

        if (!$app->jbrequest->is('controller', 'jbimport')) {

            // update index data
            $app->jbtables->checkSku(true);
            $indexTableName = $app->jbtables->getIndexTable($itemType);

            if ($app->jbtables->isTableExists($indexTableName, true)) {
                JBModelSearchindex::model()->updateByItem($item);
            }

        }
    }

    /**
     * On item deleted event
     * @static
     * @param $event AppEvent
     */
    public static function deleted($event)
    {
        // vars
        $app      = self::app();
        $item     = $event->getSubject();
        $itemType = $item->getType()->id;

        // check index table
        $tableName = $app->jbtables->getIndexTable($itemType);
        if (!$app->jbtables->isTableExists($tableName)) {
            $app->jbtables->createIndexTable($itemType);
        }

        // update index data
        JBModelSearchindex::model()->removeById($item);
        
        // execute item trigger
        $jbimageElements = $item->getElements();
        foreach ($jbimageElements as $element) {
            if (method_exists($element, 'triggerItemDeleted')) {
                $element->triggerItemDeleted();
            }
        }        
    }

    /**
     * On item status changed
     * @param AppEvent $event
     */
    public static function stateChanged($event)
    {
    }

    /**
     * On item before display
     * @param AppEvent $event
     */
    public static function beforeDisplay($event)
    {
    }

    /**
     * On item after display
     * @param AppEvent $event
     */
    public static function afterDisplay($event)
    {
    }

    /**
     * On item before save category relations
     * @param AppEvent $event
     */
    public static function beforeSaveCategoryRelations($event)
    {
    }

    /**
     * On item order
     * @param AppEvent $event
     */
    public static function orderQuery($event)
    {
        $order     = $event->getSubject();
        $ordParams = $event->getParameters();
        $newOrder  = self::app()->jborder->setItemOrder($order, $ordParams['result']);

        if ($newOrder) {
            $ordParams['result'] = $newOrder;
        }
    }

    /**
     * Before render some item layout (experemental)
     * @param AppEvent $event
     */
    public static function beforeRenderLayout($event)
    {
    }

    /**
     * After render some item layout (experemental)
     * @param AppEvent $event
     */
    public static function afterRenderLayout($event)
    {
        $item   = $event->getSubject();
        $params = $event->getParameters();
        $app    = self::app();

        if ($params['layout'] == 'full') {
            $app->jbviewed->add($item);
        }
    }
}