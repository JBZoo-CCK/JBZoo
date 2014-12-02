<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
class JBCartElementOrderEmail extends JBCartElementOrder
{

    protected function _hasValue($params = array()) {
        $value = $this->get('value');

        return $this->_containsEmail($value);
    }


    protected function _containsEmail($text) {
        return preg_match('/[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}/i', $text);
    }


    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return $this->app->html->_(
            'control.text',
            $this->getControlName('value'),
            $this->get('value', $this->config->get('default')),
            'size="60" maxlength="255" id="order-' . $this->identifier . '"'
        );
    }


    public function validateSubmission($value, $params)
    {
        $params = $this->app->data->create($params);
        $value  = $this->app->data->create($value);

        return array(
            'value' => $this->app->validator
                ->create('email', array('required' => (int)$params->get('required')))
                ->clean($value->get('value'))
        );
    }

}
