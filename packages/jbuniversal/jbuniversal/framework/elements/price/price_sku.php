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