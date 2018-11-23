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

App::getInstance('zoo')->loader->register('JBCartElementPriceOption', 'cart-elements:price/option/option.php');

/**
 * Class JBCartElementPriceText
 */
class JBCartElementPriceText extends JBCartElementPriceOption
{
    /**
     * Check if element has options.
     * @return bool
     */
    public function hasOptions()
    {
        return false;
    }
}
