<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmailOrderFields
 */
class JBCartElementEmailFields extends JBCartElementEmail
{
    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $fields = $this->_getFields();
        return !empty($fields);
    }

    /**
     * @return array
     */
    protected function _getFields()
    {
        $orderFields = $this->_order->getFields();
        $selected    = $this->config->get('fields', array());

        $result = array();
        foreach ($selected as $elementId) {
            if ($element = $orderFields->get($elementId)) {
                $result[$elementId] = $element;
            }
        }

        return $result;
    }

}
