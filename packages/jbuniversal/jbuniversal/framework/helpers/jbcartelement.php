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
        'JBCartElementOrder'         => 'cart-elements:core/order/order.php',
        'JBCartElementPayment'       => 'cart-elements:core/payment/payment.php',
        'JBCartElementPrice'         => 'cart-elements:core/price/price.php',
        'JBCartElementStatus'        => 'cart-elements:core/status/status.php',
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
     * @param array $groups
     * @param bool $getHidden
     * @return array
     */
    public function getGroups($groups, $getHidden = false)
    {
        $groups = (array)$groups;

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

                        $isHidden = $element->isHidden() == 'true';

                        if (!$isHidden || $getHidden) {
                            $elements[$group][$type] = $element;
                        }

                    }
                }
            }

            uasort($elements[$group], array($this, '_sortGroup'));
        }

        $elements = array_filter($elements);

        return $elements;
    }

    /**
     * User compare function for element grouping
     * @param JBCartElement $elem1
     * @param JBCartElement $elem2
     * @return int
     */
    protected function _sortGroup($elem1, $elem2)
    {
        if ($elem1->isCore() == $elem2->isCore()) {
            return 0;
        }

        return ($elem1->isCore() && !$elem2->isCore()) ? -1 : 1;
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
     * @param $group
     * @return array
     */
    public function getSystemTmpl($group)
    {
        $elements = array();
        $list     = $this->getGroups($group, true);

        if (isset($list[$group])) {

            foreach ($list[$group] as $type => $element) {
                if ($element->isSystemTmpl()) {

                    $element->setConfig(array(
                        'name' => 'JBZOO_ELEMENT_CORE_' . strtoupper($element->getElementType())
                    ));

                    $elements[$type] = $element;
                }
            }

        }

        return $elements;
    }

    /**
     * Creates element of given type
     * @param string $type The type to create
     * @param string $group The group to create
     * @param array $config
     * @return JBCartElement
     */
    public function create($type, $group, $config = array())
    {
        // load element class
        $elementClass  = 'JBCartElement' . $group . $type;

        if (!class_exists($elementClass)) {
            $this->app->loader->register($elementClass, "cart-elements:$group/$type/$type.php");
        }

        if (!class_exists($elementClass)) {
            return null;
        }

        $element = new $elementClass($this->app, $type, $group);

        if (isset($config['identifier'])) {
            $element->identifier = $config['identifier'];
        }

        if ($element->isCore()) {
            $element->identifier = '_' . strtolower($element->getElementType());
            $element->setConfig(array(
                'name' => JText::_('JBZOO_ELEMENT_CORE_' . $element->getElementType())
            ));
        }

        if ($config) {
            $element->setConfig($config);
        }

        return $element;
    }

}

