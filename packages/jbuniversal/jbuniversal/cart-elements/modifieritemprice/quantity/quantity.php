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
