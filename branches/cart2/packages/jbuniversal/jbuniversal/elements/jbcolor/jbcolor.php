<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class ElementJBColor
 */
class ElementJBColor extends Element implements iSubmittable
{
    /**
     * @var JBColorHelper
     */
    protected $_jbcolor;

    /**
     *  Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_jbcolor = $this->app->jbcolor;
    }

    /**
     * Checks if the element value is set
     * @param array $params
     * @return bool|void
     */
    public function hasValue($params = array())
    {
        $selectedColors = $this->get('option', array());

        if (!empty($selectedColors)) {
            return true;
        }

        return false;
    }

    /**
     * Get elements search data.
     * @return null|string
     */
    public function getSearchData()
    {
        $colorItems = $this->get('option');

        if (empty($colorItems)) {
            return null;
        }

        $result = implode(PHP_EOL, $colorItems);

        return (empty($result) ? null : $result);
    }

    /**
     * Render action
     * @param array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $this->loadAssets();
        $result = array();
        $params = $this->app->data->create($params);
        $colors = $this->getSelectedColors();

        foreach ($colors as $color => $label) {
            $result[] = $this->_render($params, $color, $label);
        }

        return $this->app->element->applySeparators($params->get('separated_by'), $result);
    }

    /**
     * @param $params
     * @param $color
     * @param $label
     * @return string
     */
    protected function _render($params, $color, $label)
    {
        $height   = (int)$params->get('height', 26);
        $width    = (int)$params->get('width', 26);
        $template = $params->get('template', 'default');

        if ($layout = $this->getLayout($template . '.php')) {

            return $this->renderLayout($layout, array(
                'color'  => $color,
                'label'  => $label,
                'width'  => $width,
                'height' => $height,
                'isFile' => $this->_jbcolor->isFile($color)
            ));
        }

        return null;
    }

    /**
     * Show color items in admin panel
     * @return null|string
     */
    public function edit()
    {
        $colors     = explode("\n", $this->config->get('colors'));
        $path       = JString::trim($this->config->get('path', 'images'));
        $path       = !empty($path) ? $path : 'images';
        $colorItems = $this->_jbcolor->getColors($colors, $path);

        $checked = $this->get('option', array());
        $type    = $this->getInputType();

        if (empty($colorItems)) {
            return JText::_('JBZOO_JBCOLORS_ERROR_NO_ITEMS');
        }

        if (empty($checked)) {
            $defaults      = explode(',', $this->config->get('default_color'));
            $defaultColors = $this->_jbcolor->getDefaults($defaults);
            $checked       = array_merge($checked, $defaultColors);
        }

        if ($layout = $this->getLayout('edit.php')) {

            $containerId = uniqid('jbcolor-');

            return $this->renderLayout($layout, array(
                'type'        => $type,
                'checked'     => $checked,
                'colorItems'  => $colorItems,
                'containerId' => $containerId
            ));
        }

        return null;
    }

    /**
     * Get selected colors for render
     * @return array
     */
    public function getSelectedColors()
    {
        $colors     = explode("\n", $this->config->get('colors'));
        $path       = JString::trim($this->config->get('path', 'images'));
        $path       = !empty($path) ? $path : 'images';
        $colorItems = $this->_jbcolor->getColors($colors, $path);
        $selected   = $this->get('option', array());

        return array_intersect(array_flip($colorItems), $selected);
    }

    /**
     * Load config assets
     * @return self
     */
    public function loadConfigAssets()
    {
        JHtml::_('behavior.colorpicker');

        return parent::loadConfigAssets();
    }

    /**
     *  Clean data before bind into element
     * @param array $data
     * @return null | array
     */
    public function bindData($data = array())
    {
        if (isset($data['option'])) {
            foreach ($data['option'] as $key => $value) {
                $data['option'][$key] = $this->_jbcolor->clean($value);
            }

            parent::bindData($data);
        }

        return null;
    }

    /**
     * Get type for input
     * @return string
     */
    public function getInputType()
    {
        $type = (boolean)$this->config->get('multiplicity', 1);
        if (!$type) {
            return 'radio';
        }

        return 'checkbox';
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit();
    }

    /**
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $colors     = explode("\n", $this->config->get('colors'));
        $path       = JString::trim($this->config->get('path', 'images'));
        $path       = !empty($path) ? $path : 'images';
        $colorItems = $this->_jbcolor->getColors($colors, $path);

        $options  = array('required' => $params->get('required'));
        $messages = array('required' => 'Please choose an option.');

        $option = $this->app->validator
            ->create('foreach', $this->app->validator->create('string', $options, $messages), $options, $messages)
            ->clean($value->get('option'));

        foreach ($option as $key => $value) {
            if (!isset($colorItems[$value])) {
                unset($option[$key]);
            }
        }

        return compact('option');
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->colors();

        return parent::loadAssets();
    }
}