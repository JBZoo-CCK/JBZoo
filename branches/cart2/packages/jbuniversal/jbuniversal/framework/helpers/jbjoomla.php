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
 * Class JBJoomlaHelper
 */
class JBJoomlaHelper extends AppHelper
{

    /**
     * Render modules by position name
     * @param string $position
     * @param array  $options
     * @return string
     */
    public function renderPosition($position, array $options = array())
    {
        $this->app->jbdebug->mark('jbjoomla::renderPosition (' . $position . ')::start');

        jimport('joomla.application.module.helper');

        $document     = JFactory::getDocument();
        $renderer     = $document->loadRenderer('modules');
        $positionHtml = $renderer->render($position, $options, null);

        $this->app->jbdebug->mark('jbjoomla::renderPosition (' . $position . ')::finish');

        return $positionHtml;
    }

    /**
     * Render module by id
     * @param int $moduleId
     * @return null|string
     */
    public function renderModuleById($moduleId)
    {
        $this->app->jbdebug->mark('jbjoomla::renderModuleById (' . $moduleId . ')::start');

        jimport('joomla.application.module.helper');

        $modules = $this->app->module->load();

        if ($moduleId && isset($modules[$moduleId])) {

            if ($modules[$moduleId]->published) {
                $rendered = JModuleHelper::renderModule($modules[$moduleId]);

                $this->app->jbdebug->mark('jbjoomla::renderModuleById (' . $moduleId . ')::finish');

                return $rendered;
            }

        }

        $this->app->jbdebug->mark('jbjoomla::renderModuleById (' . $moduleId . ')::finish');

        return null;
    }

    /**
     * Get module params by name
     * @param $name
     * @return JRegistry
     */
    public function getModuleParams($name)
    {
        $module = JModuleHelper::getModule($name);

        return new JRegistry($module->params);
    }


}
