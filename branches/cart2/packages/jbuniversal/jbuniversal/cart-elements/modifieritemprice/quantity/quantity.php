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
     * @return JBCartValue
     */
    public function getRate()
    {
        $isAjax   = $this->app->jbrequest->isAjax();
        $isChange = !$this->app->jbrequest->is('method', 'ajaxChangeVariant');
        $isValid  = $this->_isValid();
        $jbPrice  = $this->_price;
        $itemData = $this->_itemData;
        $quantity = (float)$this->config->get('quantity', 0);

        if ($isAjax && $isValid && $isChange) {

            $list = $jbPrice->getList();
            if (($itemData && $itemData->get('quantity', 1) >= $quantity) ||
                (!$itemData && $list->quantity >= $quantity)
            ) {
                return $this->_order->val($this->config->get('rate'));
            }
        }

        return $this->_order->val(0);
    }
}
