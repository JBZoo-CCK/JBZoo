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
 * Class JBCartElementModifierItemPrice
 */
abstract class JBCartElementModifierItemPrice extends JBCartElement
{
    const MODE_ALL        = 'all';
    const MODE_TYPES      = 'types';
    const MODE_ITEMS      = 'item';
    const MODE_CATEGORIES = 'categories';

    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIER_ITEM_PRICE;

    /**
     * @param JBCartValue $summa
     * @param Item       $item
     * @return JBCartValue
     */
    //abstract public function modify(JBCartValue $summa, Item $item = null);

    /**
     * @param ElementJBPrice $jbPrice
     * @param array          $session_data
     * @return \JBCartValue
     */
    public function getRate($jbPrice = null, $session_data = null)
    {
        if ($this->_isValid($jbPrice->getItem())) {
            return $this->_order->val($this->config->get('value'));
        }

        return $this->_order->val();
    }

    /**
     * Check if item is valid to modify price
     * @param Item $item
     * @return bool
     */
    protected function _isValid($item = null)
    {
        if (!$item) {
            return false;
        }

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

/**
 * Class JBCartElementModifierItemPriceException
 */
class JBCartElementModifierItemPriceException extends JBCartElementException
{
}
