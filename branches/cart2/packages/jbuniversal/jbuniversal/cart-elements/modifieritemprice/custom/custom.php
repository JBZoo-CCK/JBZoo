<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementModifierItemPriceCustom
 */
class JBCartElementModifierItemPriceCustom extends JBCartElementModifierItemPrice
{
    const MODE_ALL        = 'all';
    const MODE_TYPES      = 'types';
    const MODE_ITEMS      = 'item';
    const MODE_CATEGORIES = 'categories';

    /**
     * @param \JBCartValue $value
     * @return mixed
     */
    public function edit(JBCartValue &$value)
    {
        if ($layout = $this->getLayout('edit.php')) {
            $rate = $this->_order->val($this->get('rate', 0));
            $value->add($rate);

            return self::renderLayout($layout, array(
                'rate'  => $rate,
                'value' => $value
            ));
        }

        return null;
    }

    /**
     * @param JBCartValue   $value
     * @param Item          $item
     * @param JBCartVariant $variant
     * @return \JBCartValue
     */
    public function modify(JBCartValue $value, $item = null, $variant = null)
    {
        $rate = $this->getRate($item, $variant);

        return $value->add($rate);
    }

    /**
     * @param Item          $item
     * @param JBCartVariant $variant
     * @return \JBCartValue
     */
    public function getRate($item = null, $variant = null)
    {
        if ($this->_isValid($item)) {
            return $this->_order->val($this->config->get('value'));
        }

        return $this->_order->val();
    }

    /**
     * Check if item is valid to modify price
     * @param Item $item
     * @return bool
     */
    private function _isValid($item = null)
    {
        $config = $this->config;
        $mode   = $config->find('subject.mode') ? $config->find('subject.mode') : self::MODE_ALL;

        if ($mode == self::MODE_ALL) {
            return true;

        } elseif ($mode == self::MODE_ITEMS) {
            if ($config->find('subject.item_id') == $item->id) {
                return true;
            }

        } elseif ($mode == self::MODE_CATEGORIES) {
            if (in_array((int)$config->find('subject.category'), $item->getRelatedCategoryIds(true))) {
                return true;
            }

        } elseif ($mode == self::MODE_TYPES) {
            if ($config->find('subject.type') == $item->type) {
                return true;
            }

        }

        return false;
    }

}
