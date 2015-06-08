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

// register ElementOption class
App::getInstance('zoo')->loader->register('JBCartElementOrderOption', 'cart-elements:order/option/option.php');


class JBCartElementOrderCheckbox extends JBCartElementOrderOption
{

    /**
     * renders front-end submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        // init vars
        $options_from_config = $this->config->get('option', array());
        $default = $this->getUserState($params->get('user_field'));

        if (count($options_from_config)) {

            // set default, if item is new
            if (!empty($default)) {
                $default = array($default);
            } else {
                $default = array();
            }

            $selected_options = $this->get('option', $default);

            $i    = 0;
            $html = array('<div>');
            foreach ($options_from_config as $option) {
                $name    = $this->getControlName('option', true);
                $checked = in_array($option['value'], $selected_options) ? ' checked="checked"' : null;
                $html[]  = '<div><input id="' . $name . $i . '" type="checkbox" name="' . $name . '" value="' . $option['value'] . '"' . $checked . ' /><label for="' . $name . $i++ . '">' . $option['name'] . '</label></div>';
            }
            // workaround: if nothing is selected, the element is still being transfered
            $html[] = '<input type="hidden" name="' . $this->getControlName('check') . '" value="1" />';
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return JText::_("There are no options to choose from.");
    }

}
