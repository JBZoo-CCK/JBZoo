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

        // add advert to cart
        if ($request = $item->getElementsByType('jbadvert')) {
            $redirect = false;

            foreach ($request as $element) {
                $request = $app->jbrequest->getArray('elements');
                $elemId  = $element->identifier;
                if (isset($request[$elemId]['gotocart']) && (int)$request[$elemId]['gotocart']) {
                    $element->addToCart();
                    $redirect = true;
                }
            }

            if ($redirect) {
                JFactory::getApplication()->redirect($app->jbrouter->basket());
            }
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