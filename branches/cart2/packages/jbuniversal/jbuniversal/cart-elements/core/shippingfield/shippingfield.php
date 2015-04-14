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
 * Class JBCartElementShippingField
 */
abstract class JBCartElementShippingField extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_SHIPPINGFIELD;

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

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

        return null;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return false;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $isShippingField = true; // if shipping doesn't exist = true!
        if ($shipping = $this->getOrder()->getShipping()) {
            $isShippingField = $shipping->hasShippingField($this->identifier);
        }

        if ($isShippingField) {
            return parent::validateSubmission($value, $params);
        }

        return array();
    }
}

/**
 * Class JBCartElementShippingFieldException
 */
class JBCartElementShippingFieldException extends JBCartElementException
{
}
