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
 * Class JBCartElementCurrencyPercent
 */
class JBCartElementCurrencyPercent extends JBCartElementCurrency
{
    /**
     * @param null $currency
     * @return array|void
     */
    public function _loadData($currency = null)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return JBCartValue::PERCENT;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return JText::_('JBZOO_CART_CURRENCY_PERCENT');
    }

    public function checkCurrency($currency)
    {
        return $currency == $this->getCode();
    }

}
