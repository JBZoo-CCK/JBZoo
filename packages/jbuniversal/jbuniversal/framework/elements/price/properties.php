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
 * Class JBCSVItemPriceProperties
 */
class JBCSVItemPriceProperties extends JBCSVItemPrice
{
    /**
     * @return mixed|JBCartValue
     */
    public function toCSV()
    {
        $values = (array)$this->_param->data();
        $result = array();
        foreach ($values as $key => $value) {
            $value = JString::trim($value);

            if (!empty($value)) {
                $result[$key] = $value;
            }
        }

        return implode(JBCSVItem::SEP_CELL, $result);
    }

    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = null)
    {
        $value  = JString::trim((string)$value);
        $charts = explode(JBCSVItem::SEP_CELL, $value);

        return array(
            'height' => isset($charts[0]) ? $charts[0] : 0,
            'length' => isset($charts[1]) ? $charts[1] : 0,
            'width'  => isset($charts[2]) ? $charts[2] : 0
        );
    }
}