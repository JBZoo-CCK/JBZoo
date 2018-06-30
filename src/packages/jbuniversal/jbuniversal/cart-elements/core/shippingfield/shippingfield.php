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
