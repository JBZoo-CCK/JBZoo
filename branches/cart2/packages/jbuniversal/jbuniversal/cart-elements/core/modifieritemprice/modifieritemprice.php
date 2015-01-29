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
     * @type ElementJBPrice
     */
    protected $_price = null;

    /**
     * @type JSONData
     */
    protected $_itemData = null;

    /**
     * @param ElementJBPrice $priceElement
     */
    public function setPriceElement(ElementJBPrice $priceElement)
    {
        $this->_price = $priceElement;
    }

    /**
     * @param Array $itemData
     */
    public function setItemData($itemData = array())
    {
        $this->_itemData = $this->app->data->create($itemData);
    }

    /**
     * @param JBCartValue $value
     * @return JBCartValue
     */
    public function modify(JBCartValue $value)
    {
        return $value->add($this->getRate());
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        if ($this->_isValid()) {
            return $this->_order->val($this->config->get('rate'));
        }

        return $this->_order->val(0);
    }

    /**
     * Check if item is valid to modify price
     * @return bool
     */
    protected function _isValid()
    {
        $item = $this->_price->getItem();
        if (!$item) {
            return false;
        }

        if ($this->app->jbenv->isSite() && !$this->canAccess()) {
            return false;
        }

        $config = $this->config;
        $mode   = $config->find('target.mode', self::MODE_ALL);

        if ($mode == self::MODE_ALL) {
            return true;

        } elseif ($mode == self::MODE_ITEMS) {
            if ($config->find('target.item_id') == $item->id) {
                return true;
            }

        } elseif ($mode == self::MODE_CATEGORIES) {
            if (in_array((int)$config->find('target.category'), $item->getRelatedCategoryIds(true))) {
                return true;
            }

        } elseif ($mode == self::MODE_TYPES) {
            if ($config->find('target.type') == $item->type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            $rate   = $this->_order->val($this->get('rate', 0));
            $params = $this->app->data->create($params);

            return self::renderLayout($layout, array(
                'rate'   => $rate,
                'params' => $params,
            ));
        }

        return null;
    }

    /**
     * @return JSONData
     */
    public function getOrderData()
    {
        $this->set('rate', $this->getRate()->data());
        return $this->data();
    }

    /**
     * Load elements css/js config assets
     * @return $this
     */
    public function loadConfigAssets()
    {
        parent::loadConfigAssets();

        // load zoo frontend language file
        $this->app->system->language->load('com_zoo');

        $this->app->html->_('behavior.modal', 'a.modal');
        $this->app->jbassets->css('fields:zooapplication.css');
        $this->app->jbassets->js('fields:zooapplication.js');
    }
}

/**
 * Class JBCartElementModifierItemPriceException
 */
class JBCartElementModifierItemPriceException extends JBCartElementException
{
}
