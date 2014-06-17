<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCSVItemCoreSku
 */
class JBCSVItemCoreSku extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        $priceElements = $this->_item->getElementsByType('jbpriceadvance');

        if (!empty($priceElements)) {

            reset($priceElements);
            $skuElement = current($priceElements);

            $data = $skuElement->getIndexData(true);
            if (!empty($data)) {
                reset($data);
                $basic = current($data);
            }

            return isset($basic['sku']) ? $basic['sku'] : $this->_item->id;
        }

        return $this->_item->id;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        return $this->_item;
    }

}
