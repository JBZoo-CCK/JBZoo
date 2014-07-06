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
 * Class JBCartElementHelper
 */
class JBCartElementHelper extends AppHelper
{
    /**
     * Core elements
     * @var array
     */
    protected $_coreElements = array(
        'JBCartElement'              => 'cart-elements:core/element/element.php',
        'JBCartElementCurrency'      => 'cart-elements:core/currency/currency.php',
        'JBCartElementDelivery'      => 'cart-elements:core/delivery/delivery.php',
        'JBCartElementModifierItem'  => 'cart-elements:core/modifieritem/modifieritem.php',
        'JBCartElementModifierPrice' => 'cart-elements:core/modifierprice/modifierprice.php',
        'JBCartElementNotification'  => 'cart-elements:core/notification/notification.php',
        'JBCartElementPayment'       => 'cart-elements:core/payment/payment.php',
        'JBCartElementPriceparam'    => 'cart-elements:core/priceparam/priceparam.php',
        'JBCartElementValidator'     => 'cart-elements:core/validator/validator.php',
    );

    /**
     * Constructor
     */
    public function __construct($app)
    {
        parent::__construct($app);

        // load parent classes
        foreach ($this->_coreElements as $type => $path) {
            $app->loader->register($type, $path);
        }
    }

    /**
     * Returns an array of all Elements.
     * @param $groups
     * @return array
     */
    public function getGroups($groups = array('delivery', 'modifieritem', 'modifierprice', 'notification', 'payment', 'validator'))
    {
        $elements = array();

        foreach ($this->app->path->dirs('cart-elements:') as $group) {

            if (!in_array($group, $groups) || $group == 'core') {
                continue;
            }

            $elements[$group] = array();
            foreach ($this->app->path->dirs('cart-elements:' . $group) as $type) {

                $filePath = $this->app->path->path('cart-elements:' . $group . '/' . $type . '/' . $type . '.php');

                if ($type != 'element' && is_file($filePath)) {
                    if ($element = $this->create($type, $group)) {
                        if ($element->getMetaData('hidden') != 'true') {
                            $elements[$group][$type] = $element;
                        }
                    }
                }
            }
        }

        $elements = array_filter($elements);
        ksort($elements);

        return $elements;
    }

    /**
     * Get core
     * @return array
     */
    public function getAllCore()
    {
        $result   = array();
        $elements = $this->app->path->dirs('cart-elements:core');

        foreach ($elements as $group) {
            if ($group == 'element') {
                continue;
            }

            $result[] = $group;
        }

        return $result;
    }

    /**
     * Creates element of given type
     * @param string $type The type to create
     * @param string $group The group to create
     * @return JBCartElement
     */
    public function create($type, $group)
    {
        // load element class
        $elementClass = 'JBCartElement' . $group . $type;

        if (!class_exists($elementClass)) {
            $this->app->loader->register($elementClass, "cart-elements:$group/$type/$type.php");
        }

        if (!class_exists($elementClass)) {
            return false;
        }

        $element = new $elementClass($this->app, $type, $group);

        return $element;
    }

}