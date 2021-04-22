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

class JBCartElementOrderPrivacy extends JBCartElementOrder
{

    /**
     * renders front-end submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {   
        $privacyText    = JText::_('JBZOO_ELEMENT_ORDER_PRIVACY_LINK_PRIVACY_TEXT_SIMPLE');
        $policyText     = JText::_('JBZOO_ELEMENT_ORDER_PRIVACY_LINK_POLICY_TEXT_SIMPLE');

        if ($this->config->get('privacy')) {
            $privacyLink = JRoute::_('index.php?Itemid='.$this->config->get('privacy'));
            $privacyText = JText::sprintf('JBZOO_ELEMENT_ORDER_PRIVACY_LINK_PRIVACY_TEXT_FULL', $privacyLink);
        }

        if ($this->config->get('policy')) {
            $policyLink = JRoute::_('index.php?Itemid='.$this->config->get('policy'));
            $policyText = JText::sprintf('JBZOO_ELEMENT_ORDER_PRIVACY_LINK_POLICY_TEXT_FULL', $policyLink);
        }

        $text    = JText::sprintf('JBZOO_ELEMENT_ORDER_PRIVACY_TEXT_FULL', $privacyText, $policyText);

        $name    = $this->getControlName('option', true);
        $checked = in_array('agree', $this->get('option', array())) ? ' checked="checked"' : null;

        $html    = array('<div>');
        $html[]  = '<div><input id="'.$name.'" type="checkbox" name="' . $name . '" value="agree"'.$checked.' /><label for="'.$name.'">'.$text.'</label></div>';

        // workaround: if nothing is selected, the element is still being transfered
        $html[] = '<input type="hidden" name="' . $this->getControlName('check') . '" value="1" />';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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

        foreach ($option as $key => $value) {
            if (!in_array($value, ['agree'])) {
                unset($option[$key]);
            }
        }

        return compact('option');
    }
}
