<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementOrderFieldText
 */
class JBCartElementOrderOption extends JBCartElementOrder
{

    /**
     * Checks if there is any value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        foreach ($this->get('option', array()) as $option) {
            if (!empty($option)) {
                return true;
            }
        }

        return false;
    }

    /**
     * renders the element
     * @param array $params
     * @return mixed|string
     */
    public function render($params = array())
    {
        return $this->edit($params);
    }

    /**
     * renders element's options in the form
     * @param      $var
     * @param      $num
     * @param null $name
     * @param null $value
     * @return string
     */
    public function editOption($var, $num, $name = null, $value = null)
    {
        $path = $this->app->path->path("cart-elements:order/option/tmpl/editoption.php");
        return $this->renderLayout($path, compact('var', 'num', 'name', 'value'));
    }

    /**
     * Get parameter form object to render input form.
     * @param string $groupData
     * @return AppParameterForm
     */
    public function getConfigForm($groupData = self::DEFAULT_GROUP)
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__));
    }

    /**
     * adds js and css
     * @return Void
     */
    public function loadConfigAssets()
    {
        $this->app->jbassets->js('cart-elements:order/option/assets/option.js');
        $this->app->jbassets->less('cart-elements:order/option/assets/option.less');
        return parent::loadConfigAssets();
    }

    /**
     * for viewing in the admin panel
     * @param array $params
     * @return string
     */
    public function edit($params = array())
    {
        $selected_options = $this->get('option', array());

        $options = array();
        foreach ($this->config->get('option', array()) as $option) {
            if (in_array($option['value'], $selected_options)) {
                $options[] = $option['name'];
            }
        }

        if ($options) {
            if (count($options) > 1) {

                $html[] = '<ul>';
                foreach ($options as $option) {
                    $html[] = '<li>' . $option . '</li>';
                }
                $html[] = '</ul>';

                return implode(PHP_EOL, $html);

            }

            return $options[0];
        }

        return ' - ';
    }

    /**
     * validates submission
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $params   = $this->app->data->create($params);
        $value    = $this->app->data->create($value);
        $options  = array('required' => $params->get('required'));
        $messages = array('required' => 'Please choose an option.');

        $option = $this->app->validator
            ->create('foreach', $this->app->validator->create('string', $options, $messages), $options, $messages)
            ->clean($value->get('option'));

        $config_options = array_map(create_function('$o', 'return @$o["value"];'), $this->config->get('option', array()));
        foreach ($option as $key => $value) {
            if (!in_array($value, $config_options)) {
                unset($option[$key]);
            }
        }

        return compact('option');
    }

}
