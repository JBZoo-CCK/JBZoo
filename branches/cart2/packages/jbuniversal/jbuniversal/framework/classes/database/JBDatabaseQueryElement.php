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
 * Class JBDatabaseQueryElement
 */
class JBDatabaseQueryElement
{
    /**
     * @var string The name of the element.
     */
    protected $name = null;

    /**
     * @var array An array of elements.
     */
    protected $elements = null;

    /**
     * @var string Glue piece.
     */
    protected $glue = null;

    /**
     * Constructor
     * @param   string $name      The name of the element.
     * @param   mixed $elements  String or array.
     * @param   string $glue      The glue for elements.
     * @return  JBDatabaseQueryElement
     */
    public function __construct($name, $elements, $glue = ", \n\t")
    {
        $this->elements = array();
        $this->name     = $name;
        $this->glue     = $glue;
        $this->append($elements);
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        if (substr($this->name, -2) == '()') {
            return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
        } else {
            return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
        }
    }

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @return void
     */
    public function append($elements)
    {
        if (is_array($elements)) {
            $this->elements = array_merge($this->elements, $elements);
        } else {
            $this->elements = array_merge($this->elements, array($elements));
        }
    }

    /**
     * Gets the elements of this element.
     * @return  string
     */
    public function getElements()
    {
        return $this->elements;
    }
}
