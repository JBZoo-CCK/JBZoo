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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->isOverlay = true;
    }

    /**
     * Get variant from $this->data() by values
     * MODE: OVERLAY
     * @param  array $values
     * @return array
     */
    public function getVariantByValues($values = array())
    {
        $data = (array)$this->get('values', array());

        if (empty($values) || empty($data)) {
            return (array)$values;
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

        return (array)$variations;
    }

    /**
     * @param string $template
     * @param array  $values
     */
    public function ajaxChangeVariant($template = 'default', $values = array())
    {
        $list = $this->getVariantByValues($values);

        $keys = array_keys($list);
        $key  = (int)end($keys);

        $this->setDefault($key);
        $this->_template = $template;

        if($this->_list instanceof JBCartVariantList) {
            $this->_list->clear();
        }

        $this->getList($list, array(
            'default'  => $key,
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
        $this->setDefault($key);

        $this->_template = $template;
        $this->getList($list, array(
            'values'   => $values,
            'quantity' => $quantity,
            'currency' => !empty($currency) ? $currency : $this->currency()
        ));
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
     * //TODO Hard function
     * Get all options for element.
     * Used in element like select, color, radio etc.
     * @param string $id
     * @return array
     */
    public function findOptions($id)
    {
        if (empty($id)) {
            return array();
        }

        return $this->get('selected.' . $id, array());
    }

    /**
     * Prepare element data to push into JBHtmlHelper - select, radio etc.
     * @param $identifier
     * @return array
     */
    public function selectedOptions($identifier)
    {
        return $this->findOptions($identifier);
    }

    /**
     * @param $identifier
     * @return array|void
     */
    public function elementOptions($identifier)
    {
        $modifiers = (int)$this->config->get('show_modifiers', 1);

        if ($modifiers) {
            $options = $this->findOptions($identifier);

            return $this->addModifiers($options);
        }

        return $this->selectedOptions($identifier);
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
            $parts = explode('.', $key);
            if ($value = $this->getData($parts[1] . '._value.value')) {
                $total = JBCart::val($value);

                $result[$option] = $option . ' <em>' . $total->html($this->currency()) . '</em>';
            }
        }

        return $result;
    }

    /**
     * Bind and validate data
     * @param array $data
     */
    public function bindData($data = array())
    {
        $result = array();
        $hashes = array();

        if (isset($data['variations'])) {
            $list       = $this->build($data['variations']);
            $variations = array();
            $selected   = array();
            $values     = array();

            unset($data['variations']);
            /** @type JBCartVariant $variant */
            foreach ($list as $key => $variant) {

                /** @type JBCartElementPrice $element */
                if ($variant->count('simple') || $variant->isBasic()) {

                    //add variant hash to array based on elements values
                    $hashes[$key]     = $variant->hash();
                    $variations[$key] = $variant->data();
                }
            }
            //leave only unique hashes. The array keys are the keys of valid variants.
            $hashes = array_unique($hashes);

            //get valid variants
            $list       = array_intersect_key($list, $hashes);
            $variations = array_intersect_key($variations, $hashes);

            //reset the array keys
            if (isset($variations)) {
                $variations = array_values($variations);
                $list       = array_values($list);
            }

            //unset basic variant. He has no simple elements.
            unset($list[self::BASIC_VARIANT]);

            //generate array values and selected
            if (count($list)) {
                foreach ($list as $key => $variant) {
                    if (!$variant->isBasic()) {
                        $values[$key] = array_filter(array_map(create_function('$element',
                                'return JString::strlen($element->getValue(true)) > 0 && $element->isCore() == false
                                ? (array)$element->data() : null;'), $variant->all())
                        );

                        foreach ($variant->getSimpleElements() as $id => $element) {
                            $value                 = $this->_helper->clean($element->getValue(true));
                            $selected[$id][$value] = $value;
                        }
                    }
                }
            }

            $result['values']     = $values;
            $result['selected']   = $selected;
            $result['variations'] = $variations;
            unset($values, $selected, $variations);
        }

        if (!empty($data)) {
            foreach ($data as $_id => $unknown) {
                $result[$_id] = is_string($unknown) ? JString::trim($unknown) : $unknown;
            }
        }

        parent::bindData($result);
    }

    /**
     * Get element search data for sku table
     * @return array
     */
    public function getIndexData()
    {
        $list    = $this->get('variations');
        $item_id = $this->getItem()->id;

        $data = array();
        if (!empty($list)) {
            /*$elements = array_merge(
                (array)$this->_position->loadElements(JBCart::CONFIG_PRICE),
                (array)$this->_position->loadElements(JBCart::CONFIG_PRICE_TMPL)
            );
            */
            $this->setDefault(self::BASIC_VARIANT);

            $_list = $this->getList($list);
            foreach ($_list->first()->all() as $id => $element) {
                $value = $element->getSearchData();

                if (!empty($value)) {
                    $d = $s = $n = null;
                    if ($value instanceof JBCartValue) {
                        $s = $value->data(true);
                        $n = $value->val();
                    } elseif (isset($value{0})) {
                        $s = $value;
                        $n = $this->isNumeric($value) ? $value : null;
                        $d = $this->isDate($value) ? $value : null;
                    }

                    if (isset($s) || isset($n) || isset($d)) {
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
        }
        $this->_list = $this->_params = $this->params = null;

        return $data;
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
        $this->app->jbassets->less('elements:jbpricecalc/assets/less/jbpricecalc.less');

        return parent::loadAssets();
    }
}
