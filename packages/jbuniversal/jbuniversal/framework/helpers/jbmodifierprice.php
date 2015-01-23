<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
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
