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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
require_once(JPATH_ROOT . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');

/**
 * Class JFormFieldJBZooInit
 */
class JFormFieldJBZooInit extends JFormField
{

    protected $type = 'jbzooinit';

    /**
     * Get input
     * @return null|string
     */
    public function getInput()
    {
        JBZoo::init();

        $zoo = App::getInstance('zoo');
        $zoo->system->language->load('com_jbzoo', $zoo->path->path('applications:jbuniversal'), null, true);
        $zoo->system->language->load('com_jbzoostd', $zoo->path->path('applications:jbuniversal'), null, true);
        $zoo->jbassets->admin();

        return null;
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        return null;
    }

}