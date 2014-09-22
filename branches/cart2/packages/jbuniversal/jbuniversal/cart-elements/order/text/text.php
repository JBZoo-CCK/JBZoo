<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCartElementOrderFieldText
 */
class JBCartElementOrderText extends JBCartElementOrder
{

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

    /**
     * @param $params
     * @return mixed
     */
    public function edit($params = array())
    {
        return $this->get('value');
    }

}
