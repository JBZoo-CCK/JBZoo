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
 * Class JBCartElementCurrencyManual
 */
class JBCartElementCurrencyManual extends JBCartElementCurrency
{
    /**
     * @param null $currency
     * @return array|void
     */
    public function _loadData($currency = null)
    {
        $code = $this->getCode();
        $rate = $this->config->get('rate', 1);

        if ($currency && $currency != $code) {
            return array();
        }

        if ($code && $rate > 0) {
            return array($code => $rate);
        }

        return array();
    }

}
