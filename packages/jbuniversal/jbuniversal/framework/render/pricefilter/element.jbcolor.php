<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBPriceFilterElementJBColor
 */
class JBPriceFilterElementJBColor extends JBPriceFilterElement
{
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

        $colors = explode(PHP_EOL, $this->_element->config->get('options'));
        $path   = JString::trim($this->_element->config->get('path'));
        $colors = $this->app->jbcolor->getColors($colors, $path);
        $data   = array();
        $titles = array();
        $values = $this->_createValues($this->_getDbValues());

        foreach ($values as $key => $value) {
            if (isset($colors[$value])) {
                $color = $colors[$value];

                $data[$key]   = $color;
                $titles[$key] = $value;
            }
        }

        return $this->_html->colors(
            $type,
            $data,
            $this->_getName(true, $this->_isMultiple),
            $this->_value,
            array(),
            '26px',
            '26px',
            $titles
        );
    }

    /**
     * Check is has value
     */
    public function hasValue()
    {
        $values = $this->_getDbValues();

        return !empty($values);
    }

    /**
     * @param $values
     * @return array
     */
    protected function _createValues($values)
    {
        $result = array();
        foreach ($values as $value) {
            $result[$value['value']] = $value['text'];
        }

        return $result;
    }

}