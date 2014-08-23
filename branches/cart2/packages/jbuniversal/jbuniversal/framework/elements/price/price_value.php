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
 * Class JBCSVItemPricePrice_base
 */
class JBCSVItemPricePrice_value extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $priceElements = $this->_element;
        if (!empty($priceElements)) {
            $data = $priceElements->data();
            if (!empty($data)) {
                $basic = $data['basic'];
            }
            return isset($basic['value']) ? $basic['value'] : '';
        }
        return null;
    }

    /**
     * @param  $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = null)
    {
        // save data
        $data = $this->_element->data();

        if (!isset($variant)) {
            $data['basic']['_value'] = $this->_getFloat($value);

        } elseif ($variant >= 0) {
            $data['variations'][$variant]['_value'] = $this->_getFloat($value);
        }

        $this->_element->bindData($data);

        return $this->_item;
    }

}