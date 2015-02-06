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
 * Class JBCartElementOrderTextarea
 */
class JBCartElementOrderTextarea extends JBCartElementOrder
{

    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $default = $this->getUserState($params->get('user_field'));

        return $this->app->html->_(
            'control.textarea',
            $this->getControlName('value'),
            $this->get('value', $default),
            'id="' . $this->htmlId() . '"'
        );
    }

}