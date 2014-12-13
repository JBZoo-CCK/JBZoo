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
 * Class JBEventApplication
 */
class JBEventApplication extends JBEvent
{

    /**
     * On application installed
     * @param AppEvent $event
     */
    public static function installed($event)
    {
    }

    /**
     * On application init
     * @param AppEvent $event
     */
    public static function init($event)
    {
        $app = self::app();
        $app->jbtemplate->init($event);
    }

    /**
     * On after application save
     * @param AppEvent $event
     */
    public static function saved($event)
    {
        $app = self::app();

        $applciation = $event->getSubject();

        $template = $applciation->params->get('template');

        if ($applciation->getGroup() == JBZOO_APP_GROUP && empty($template)) {
            $applciation->params->set('template', 'catalog');
            $app->table->application->save($applciation);
        }
    }

    /**
     * on after application delete
     * @param AppEvent $event
     */
    public static function deleted($event)
    {
    }

    /**
     * on after application configparams
     * @param AppEvent $event
     */
    public static function configParams($event)
    {
    }

    /**
     * on after application sefparseroute
     * @param AppEvent $event
     */
    public static function sefParseRoute($event)
    {
    }

    /**
     * on after application sefbuildroute
     * @param AppEvent $event
     */
    public static function sefBuildRoute($event)
    {
    }

    /**
     * on after application sefbuildroute
     * @param AppEvent $event
     */
    public static function sh404sef($event)
    {
    }

    /**
     * After adminmenu items added
     * @param AppEvent $event
     */
    public static function addMenuItems($event)
    {
        $app    = self::app();
        $params = $event->getParameters();

        $controller = $app->jbrequest->getCtrl();

        if (strpos($controller, 'jb') === 0) {
            $params['tab']->setAttribute('data-href-replace', 'controller=' . $controller);
        }

        $event->setReturnValue($params);
    }

}
