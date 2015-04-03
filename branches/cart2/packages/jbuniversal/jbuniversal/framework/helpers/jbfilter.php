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
 * Class JBFilterHelper
 */
class JBFilterHelper extends AppHelper
{

    /**
     * @var string|null
     */
    protected $_type = null;

    /**
     * @var int|null
     */
    protected $_application = null;

    /**
     * Constructor
     * @param $app
     */
    public function __construct($app)
    {
        $this->app   = $app;
        $this->_name = strtolower(basename(get_class($this), 'Helper'));

        $this->app->loader->register('Type', 'classes:type.php');
        $this->app->loader->register('FilterElement', 'classes:/filter/element.php');
    }

    /**
     * Set filter info before helper use
     * @param string $type
     * @param int    $application
     * @return void
     */
    public function set($type, $application)
    {
        $this->_type        = $type;
        $this->_application = $application;
    }

    /**
     * Get element by id
     * @param string $elementId
     * @return mixed
     */
    public function getElement($elementId)
    {
        if (!isset($this->_elements[$elementId])) {
            $zooType                     = $this->getType($this->_type, $this->_application);
            $this->_elements[$elementId] = $zooType->getElement($elementId);
        }

        return $this->_elements[$elementId];
    }

    /**
     * Get type
     * @param string $type
     * @param        $application
     * @return mixed
     */
    public function getType($type, $application)
    {
        if (!isset($this->_types[$type])) {
            $this->_types[$type] = new Type($type, $application);
        }

        return $this->_types[$type];
    }

    /**
     * Element render
     * @param string $identifier
     * @param bool   $value
     * @param array  $params
     * @param array  $attrs
     * @return mixed
     * @throws Exception
     */
    public function elementRender($identifier, $value = false, $params = array(), $attrs = array())
    {
        $element     = $this->getElement($identifier);
        $elementType = $element->getElementType();
        $render      = $this->_getRender($params, $elementType);

        $params['jbzoo_original_type']    = $elementType;
        $params['jbzoo_is_original_type'] = ($elementType == $render);

        $renderPaths   = explode('-', $render);
        $className     = 'JBFilterElement';
        $classFilename = 'element';

        foreach ($renderPaths as $renderPath) {

            $className .= $renderPath;
            $classFilename .= '.' . $renderPath;

            $this->app->loader->register($className, 'renderer:/filter/' . $classFilename . '.php');

            if (!class_exists($className)) {
                throw new Exception('Unkown class render "' . $className . '"');
            }
        }

        $render = new $className($identifier, $value, $params, $attrs);
        if ($render->hasValue()) {
            return $render->html();
        }

        return null;
    }

    /**
     * Mapper elementType to render method
     * @param array  $params
     * @param string $elementType
     * @return string
     */
    private function _getRender(array $params, $elementType)
    {
        if (!isset($params['jbzoo_filter_render'])) {
            $params['jbzoo_filter_render'] = '_auto_';
        }

        if ($params['jbzoo_filter_render'] == '_auto_') {

            switch ($elementType) {

                case 'radio':
                    $renderMethod = 'radio';
                    break;

                case 'checkbox':
                    $renderMethod = 'checkbox';
                    break;

                case 'select':
                    $renderMethod = 'select';
                    break;

                case 'country':
                    $renderMethod = 'country-select';
                    break;

                case 'itemcreated':
                case 'itemmodified':
                case 'itempublish_down':
                case 'itempublish_up':
                case 'date':
                    $renderMethod = 'date-range';
                    break;

                case 'itemauthor':
                    $renderMethod = 'author';
                    break;

                case 'itemcategory':
                    $renderMethod = 'category';
                    break;

                case 'itemfrontpage':
                    $renderMethod = 'frontpage';
                    break;

                case 'itemtag':
                    $renderMethod = 'tag-checkbox';
                    break;

                case 'rating':
                    $renderMethod = 'rating-slider';
                    break;

                case 'jbselectcascade':
                    $renderMethod = 'jbselectcascade';
                    break;

                case 'text':
                case 'textarea':
                case 'itemname':
                default :
                    $renderMethod = 'text';
                    break;
            }

        } else {
            $renderMethod = $params['jbzoo_filter_render'];
        }

        return $renderMethod;
    }
}
