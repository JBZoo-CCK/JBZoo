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
 * Class JBWrapperHelper
 */
class JBWrapperHelper extends AppHelper
{

    protected $_id = 'jbzoo';
    protected $_prefix = 'jbzoo';
    protected $_yoo = 'yoo-zoo';

    /**
     * Get differen system classes for parent wrapper element
     * @return string
     */
    public function attrs()
    {
        $attrs = array();

        // standart
        $attrs['id']      = $this->_id;
        $attrs['class'][] = $this->_prefix;

        // view or task
        if ($view = $this->app->jbrequest->get('view')) {
            $attrs['class'][] = $this->_prefix . '-view-' . $view;
        }

        if ($task = $this->app->jbrequest->get('task')) {
            $attrs['class'][] = $this->_prefix . '-view-' . $task;
        }

        // application info
        $application = $this->app->zoo->getApplication();
        if ($application) {
            $attrs['class'][] = $this->_prefix . '-app-' . $application->alias;
            $attrs['class'][] = $this->_prefix . '-tmpl-' . $application->getTemplate()->name;

            if ((int)$application->params->get('global.config.yoo_support', 1)) {
                $attrs['id']      = $this->_yoo;
                $attrs['class'][] = $this->_yoo;
            }

            if ((int)$application->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->_prefix . '-rborder';
            }
        }

        return $this->_buildAttrs($attrs);
    }

    /**
     * Tags on start
     * @return string
     */
    public function start()
    {
        echo '<div ' . $this->attrs() . ">\n";
    }

    /**
     * Tags on end
     * @return string
     */
    public function end()
    {
        echo '<div class="clear clr"></div></div>' . "\n";
    }

    /**
     * Convert array attrs to string
     * @param array $attrs
     * @return string
     */
    protected function _buildAttrs(array $attrs)
    {
        $result = array();

        foreach ($attrs as $key => $attr) {

            if (is_array($attr)) {
                $result[] .= $key . '="' . implode(' ', $attr) . '"';
            } else {
                $result[] .= $key . '="' . $attr . '"';
            }

        }

        return ' ' . implode(' ', $result) . ' ';
    }
}