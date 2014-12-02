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

App::getInstance('zoo')->loader->register('JBCartElementOrderOption', 'cart-elements:order/option/option.php');

/**
 * Class JBCartElementOrderFieldText
 */
class JBCartElementOrderRadio extends JBCartElementOrderOption
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
        $default             = $this->config->get('default');

        if (count($options_from_config)) {

            // set default, if item is new
            if ($default != '' && $this->_item != null && $this->_item->id == 0) {
                $this->set('option', array($default));
            }

            $options = array();
            foreach ($options_from_config as $option) {
                $options[] = $this->app->html->_('select.option', $option['value'], $option['name']);
            }

            $option = $this->get('option', array());

            return $this->app->html->_('select.radiolist', $options, $this->getControlName('option', true), null, 'value', 'text', (isset($option[0]) ? $option[0] : null));
        }

        return JText::_("There are no options to choose from.");
    }


}