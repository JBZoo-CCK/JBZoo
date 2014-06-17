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
 * Class JBNotifyHelper
 */
class JBNotifyHelper extends AppHelper
{
    /**
     * Show warning message
     * @param $message string
     * @return mixed
     */
    public function warning($message)
    {
        return $this->app->error->raiseWarning(0, $message);
    }

    /**
     * Show notice message
     * @param $message string
     * @return mixed
     */
    public function notice($message)
    {
        return $this->app->error->raiseNotice(0, $message);
    }

    /**
     * Show error message
     * @param $message
     * @return mixed
     */
    public function error($message)
    {
        return $this->app->error->raiseError(500, $message);
    }

}
