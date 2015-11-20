<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementPriceOption
 * @since 2.2
 */
class JBCartElementPriceOption extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $selected = $this->_getOptions(false);

        return !empty($selected);
    }

    /**
     * Check if element has options.
     * @return bool
     */
    public function hasOptions()
    {
        return $this->config->has('options');
    }

    /**
     * @todo Not completed
     * Check if element has option.
     * @param  string $value Option value
     * @return bool
     */
    public function hasOption($value)
    {
        $options = $this->_parseOptions(false);

        return (in_array($value, $options, true) && !empty($options));
    }

    /**
     * Get elements search data
     * @return mixed|null
     */
    public function getSearchData()
    {
        return $this->getValue();
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'options' => $this->_parseOptions(),
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout($params->get('layout', 'radio') . '.php')) {
            return $this->renderLayout($layout, array(
                'data' => $this->_getOptions($params->get('label', '')),
            ));
        }

        return false;
    }

    /**
     * Get elements value
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     * @param bool   $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = 'value', $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * @todo Complete method
     * Check if element is required.
     * @return int
     */
    public function isRequired()
    {
        if ($this->required === null) {
            $hasOptions = ($this->hasOptions() && $this->_getOptions(false)) || !$this->hasOptions();
            $required   = (int)$this->config->get('required', 0) === 1;

            $this->required = $hasOptions && $required;
        }

        return $this->required;
    }

    /**
     * Wrapper for protected method.
     * @see _parseOptions()
     * @return array
     */
    public function parseOptions()
    {
        return $this->_parseOptions(false);
    }

    /**
     * @todo Not completed
     * Check if element has option.
     * @param  string $value Option value
     * @deprecated
     * @see  JBCartElementPrice::hasOption()
     * @return bool
     */
    public function issetOption($value)
    {
        $options = $this->_parseOptions(false);

        return (!empty($options) && in_array($value, $options, true));
    }

    /**
     * Get options for simple element
     * @param  bool $label - add option with no value
     * @return mixed
     */
    protected function _getOptions($label = true)
    {
        $options = $sorted = $this->_parseOptions(false);

        if (!$this->hasOptions()) {
            $options = $this->getJBPrice()->elementOptions($this->identifier);

        } elseif (!$this->showAll && $options) {
            $selected = $this->getJBPrice()->elementOptions($this->identifier);

            array_walk($selected, function ($value, $key) use ($options) {
                return isset($options[$key]) ? $value : null;
            });
            $options = array_filter($selected);
        }

        if (false !== $label && count($options)) {
            $options = $this->app->jbarray->sortByArray($options, $sorted);
            $options = $this->app->jbarray->unshiftAssoc($options, '', $this->getLabel($label));
        }

        return $options;
    }

    /**
     * Parse options from config.
     * @param  bool $label - add option with no value
     * @return array
     */
    protected function _parseOptions($label = true)
    {
        $options = $this->config->get('options', '');
        $options = $this->_parseLines($options);

        if ($label !== false && count($options)) {
            $options = $this->app->jbarray->unshiftAssoc($options, '', $this->getLabel());
        }

        return $options;
    }

    /**
     * @todo Use helper
     * @param $text
     * @return array
     */
    protected function _parseLines($text)
    {
        $text   = JString::trim($text);
        $result = array();
        if (!empty($text)) {
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = JString::trim($line);

                $result[$line] = $line;
            }
        }

        return $result;
    }

    /**
     * Get label for element template.
     * @param string $label
     * @return string
     */
    protected function getLabel($label = '')
    {
        $label = JString::trim($label);
        if ($label === '') {
            $label = '- ' . $this->getName() . ' -';
        }

        return JText::_($label);
    }
}
