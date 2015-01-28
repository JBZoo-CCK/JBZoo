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
 * Class JBCartElementModifierItemPriceQuantity
 */
class JBCartElementModifierItemPriceQuantity extends JBCartElementModifierItemPrice
{
    /**
     * @param \JBCartValue $value
     * @param  JSONData|array    $data
     * @return mixed
     */
    public function edit(JBCartValue &$value, $data = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            $rate = $this->_order->val($this->get('rate', 0));
            $value->add($rate);

            return self::renderLayout($layout, array(
                'rate'  => $rate,
                'value' => $value
            ));
        }

        return null;
    }

    /**
     * @param JBCartValue    $value
     * @param ElementJBPrice $jbPrice
     * @param array          $session_data
     * @return \JBCartValue
     */
    public function modify(JBCartValue $value, $jbPrice = null, $session_data = null)
    {
        $rate = $this->getRate($jbPrice, $session_data);

        return $value->add($rate);
    }

    /**
     * @param ElementJBPrice $jbPrice
     * @param array          $session_data
     * @return \JBCartValue
     */
    public function getRate($jbPrice = null, $session_data = null)
    {
        if ($this->app->jbrequest->isAjax() && $this->_isValid($jbPrice->getItem())) {
            $quantity = (float)$this->config->get('quantity', 0);

            $list = $jbPrice->getVariantList();
            if (($session_data && $session_data['quantity'] >= $quantity) ||
                (!$session_data && $list->quantity >= $quantity)
            ) {
                return $this->_order->val($this->config->get('value'));
            }
        }

        return $this->_order->val();
    }
}
