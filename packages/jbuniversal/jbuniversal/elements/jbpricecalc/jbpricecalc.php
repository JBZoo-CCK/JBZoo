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
        $this->getVariantList($list, array(
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
        $this->getVariantList($list, array(
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
     * @param string $identifier
     * @return array
     */
    public function findOptions($identifier)
    {
        $result = array();
        if (empty($identifier)) {
            return $result;
        }

        $variations = $this->get('values', array());
        if (!empty($variations)) {
            foreach ($variations as $key => $variant) {
                if (isset($variant[$identifier])) {
                    $value = $variant[$identifier]['value'];
                    if (JString::strlen($value) !== 0) {
                        $result[$key] = array(
                            'value' => $value,
                            'name'  => $value
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Prepare element data to push into JBHtmlHelper - select, radio etc.
     * @param $identifier
     * @return array
     */
    public function selectedOptions($identifier)
    {
        $options = $this->findOptions($identifier);
        if (empty($options)) {
            return $options;
        }

        $result = array();
        foreach ($options as $key => $value) {
            $result[$value['value']] = $value['name'];
        }

        return $result;
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
        $data   = array(
            0 => $this->get('variations.' . 0)
        );
        foreach ($options as $key => $option) {
            if (!$variant = $this->_list->get($key)) {
                if ($key != 0) {
                    $data[$key] = $this->get('variations.' . $key);
                }

                $variant = $this->build($data);
            }
            $total = $variant[$key]->get('_value');

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
     * @param array $list
     * @param array $options
     * @return JBCartVariantList
     */
    public function getVariantList($list = array(), $options = array())
    {
        if (!$this->_list instanceof JBCartVariantList) {
            if (empty($list)) {
                $list = $this->defaultList();
            }

            $variations = $this->build($list);
            $options = array_merge(array(
                'element'    => $this,
                'element_id' => $this->identifier,
                'item_id'    => $this->_item->id,
                'item_name'  => $this->_item->name,
                'template'   => $this->_template,
                'layout'     => $this->_layout,
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay(),
                'values'     => $this->get('values.' . $this->defaultKey()),
                'currency'   => $this->currency(),
                'default'    => $this->defaultKey()
            ), (array)$options);

            $this->_list = new JBCartVariantList($variations, $options);

            return $this->_list;
        }

        if (!empty($list)) {
            $variations = $this->build($list);
            $this->_list
                ->add($variations)
                ->setOptions($options);
        }

        return $this->_list;
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadEditAssets()
    {
        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator/calc.js');
        }

        return parent::loadEditAssets();
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
