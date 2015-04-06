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
 * Class JBCSVItemPricePrice_sku
 */
class JBCSVItemPricePrice_sku extends JBCSVItem
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

            return isset($basic['sku']) ? $basic['sku'] : $this->_item->id;
        }

        return $this->_item->id;
    }

    /**
     * @param      $value
     * @param  int $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = 0)
    {
        /** @type JBCartVariant $var */
        $data = array(
            'value' => (null !== $value && $value !== '' ? $value : $this->_item->id)
        );
        $var  = $this->_element->getVariant($variant);

        if ($var && $var->has('_sku')) {
            $var->get('_sku')->bindData($data);
            $this->_element->bindVariant($var);
        }

        return $this->_item;
    }

}