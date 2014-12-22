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

/**
 * Class JBCSVItemPriceDiscount
 */
class JBCSVItemPriceDiscount extends JBCSVItemPrice
{
    /**
     * @return string|JBCartValue
     */
    public function toCSV()
    {
        return parent::toCSV()->data();
    }

    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = 0)
    {
        $isCurrency = $this->app->jbmoney->checkCurrency($value);

        if ($isCurrency) {
            $val   = $this->_element->data()->find('variations.' . $variant . '._discount.value');
            $value = $val . $value;
        }

        return array('value' => $value);
    }

}