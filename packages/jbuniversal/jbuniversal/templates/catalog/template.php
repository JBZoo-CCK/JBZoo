<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Segrey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBTemplateCatalog
 */
class JBTemplateCatalog extends JBTemplate
{

    /**
     * On init template.
     *
     * @return void
     */
    public function onInit()
    {
        $this->app->jbassets->less(array(
            'jbassets:less/general.less',
            'jbassets:less/catalog.styles.less',
        ));
    }

}
