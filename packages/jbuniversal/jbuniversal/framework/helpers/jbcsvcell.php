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
 * Class JBCSVCellHelper
 */
class JBCSVCellHelper extends AppHelper
{

    /**
     * Class Constructor
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->app->loader->register('JBCSVItem', 'jbelements:item.php');
        $this->app->loader->register('JBCSVCategory', 'jbelements:category.php');
    }

    /**
     * Create a JBElement object
     * @param String|Element $element
     * @param mixed          $item
     * @param String         $group
     * @param array          $options
     * @return JBCSVItem
     * @throws AppException
     */
    public function createItem($element, $item, $group, $options = array())
    {
        if (empty($item)) {
            return false;
        }

        if (is_string($element)) {
            $type = $element;
        } else {
            $type = $element->getElementType();
        }

        // load table class
        $class = 'JBCSVItem' . $group . $type;
        if (!class_exists($class)) {
            $this->app->loader->register($class, 'jbelements:' . $group . '/' . strtolower($type) . '.php');
        }

        if (class_exists($class)) {
            $instance = new $class($element, $item, $options);
        } else {
            $instance = new JBCSVItem($element, $item, $options);
        }

        return $instance;
    }

    /**
     * @param          $type
     * @param Category $category
     * @return mixed
     * @throws AppException
     */
    public function createCategory($type, Category $category)
    {
        // load table class
        $class = 'JBCSVCategory' . $type;

        $this->app->loader->register($class, 'jbelements:category/' . strtolower($type) . '.php');

        if (class_exists($class)) {
            $instance = new $class($category);
        } else {
            throw new AppException('Unknown category class ' . $class);
        }

        return $instance;
    }

}
