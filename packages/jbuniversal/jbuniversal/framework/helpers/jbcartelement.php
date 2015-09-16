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
 * Class JBCartElementHelper
 */
class JBCartElementHelper extends AppHelper
{
    /**
     * Core elements
     * @var array
     */
    protected $_coreElements = array(
        'JBCartElement'                   => 'element',
        'JBCartElementCurrency'           => 'currency',
        'JBCartElementEmail'              => 'email',
        'JBCartElementShipping'           => 'shipping',
        'JBCartElementShippingField'      => 'shippingfield',
        'JBCartElementModifierItem'       => 'modifieritem',
        'JBCartElementModifierItemPrice'  => 'modifieritemprice',
        'JBCartElementModifierOrderPrice' => 'modifierorderprice',
        'JBCartElementNotification'       => 'notification',
        'JBCartElementHook'               => 'hook',
        'JBCartElementOrder'              => 'order',
        'JBCartElementPayment'            => 'payment',
        'JBCartElementPrice'              => 'price',
        'JBCartElementStatus'             => 'status',
        'JBCartElementValidator'          => 'validator',
    );

    /**
     * Constructor
     */
    public function __construct($app)
    {
        parent::__construct($app);

        // load parent classes
        $path = JPATH_ROOT . '/media/zoo/applications/jbuniversal/cart-elements/core/';
        foreach ($this->_coreElements as $class => $type) {
            JLoader::register($class, $path . $type . '/' . $type . '.php');
        }
    }

    /**
     * Returns an array of all Elements.
     * @param array $groups
     * @param bool  $getHidden
     * @return array
     */
    public function getGroups($groups, $getHidden = false)
    {
        $groups   = (array)$groups;
        $elements = array();

        foreach ($groups as $group) {

            if ($group == 'core' || !$this->app->path->path('cart-elements:' . $group)) {
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
     * @param JBCartElement $element1
     * @param JBCartElement $element2
     * @return int
     */
    protected function _sortGroup($element1, $element2)
    {
        $core1 = $element1->isCore();
        $core2 = $element2->isCore();
        $name1 = $element1->getMetaData('name');
        $name2 = $element2->getMetaData('name');

        if ($core1 == $core2) {
            return strcasecmp($name1, $name2);
        }

        return ($core1 && !$core2) ? -1 : 1;
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
                    $type = strtolower($element->getElementType());

                    if ($element->isCore()) {
                        $element->identifier = '_' . $type;
                    }
                    $elements[$element->identifier] = $element;
                }
            }

        }

        return $elements;
    }

    /**
     * Creates element of given type
     * @param string $type  The type to create
     * @param string $group The group to create
     * @param array  $config
     * @return JBCartElement
     */
    public function create($type, $group, $config = array())
    {
        // load element class
        $elementClass = 'JBCartElement' . $group . $type;

        if (!class_exists($elementClass)) {
            if ($classPath = $this->app->path->path("cart-elements:$group/$type/$type.php")) {
                require_once $classPath;
            }
        }

        if (!class_exists($elementClass)) {
            return null;
        }

        /** @var JBCartElement $element */
        $element = new $elementClass($this->app, $type, $group);

        $keyName     = 'JBZOO_ELEMENT_' . strtoupper($group) . '_' . strtoupper($type) . '_NAME';
        $elementName = JText::_($keyName) !== $keyName ? JText::_($keyName) : '';

        $config = array_merge(array(
            'identifier'  => $this->app->utility->generateUUID(),
            'type'        => $type,
            'group'       => $group,
            'name'        => $elementName,
            'description' => '',
            'access'      => '1',
        ), (array)$config);

        if ($element->isCore()) {
            $config['identifier'] = '_' . strtolower($element->getElementType());
            $config['name']       = JText::_('JBZOO_ELEMENT_CORE_' . $element->getElementType());
        }

        if ($config['identifier']) {
            $element->identifier = $config['identifier'];
        }

        $element->setConfig($config);

        return $element;
    }

    /**
     * Get core elements from price group
     * @return array
     */
    public function getPriceCore()
    {
        $group    = $this->getGroups(JBCart::ELEMENT_TYPE_PRICE);
        $elements = array();

        if (!empty($group['price'])) {
            foreach ($group['price'] as $key => $element) {
                if ($element->isCore()) {
                    $elements[$element->identifier] = $element;
                }
            }
        }

        return $elements;
    }

}

