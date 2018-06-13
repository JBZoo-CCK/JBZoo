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
class JBCartElementOrderEmail extends JBCartElementOrder
{

    /**
     * @param array $params
     * @return int
     */
    protected function _hasValue($params = array())
    {
        $value = $this->get('value');

        return $this->_containsEmail($value);
    }

    /**
     * @param $text
     * @return int
     */
    protected function _containsEmail($text)
    {
        return preg_match('/[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}/i', $text);
    }

    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $default = $this->getUserState($params->get('user_field'));
        return $this->app->html->_(
            'control.text',
            $this->getControlName('value'),
            $this->get('value', $default),
            'size="60" maxlength="255" id="' . $this->htmlId() . '"'
        );
    }

    /**
     * @param $value
     * @param $params
     * @return array
     */
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

    /**
     * @param array $params
     * @return mixed|string
     */
    public function edit($params = array())
    {
        $mailto = $this->get('value');

        if ($mailto) {
            return sprintf('<a href="mailto:%s">%s</a>', $mailto, $mailto);
        }

        return '-';
    }
}
