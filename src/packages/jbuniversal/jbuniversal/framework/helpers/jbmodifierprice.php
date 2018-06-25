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
 * Class JBModifierPriceHelper
 */
class JBModifierPriceHelper extends AppHelper
{

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        $list = $this->app->jbcartposition->loadPositions(JBCart::CONFIG_MODIFIER_ORDER_PRICE);
        if (isset($list[JBCart::DEFAULT_POSITION]) && !empty($list[JBCart::DEFAULT_POSITION])) {
            return $list[JBCart::DEFAULT_POSITION];
        }

        return array();
    }

}
