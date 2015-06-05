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
 * Class ElementJBPricePlain.
 * The simple price element for JBZoo App.
 *
 * @package      JBZoo.Price
 * @author       Alexander Oganov <t_tapak@yahoo.com>
 * @version      1.1
 * @since        Release 2.2(Beta)
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
        $data = array_filter((array)$this->get('values', array()));

        if (empty($values) || empty($data)) {
            return (array)$values;
        }
        asort($values);

        $needle    = md5(serialize($values));
        $hashTable = array_map(function ($array) {
            asort($array);

            return md5(serialize($array));
        }, $data);

        return array_search($needle, $hashTable, true);
    }

    /**
     * @param array  $template - need to get render params for elements
     * @param array  $values   - selected values
     */
    public function ajaxChangeVariant($template = array('default'), $values = array())
    {
        $key = $this->getVariantByValues($values);
        $key = !empty($key) || $key == '0' ? $key : self::BASIC_VARIANT;

        $this->setDefault($key)->setTemplate($template);
        $this->getList($this->defaultData(), array(
            'default'  => $key,
            'values'   => $this->getValues($values),
            'selected' => $values
        ));

        $this->app->jbajax->send($this->renderVariant());
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
        /** @type $jbAjax JBAjaxHelper  */
        $jbAjax = $this->app->jbajax;

        //Get variant by selected values.
        $key = $this->getVariantByValues($values);
        $key = !empty($key) || $key == '0' ? $key : self::BASIC_VARIANT;

        $cart = JBCart::getInstance();

        // Set the default option, which we have received, not saved. For correct calculation.
        $this->setDefault($key)->setTemplate($template);

        $list = $this->getList($this->defaultData(), array(
            'values'   => $this->getValues($values),
            'selected' => $values,
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
        $this->setTemplate($template);
        $this->cache = false;
        $this->hash  = $hash;

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