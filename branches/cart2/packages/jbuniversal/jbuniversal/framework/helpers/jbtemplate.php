<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBTemplateHelper
 */
class JBTemplateHelper extends AppHelper
{

    /**
     * Template class prefix
     * @var string
     */
    protected $_classPrefix = 'JBTemplate';

    /**
     * Preg replacement
     * @var string
     */
    protected $_replacement = '|';

    /**
     * Init JBTemplate
     * @param $event
     */
    public function init($event)
    {
        $application = $event->getSubject();
        $isSite      = $this->app->jbenv->isSite();
        $appGroup    = $application->application_group;
        $template    = $application->params->get('template');
        $controller  = $this->app->jbrequest->get('controller');
        $task        = $this->app->jbrequest->get('task');

        $application->cartConfig = JBModelConfig::model()->getGroup('cart.config');

        if ($controller == 'basket' && $task == 'index') {
            $application->basketTmpl = $template = $application->cartConfig->get('tmpl_name', 'uikit');
        }

        if ($isSite && $appGroup == JBZOO_APP_GROUP) {
            $classPath     = $this->_getClassPath();
            $tmplClassPath = $this->_getTmplClassPath($template);

            require_once "{$classPath}";
            $this->_initMethod($event, $this->_classPrefix, 'init');

            $template = $this->camelize($template);

            if (file_exists($tmplClassPath)) {
                require_once "{$tmplClassPath}";
                $this->_initMethod($event, $this->_getClassName($template));
            } else {
                $this->_initMethod($event, $this->_classPrefix);
            }
        }

        if ($appGroup == JBZOO_APP_GROUP) {
            JFactory::getLanguage()->load('jbzoo_' . $template, $this->app->path->path('jbtmpl:' . $template));
        }
    }

    /**
     * Camilize string
     * @param string $template
     * @return array|mixed|null|string
     */
    public function camelize($template = 'catalog')
    {
        if (preg_match('/[-_]/', $template)) {
            $newName  = null;
            $template = preg_replace('/[-_]/', $this->_replacement, $template);
            $template = explode($this->_replacement, $template);

            foreach ($template as $subStr) {
                $newName .= JString::ucfirst($subStr);
            }

            $template = $newName;
        }

        return $template;
    }

    /**
     * Register helper path by template.
     *
     * @param string $template
     */
    public function regHelpersByTpl($template = 'catalog')
    {
        $this->app->path->register($this->app->path->path('jbtmpl:' . $template . '/helpers'), 'helpers');
    }

    /**
     * Get path for jbtemplate class
     * @return string
     */
    protected function _getClassPath()
    {
        return $this->app->path->path('jbzoo:classes') . DS . 'jbtemplate.php';
    }

    /**
     * Get selected template class path
     * @param string $name
     * @return string
     */
    protected function _getTmplClassPath($name = 'catalog')
    {
        return $this->app->path->path('jbtmpl:' . $name) . DS . 'template.php';
    }

    /**
     * Init class method
     * @param $event
     * @param string $className
     * @param string $method
     */
    protected function _initMethod($event, $className = 'JBTemplate', $method = 'onInit')
    {
        if (class_exists($className) && $event) {
            $templateObj = new $className($event);
            if (method_exists($templateObj, $method)) {
                $templateObj->{$method}();
            }
            $event->getSubject()->jbtemplate = $templateObj;
        }
    }

    /**
     * Get template class name
     * @param string $name
     * @return string
     */
    protected function _getClassName($name = 'catalog')
    {
        return $this->_classPrefix . JString::ucfirst($name);
    }

}
