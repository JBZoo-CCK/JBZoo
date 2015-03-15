<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementOrder
 */
abstract class JBCartElementOrder extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_ORDER;

    /**
     * Render shipping in order
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
            ));
        }

        return false;
    }

    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $value = $this->getUserState($params->get('user_field'));

        return $this->app->html->_(
            'control.text',
            $this->getControlName('value'),
            $this->get('value', $value),
            'size="60" maxlength="255" id="' . $this->htmlId() . '"'
        );
    }

    /**
     * Get value from user profile
     * @param         $key
     * @param  string $default
     * @return mixed
     */
    public function getUserState($key, $default = '')
    {
        $user = JFactory::getUser();
        if (empty($default)) {
            $default = $this->config->get('default');
        }

        if ($user->guest) {
            return $default;
        }

        $value = null;
        if (property_exists($user, $key)) {
            $value = $user->$key;

            if (empty($value) || !isset($value)) {
                $value = $default;
            }
        }

        return $value;
    }
}

/**
 * Class JBCartElementOrderException
 */
class JBCartElementOrderException extends JBCartElementException
{
}
