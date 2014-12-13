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
 * Class JBWrapperHelper
 */
class JBWrapperHelper extends AppHelper
{

    /**
     * Tags on start
     * @return string
     */
    public function start()
    {
        echo $this->app->zoo->getApplication()->jbtemplate->wrapStart();
    }

    /**
     * Tags on end
     * @return string
     */
    public function end()
    {
        echo $this->app->zoo->getApplication()->jbtemplate->wrapEnd();
    }

}
