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
 * Class JBAjaxHelper
 */
class JBAjaxHelper extends AppHelper
{

    /**
     * Send response in JSON-format
     * @param array $data
     * @param bool $result
     */
    public function send(array $data = array(), $result = true)
    {
        $data['result'] = $result;

        if (!isset($data['message'])) {
            $data['message'] = false;
        }

        JResponse::allowCache(false);
        JResponse::setHeader('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT', true);
        JResponse::setHeader('Content-Type', 'application/json; charset=utf-8', true);
        JResponse::sendHeaders();

        jexit(json_encode($data));
    }

}
