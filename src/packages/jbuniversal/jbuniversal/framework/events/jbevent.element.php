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
 * Class JBEventElement
 */
class JBEventElement extends JBEvent
{

    /**
     * Attach new element options for jbzoo extensions
     * @param AppEvent $event
     */
    public static function configParams(AppEvent $event)
    {
        $app = self::app();

        if ($app->jbrequest->is('group', JBZOO_APP_GROUP)) {

            // extract event
            $element = $event->getSubject();
            $params  = $event->getReturnValue();


            // get extranal vars
            $requestParams = array(
                'path'   => $app->jbrequest->get('path'),
                'type'   => $app->jbrequest->get('type'),
                'layout' => $app->jbrequest->get('layout'),
                'cid'    => $app->jbrequest->get('cid'),
            );

            // add new xml params
            if ($app->jbrequest->is('task', 'editelements')) {
                $params = $app->jbelementxml->editElements($element, $params, $requestParams);

            } elseif ($app->jbrequest->is('task', 'assignelements')) {
                $params = $app->jbelementxml->assignElements($element, $params, $requestParams);
            }

            // set params to element
            $event->setReturnValue($params);
        }

    }

    /**
     * On before donload with DownloadElement
     * @param AppEvent $event
     */
    public static function download($event)
    {
    }

    /**
     * On config form init
     * @param AppEvent $event
     */
    public static function configForm($event)
    {
    }

    /**
     * On config XML init
     * @param AppEvent $event
     */
    public static function configXML($event)
    {
    }

    /**
     * On after element display
     * @param $event
     */
    public static function afterDisplay($event)
    {
    }

    /**
     * On before element display
     * @param AppEvent $event
     */
    public static function beforeDisplay($event)
    {
    }

    /**
     * On after submission display
     * @param AppEvent $event
     */
    public static function afterSubmissionDisplay($event)
    {
    }

    /**
     * On Before submission display
     * @param AppEvent $event
     */
    public static function beforeSubmissionDisplay($event)
    {
    }

    /**
     * On after edit view display
     * @param AppEvent $event
     */
    public static function afterEdit($event)
    {
        $params = $event->getParameters();

        if (isset($params['html'][0])) {
            $element = $event->getSubject();
            $find    = 'element-' . $element->getElementType();

            $params['html'][0] = JString::str_ireplace($find, $find . ' element-' . $element->identifier, $params['html'][0]);
        }

        $event->setReturnValue($params);
    }

    /**
     * On before edit view display
     * @param AppEvent $event
     */
    public static function beforeEdit($event)
    {
    }

}