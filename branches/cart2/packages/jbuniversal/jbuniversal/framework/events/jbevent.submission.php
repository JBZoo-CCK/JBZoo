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
 * Class JBEventSubmission
 */
class JBEventSubmission extends JBEvent
{

    /**
     * On submission init
     * @param AppEvent $event
     */
    public static function init($event)
    {
    }

    /**
     * On submission saved
     * @param AppEvent $event
     */
    public static function saved($event)
    {
        $app    = self::app();
        $params = $event->getParameters();
        $item   = $params['item'];

        if ($app->jbrequest->get('goTo', false) && $elements = $item->getElementsByType('jbadvert')) {
            foreach ($elements as $element) {
                /** @type ElementJBAdvert $element */
                $element->addToCart();
            }
            $mod_params = $app->jbjoomla->getModuleParams('mod_jbzoo_basket');

            $url = $app->jbrouter->basket((int)$mod_params->get('menuitem'));
            JFactory::getApplication()->redirect($url);
        }
    }

    /**
     * On submission deleted
     * @param AppEvent $event
     */
    public static function deleted($event)
    {
    }

    /**
     * On submission status chenged
     * @param AppEvent $event
     */
    public static function stateChanged($event)
    {
    }

    /**
     * On submission before saved
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
    }
}