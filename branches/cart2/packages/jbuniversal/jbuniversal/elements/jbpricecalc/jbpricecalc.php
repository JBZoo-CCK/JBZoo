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
        $this->_list     = $this->getVariantList($list, $this, array(
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
     * @param bool   $sendAjax
     */
    public function ajaxAddToCart($template = 'default', $quantity = 1, $values = array(), $sendAjax = true)
    {
        $jbAjax = $this->app->jbajax;

        $cart = JBCart::getInstance();

        $list = $this->getVariantByValues($values);
        $keys = array_keys($list);
        $key  = (int)end($keys);

        $this->set('default_variant', $key);

        $this->_template = $template;
        $this->_list     = $this->getVariantList($list, $this, array(
            'values'   => $values,
            'quantity' => $quantity,
            'currency' => $this->_config->get('cart.default_currency', JBCart::val()->cur())
        ));

        if ($this->inStock($quantity)) {

            $cart->addItem($this->_list->getCartData());
            $sendAjax && $jbAjax->send(array(), true);

        } else {

            $sendAjax && $jbAjax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY')), false);
        }

        $sendAjax && $jbAjax->send(array('added' => 0, 'message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')));
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
            $variant = new JBCartVariant($key, $this, $this->get('variations.' . $key));

            $result[$option['value']] = $option['name'] . ' <em>' . $variant->getTotal()->html() . '</em>';
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

            $key     = self::BASIC_VARIANT;
            $variant = $variations[$key];

            $this->set('default_variant', $key);
            $this->_list = $this->getVariantList(array($key => $variant));

            foreach ($this->_list->shift()->getElements() as $id => $element) {
                $value = JString::trim($element->getSearchData());

                if (JString::strlen($value) !== 0) {

                    $n = $this->isNumeric($value);
                    $d = $this->isDate($value);
                    $s = $value;

                    $data[$key . '_' . $id] = array(
                        'item_id'    => $item_id,
                        'element_id' => $this->identifier,
                        'param_id'   => $element->identifier,
                        'variant'    => $key,
                        'value_s'    => $s,
                        'value_n'    => $n,
                        'value_d'    => $d
                    );
                }
            }
        }
        unset($list,
            $this->_list,
            $this->_params,
            $this->params);

        return $data;
    }

    /**
     * Bind and validate data
     * @param array $data
     */
    public function bindData($data = array())
    {
        parent::bindData($data);
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
