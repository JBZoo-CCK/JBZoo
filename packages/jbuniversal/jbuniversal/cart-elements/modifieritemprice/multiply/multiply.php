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
 * Class JBCartElementModifierItemPriceMultiply
 */
class JBCartElementModifierItemPriceMultiply extends JBCartElementModifierItemPrice
{
    /**
     * @param \JBCartValue $value
     * @return mixed
     */
    public function edit(JBCartValue &$value)
    {
        if ($layout = $this->getLayout('edit.php')) {
            $this->modify($value);

            return self::renderLayout($layout, array(
                'rate'  => $this->_order->val($this->get('rate', 0)),
                'value' => $value
            ));
        }

        return null;
    }

    /**
     * @param JBCartValue   $value
     * @param Item          $item
     * @param JBCartVariant $variant
     * @return \JBCartValue
     */
    public function modify(JBCartValue $value, $item = null, $variant = null)
    {
        $rate = (float)$this->getRate($item, $variant)->val();

        return $value->multiply($rate);
    }

    /**
     * @param Item          $item
     * @param JBCartVariant $variant
     * @return \JBCartValue
     */
    public function getRate($item = null, $variant = null)
    {
        return $this->_order->val($this->config->get('value'));
    }
}
