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
 * Class JBAjaxHelper
 */
class JBAjaxHelper extends AppHelper
{

    /**
     * Send response in JSON-format
     * @param array $data
     * @param bool  $result
     */
    public function send(array $data = array(), $result = true)
    {
        $data['result'] = $result;

        if (!isset($data['message'])) {
            $data['message'] = false;
        }

        if ($this->app->jbversion->joomla('3')) {
            $app = JFactory::getApplication();
            $app->allowCache(false);
            $app->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT', true);
            $app->setHeader('Content-Type', 'application/json; charset=utf-8', true);
            $app->sendHeaders();

        } elseif (class_exists('JResponse')) {
            JResponse::allowCache(false);
            JResponse::setHeader('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT', true);
            JResponse::setHeader('Content-Type', 'application/json; charset=utf-8', true);
            JResponse::sendHeaders();
        }

        $data['mpu'] = round(memory_get_peak_usage(false) / 1024 / 1024, 2) . ' M'; // debug info

        jexit(json_encode($data));
    }

}
