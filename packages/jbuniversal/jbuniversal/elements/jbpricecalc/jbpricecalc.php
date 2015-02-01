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
 * Class ElementJBPriceAdvance
 * The Price element for JBZoo
 */
class ElementJBPriceCalc extends ElementJBPrice implements iSubmittable
{
    /**
     * Get variant from $this->data() by values
     * MODE: OVERLAY
     * @param  array $values
     * @return array
     */
    public function getVariantByValues($values = array())
    {
        $data = $this->get('values', array());

        if (empty($values) || empty($data)) {
            return $values;
        }

        $variations = array();
        foreach ($data as $i => $value) {
            foreach ($value as $identifier => $fields) {
                if (isset($values[$identifier])) {

                    $diff = array_diff_assoc($fields, $values[$identifier]);
                    if (empty($diff)) {
                        $variations[$i] = $this->get('variations.' . $i, array());
                    }
                }
            }
        }

        return $variations;
    }

    /**
     * @param string $template
     * @param array  $values
     * @param string $currency
     */
    public function ajaxChangeVariant($template = 'default', $values = array(), $currency = '')
    {
        $list = $this->getVariantByValues($values);

        $keys = array_keys($list);
        $key  = (int)end($keys);

        $this->set('default_variant', $key);

        $this->_template = $template;
        $this->_list     = new JBCartVariantList($list, $this, array(
            'values'   => $values,
            'template' => $template,
            'currency' => $currency
        ));

        $this->app->jbajax->send($this->_list->renderVariant());
    }

    /**
     * Ajax add to cart method
     * @param string $template
     * @param int    $quantity
     * @param array  $values
     */
    public function ajaxAddToCart($template = 'default', $quantity = 1, $values = array())
    {
        $jbAjax = $this->app->jbajax;

        //Get variant by selected values
        $list = $this->getVariantByValues($values);

        $cart = JBCart::getInstance();
        $keys = array_keys($list);
        $key  = (int)end($keys);

        //Set the default option, which we have received, not saved. For correct calculation.
        $this->set('default_variant', $key);

        $this->_template = $template;
        $this->getVariantList($list, array(
            'values'   => $values,
            'quantity' => $quantity,
            'currency' => $this->_config->get('cart.default_currency', JBCart::val()->cur())
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
        $modifiers = (int)$this->config->get('show_modifiers', 1);

        if ($modifiers) {
            $options = parent::findOptions($identifier);
            $options = self::addModifiers($options);
        } else {
            $options = parent::selectedOptions($identifier);
        }

        return $options;
    }

    /**
     * Adds modifier value of each option
     * @param array $options
     * @return array
     */
    public function addModifiers($options = array())
    {
        if (empty($options)) {
            return $options;
        }
        $result = array();

        foreach ($options as $key => $option) {
            $variant = new JBCartVariant($key, $this->_list, $this->get('variations.' . $key));
            $total   = $variant->get('_value');

            $result[$option['value']] = $option['name'] . ' <em>' . $total->html() . '</em>';
        }

        return $result;
    }

    /**
     * Get element search data for sku table
     * @return array
     */
    public function getIndexData()
    {
        $variations = $this->get('variations');
        $item_id    = $this->getItem()->id;

        $data = array();
        if (!empty($variations)) {
            $elements = array_merge(
                (array)$this->_position->loadElements(JBCart::CONFIG_PRICE),
                (array)$this->_position->loadElements(JBCart::CONFIG_PRICE_TMPL)
            );
            $this->set('default_variant', self::BASIC_VARIANT);

            $list     = $this->getVariantList();
            $variant  = $list->shift();
            $elements = array_merge((array)$elements, (array)$variant->getElements());
            foreach ($elements as $id => $element) {
                if ($element->isSystemTmpl()) {
                    $element->setJBPrice($this);
                    $element->config->set('_variant', self::BASIC_VARIANT);
                }

                $value = $element->getSearchData();

                if (!empty($value)) {
                    $d = $s = $n = null;
                    if ($value instanceof JBCartValue) {
                        $s = $value->data(true);
                        $n = $value->val();
                    } elseif (JSTring::strlen($value) !== 0) {
                        $s = $value;
                        $n = $this->isNumeric($value) ? $value : null;
                        $d = $this->isDate($value) ? $value : null;
                    }

                    $data[self::BASIC_VARIANT . $id] = array(
                        'item_id'    => $item_id,
                        'element_id' => $this->identifier,
                        'param_id'   => $id,
                        'value_s'    => $s,
                        'value_n'    => $n,
                        'value_d'    => $d,
                        'variant'    => self::BASIC_VARIANT
                    );
                }
            }
        }
        $this->_list = $this->_params = $this->params = null;

        return $data;
    }

    /**
     * @param array $variations
     * @param array $options
     * @param bool  $reload
     * @return \JBCartVariantList
     */
    public function getVariantList($variations = array(), $options = array(), $reload = false)
    {
        if ($reload) {
            $this->unsetList();
        }

        if (!$this->_list instanceof JBCartVariantList) {
            if (empty($variations)) {
                $variations = array(
                    self::BASIC_VARIANT       => $this->get('variations.' . self::BASIC_VARIANT),
                    self::defaultVariantKey() => $this->get('variations.' . self::defaultVariantKey())
                );
            }

            $options = array_merge(array(
                'values'   => $this->quickSearch(array_keys($variations), 'value', false, 'values'),
                'currency' => $this->_config->get('cart.default_currency', JBCart::val()->cur())
            ), (array)$options);

            $this->_list = new JBCartVariantList($variations, $this, $options);
        }

        return $this->_list;
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadEditAssets()
    {
        parent::loadEditAssets();
        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator/calc.js');
        }

        return $this;
    }

    /**
     * @return $this|void
     */
    public function loadAssets()
    {
        parent::loadAssets();
        $this->app->jbassets->less('elements:jbpricecalc/assets/less/jbpricecalc.less');
    }
}
