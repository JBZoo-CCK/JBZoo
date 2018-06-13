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
 * Class JBEventType
 */
class JBEventType extends JBEvent
{
    /**
     * On type before save
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
        $itemType = $event->getSubject();
        self::app()->jbtables->checkTypeBeforeSave($itemType);
    }

    /**
     * On type after save
     * @param AppEvent $event
     */
    public static function afterSave($event)
    {
        $itemType = $event->getSubject();
        self::app()->jbtables->checkTypeAfterSave($itemType);
    }

    /**
     * On type copied
     * @param AppEvent $event
     */
    public static function copied($event)
    {

    }

    /**
     * On type deleted
     * @param AppEvent $event
     */
    public static function deleted($event)
    {
        $app    = self::app();
        $type   = $event->getSubject();
        $config = JBModelConfig::model();

        $elements = $app->jbprice->getTypePrices($type);

        $tableName = $app->jbtables->getIndexTable($type->id);
        $app->jbtables->dropTable($tableName);

        if (!empty($elements)) {
            foreach ($elements as $id => $element) {
                $config->removeGroup('cart.' . JBCart::ELEMENT_TYPE_PRICE . '.' . $id);
                $config->removeGroup('cart.' . JBCart::CONFIG_PRICE_TMPL . '.' . $id);
                $config->removeGroup('cart.' . JBCart::CONFIG_PRICE_TMPL_FILTER . '.' . $id);
            }
        }
    }

    /**
     * On type edit display
     * @param AppEvent $event
     */
    public static function editDisplay($event)
    {

    }

    /**
     * On type assign elements
     * @param AppEvent $event
     */
    public static function assignElements($event)
    {

    }

    /**
     * On type core config
     * @param AppEvent $event
     */
    public static function coreConfig($event)
    {

    }

}