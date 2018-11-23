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
 * Class JBCartElementOrderFieldText
 */
class JBCartElementOrderOption extends JBCartElementOrder
{

    /**
     * Checks if there is any value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = [])
    {
        foreach ($this->get('option', []) as $option) {
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
    public function render($params = [])
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
    public function edit($params = [])
    {
        $selected_options = $this->get('option', []);

        $options = [];
        foreach ($this->config->get('option', []) as $option) {
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
        $params = $this->app->data->create($params);
        $value = $this->app->data->create($value);
        $options = ['required' => $params->get('required')];
        $messages = ['required' => 'Please choose an option.'];

        $option = $this->app->validator
            ->create('foreach', $this->app->validator->create('string', $options, $messages), $options, $messages)
            ->clean($value->get('option'));

        $config_options = array_map(function ($o) {
            return @$o["value"];
        }, $this->config->get('option', []));

        foreach ($option as $key => $value) {
            if (!in_array($value, $config_options)) {
                unset($option[$key]);
            }
        }

        return compact('option');
    }

}
