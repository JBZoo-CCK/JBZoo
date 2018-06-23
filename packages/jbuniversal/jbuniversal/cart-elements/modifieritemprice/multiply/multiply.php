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
 * Class JBCartElementModifierItemPriceMultiply
 */
class JBCartElementModifierItemPriceMultiply extends JBCartElementModifierItemPrice
{
    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        if ($this->_isValid()) {
            $rate = $this->app->jbvars->number($this->config->get('rate', 0));
            if ($rate) {
                $multiplier = ($rate - 1) * 100;

                return $this->_order->val($multiplier, '%');
            }
        }

        return $this->_order->val(0, '%');
    }
}
