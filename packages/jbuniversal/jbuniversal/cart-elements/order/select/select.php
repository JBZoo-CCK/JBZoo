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


class JBCartElementOrderSelect extends JBCartElementOrderOption
{

    /**
     * renders front-end submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        // init vars
        $optionList = $this->config->get('option', array());
        $multiple   = $this->config->get('multiple');
        $default    = $this->getUserState($params->get('user_field'));
        $name       = $this->getName();

        if (count($optionList)) {

            // set default, if item is new
            if (!empty($default)) {
                $default = (array)$default;
                $this->set('option', $default);
            }

            $options = array();
            if (!$multiple) {
                $options[] = $this->app->html->_('select.option', '', '-' . JText::sprintf('Select %s', $name) . '-');
            }

            foreach ($optionList as $option) {
                $options[] = $this->app->html->_('select.option', $option['value'], $option['name']);
            }

            $style  = $multiple ? 'multiple="multiple" size="5"' : '';
            $html[] = $this->app->html->_('select.genericlist', $options, $this->getControlName('option', true), $style, 'value', 'text', $this->get('option', array()));

            // workaround: if nothing is selected, the element is still being transfered
            $html[] = '<input type="hidden" name="' . $this->getControlName('select') . '" value="1" />';

            return implode(PHP_EOL, $html);
        }

        return JText::_("There are no options to choose from.");
    }

}
