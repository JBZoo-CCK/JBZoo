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

App::getInstance('zoo')->loader->register('ElementJBPrice', 'elements:jbprice/jbprice.php');

/**
 * Class ElementJBPricePlain
 * The Price element for JBZoo
 */
class ElementJBPricePlain extends ElementJBPrice implements iSubmittable
{
    /**
     * Get variant from $this->data() by values
     * MODE: DEFAULT
     * @param  array $values values from front end
     * @return array
     */
    public function getVariantByValues($values = array())
    {
        $data = $this->get('values', array());

        if (empty($values) || empty($data)) {
            return $values;
        }

        $variations = $this->get('variations', array());
        unset($variations[self::BASIC_VARIANT]);
        foreach ($data as $i => $value) {
            foreach ($values as $identifier => $fields) {

                $valError = false;
                $idError  = false;

                if (!isset($value[$identifier]) ||
                    count($values) !== count($value)
                ) {
                    $idError = true;
                }

                if ($idError === false) {
                    if (isset($fields['value']) && (JString::strlen($fields['value']) === 0)) {
                        unset($fields);
                    }

                    if (!empty($fields)) {
                        $diff = array_diff_assoc($fields, $value[$identifier]);
                    }

                    if (!empty($diff)) {
                        $valError = true;
                    }
                }

                if ($idError === true || $valError === true) {
                    unset($variations[$i]);
                }
            }
        }

        return $variations;
    }

    /**
     * @param string $template - need to get render params for elements
     * @param array  $values   - selected values
     * @param string $currency
     */
    public function ajaxChangeVariant($template = 'default', $values = array(), $currency = '')
    {
        $list = $this->getVariantByValues($values);
        $key  = (int)(JString::strlen(key($list)) > 0 ? key($list) : self::BASIC_VARIANT);

        $this->set('default_variant', $key);

        $this->_template = $template;
        $this->_list     = new JBCartVariantList($list, $this, array(
            'values'   => $values,
            'template' => $template,
            'currency' => !empty($currency) ? $currency : $this->currency()
        ));

        $this->app->jbajax->send($this->_list->renderVariant());
    }

    /**
     * Ajax add to cart method
     * @param string $template
     * @param int    $quantity
     * @param array  $values
     * @param string $currency
     */
    public function ajaxAddToCart($template = 'default', $quantity = 1, $values = array(), $currency = '')
    {
        $jbAjax = $this->app->jbajax;

        //Get variant by selected values
        $list = $this->getVariantByValues($values);

        $cart = JBCart::getInstance();
        $key  = (int)(JString::strlen(key($list)) > 0 ? key($list) : self::BASIC_VARIANT);

        //Set the default option, which we have received, not saved. For correct calculation.
        $this->set('default_variant', $key);

        $this->_template = $template;
        $this->getVariantList($list, array(
            'values'   => $values,
            'quantity' => $quantity,
            'currency' => !empty($currency) ? $currency : $this->currency()
        ), true);
        $session_key = $this->_list->getSessionKey();

        $data = $cart->getItem($session_key);
        if (!empty($data)) {
            $quantity += $data['quantity'];
        }

        //Check balance
        if ($this->inStock($quantity, $key)) {
            $cart
                ->addItem($this->_list->getCartData())
                ->updateItem($cart->getItem($session_key));

            $jbAjax->send(array(), true);

        } else {
            $jbAjax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY')), false);
        }

        $jbAjax->send(array('added' => 0, 'message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')));
    }

    /**
     * Remove from cart method
     * @param string $key - Session key
     * @return mixed
     */
    public function ajaxRemoveFromCart($key = null)
    {
        if (!(int)$this->config->get('remove_variant', 0)) {
            $key = null;
        }
        $item_id = $this->getItem()->id;
        $result  = JBCart::getInstance()->remove($item_id, $this->identifier, $key);

        $this->app->jbajax->send(array('removed' => $result));
    }

    /**
     * @param $identifier
     * @return array|void
     */
    public function elementOptions($identifier)
    {
        return parent::selectedOptions($identifier);
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadEditAssets()
    {
        parent::loadEditAssets();

        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator/plain.js');
        }

        return $this;
    }
}