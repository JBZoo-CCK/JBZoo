<?php

/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JBFilterElementJBColor extends JBFilterElement
{
    /**
     * Elements object
     * @var
     */
    protected $_element;

    /**
     * @param string $identifier
     * @param array|string $value
     * @param array $params
     * @param array $attrs
     */
    public function __construct($identifier, $value, array $params, array $attrs)
    {
        parent::__construct($identifier, $value, $params, $attrs);

        $this->_element = $this->app->jbfilter->getElement($this->_identifier);
    }

    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $type = $this->_isMultiple ? 'checkbox' : 'radio';

        if (is_string($this->_value)) {
            $this->_value = $this->app->jbcolor->clean(explode(',', $this->_value));
        }

        $values = $this->_createValues($this->_getDbValues());

        return $this->_jbhtml->colors(
            $type,
            $values,
            $this->_getName(),
            $this->_value
        );
    }

    /**
     * @param $values
     * @return array
     */
    protected function _createValues($values)
    {
        $colors = explode("\n", $this->_element->config->get('colors'));
        $path   = JString::trim($this->_element->config->get('path'));

        $result = array();
        $colors = $this->app->jbcolor->getColors($colors, $path);

        foreach ($values as $value) {
            $result[$value['value']] = $value['value'];
        }

        $colors = array_intersect(array_flip($colors), $result);

        return array_flip($colors);
    }

}