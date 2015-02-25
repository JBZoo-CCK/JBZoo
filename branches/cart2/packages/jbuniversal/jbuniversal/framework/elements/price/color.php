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

require_once dirname(__FILE__) . '/price.php';

/**
 * Class JBCSVItemPriceColor
 */
class JBCSVItemPriceColor extends JBCSVItemPrice
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        return parent::toCSV();
    }

    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = 0)
    {
        $value = key($this->app->jbcolor->parse($value));
        
        return array('value' => $value);
    }

}