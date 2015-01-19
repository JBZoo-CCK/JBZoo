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
 * Class JBCartElementModifierPriceCustom
 */
class JBCartElementModifierPriceCustom extends JBCartElementModifierPrice
{
    const MODE_ALL        = 'all';
    const MODE_TYPES      = 'types';
    const MODE_ITEMS      = 'item';
    const MODE_CATEGORIES = 'categories';

    /**
     * @param JBCartValue $value
     * @param \Item       $item
     * @return \JBCartValue
     */
    public function modify(JBCartValue $value, Item $item = null)
    {
        $rate = $this->getRate();
        if ($rate->isEmpty()) {
            return $value;
        }

        if ($this->isValid($item)) {
            $value->add($this->getRate());
        }

        return $value;
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val($this->config->get('value'));
    }

    /**
     * Check if item is valid to modify price
     * @param Item|mixed $item
     * @return bool
     */
    public function isValid(Item $item)
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
            if ($config->find('subject.type') == $item->getType()->id) {
                return true;
            }

        }

        return false;
    }

}
