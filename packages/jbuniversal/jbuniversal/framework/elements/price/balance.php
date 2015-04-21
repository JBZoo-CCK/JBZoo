<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/price.php';

/**
 * Class JBCSVItemPriceBalance
 */
class JBCSVItemPriceBalance extends JBCSVItemPrice
{
    /**
     * @param       $value
     * @param  null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = null)
    {
        $value = JString::trim($value);

        return array('value' => $value);
    }
}