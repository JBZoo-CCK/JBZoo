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
class ElementJBPricePlain extends ElementJBPrice
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->isOverlay = false;
    }

    /**
     * Get variant from $this->data() by values
     * MODE: DEFAULT
     * @param  array $values values from front end
     * @return array
     */
    public function getVariantByValues($values = array())
    {
        $data = (array)$this->get('values', array());

        if (empty($values) || empty($data)) {
            return (array)$values;
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
     * @param array $template - need to get render params for elements
     * @param array  $values   - selected values
     */
    public function ajaxChangeVariant($template = array('default'), $values = array())
    {
        $data = array();
        $list = $this->getVariantByValues($values);
        $key  = (int)(JString::strlen(key($list)) > 0 ? key($list) : self::BASIC_VARIANT);

        $this->setDefault($key);
        if(count($template))
        {
            foreach($template as $tpl) {
                $this->setTemplate($tpl);

                $this->getList($list, array(
                    'default'  => $key,
                    'values'   => $values,
                    'template' => $tpl,
                    'currency' => $this->currency()
                ));

                $data = array_merge_recursive((array)$data, (array)$this->_list->renderVariant());
            }
        }

        $this->app->jbajax->send($data);
    }

    /**
     * Ajax add to cart method
     * @param array $template
     * @param int    $quantity
     * @param array  $values
     * @throws ElementJBPriceException
     */
    public function ajaxAddToCart($template = array('default'), $quantity = 1, $values = array())
    {
        $jbAjax = $this->app->jbajax;

        //Get variant by selected values.
        $list = $this->getVariantByValues($values);

        $cart = JBCart::getInstance();
        $key  = (int)(JString::strlen(key($list)) > 0 ? key($list) : self::BASIC_VARIANT);

        // Set the default option, which we have received, not saved. For correct calculation.
        $this->setDefault($key)->setTemplate($template);

        $list = $this->getList($list, array(
            'values'   => $values,
            'quantity' => $quantity
        ));

        $session_key = $list->getSessionKey();
        $data        = $cart->getItem($session_key);
        if (!empty($data)) {
            $quantity += $data['quantity'];
        }

        // Check if all required params is selected.
        $missing = $this->getMissing($values);

        // Check required.
        if (count($missing)) {
            throw new ElementJBPriceException(JText::sprintf('JBZOO_JBPRICE_OPTIONS_IS_REQUIRED', '"' . implode('", "', $missing) . '"'));
        }

        // Check balance.
        if (!$this->inStock($quantity, $key)) {
            throw new ElementJBPriceException(JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY'));
        }

        $cart->addItem($list->getCartData())
             ->updateItem($cart->getItem($session_key));

        $jbAjax->send(array(), true);
    }

    /**
     * @param string $template Template to render
     * @param string $layout   Current element price layout
     * @param string $hash     Hash string for communication between the elements in/out modal window
     * @return string
     */
    public function ajaxModalWindow($template = 'default', $layout = 'default', $hash = '')
    {
        $this->setTemplate($template)->setLayout($layout);
        $this->cache = false;

        $this->getParameters();
        $this->getConfigs();

        $html = $this->render(array(
            'template'       => $template,
            'layout'         => 'modal',
            '_layout'        => $layout,
            'modal_template' => null
        ));

        return parent::renderLayout($this->getLayout('_modal.php'), array(
            'html' => $html
        ));
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
        $result  = JBCart::getInstance()->remove($item_id, $this->identifier);

        $this->app->jbajax->send(array('removed' => $result));
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