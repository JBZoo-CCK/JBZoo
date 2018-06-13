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
        $optionList = $this->config->get('option', array());
        $default = $this->getUserState($params->get('user_field'));

        if (count($optionList)) {

            // set default, if item is new
            if (!empty($default)) {
                $this->set('option', array($default));
            }

            $options = array();
            foreach ($optionList as $option) {
                $options[] = $this->app->html->_('select.option', $option['value'], $option['name']);
            }

            $option = $this->get('option', array());

            return $this->app->html->_('select.radiolist', $options, $this->getControlName('option', true), null, 'value', 'text', (isset($option[0]) ? $option[0] : null));
        }

        return JText::_("There are no options to choose from.");
    }

}
