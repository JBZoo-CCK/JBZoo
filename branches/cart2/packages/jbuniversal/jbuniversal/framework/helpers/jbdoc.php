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
 * Class JBDocHelper
 */
class JBDocHelper extends AppHelper
{

    /**
     * Set meta for noindex and nofollow
     */
    public function noindex()
    {
        $doc = JFactory::getDocument();

        // set meta
        $doc->setMetadata('robots', 'noindex, nofollow');
    }

    /**
     * Disable Joomla template
     */
    public function disableTmpl()
    {
        $this->app->jbrequest->set('tmpl', 'component');
    }

    /**
     * Disable Joomla template
     */
    public function rawOutput()
    {
        $this->app->jbrequest->set('tmpl', 'raw');
    }

}
