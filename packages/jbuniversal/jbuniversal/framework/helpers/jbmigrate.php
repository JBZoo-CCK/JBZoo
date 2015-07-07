<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBMigrateHelper
 */
class JBMigrateHelper extends AppHelper
{
    /**
     * Type of price to create
     * @type string
     */
    protected $_type = 'jbpriceplain';

    /**
     * @type JBCartElementHelper
     */
    protected $_element;

    /**
     * @type JBCartPositionHelper
     */
    protected $_position;

    /** Class constructor
     *
     * @param App $app A reference to an App Object
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_element  = $this->app->jbcartelement;
        $this->_position = $this->app->jbcartposition;
    }

    /**
     * @param $type
     * @param $data
     * @return mixed
     */
    public function create($type, $data)
    {
        $method = 'create' . $type;
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $data);
        }

        return false;
    }

    /**
     * @param $list
     * @return bool
     */
    public function createCurrency($list)
    {
        $jbMoney = $this->app->jbmoney;
        $params  = $this->_position->loadParams(JBCart::CONFIG_CURRENCIES, false, false);

        $position = $params->get(JBCart::DEFAULT_POSITION);
        $isModify = false;

        foreach ($list as $currency) {
            if (!$jbMoney->checkCurrency($currency)) {
                $isModify = true;
                $element  = $this->_element->create('cbr', 'currency');
                $config   = $element->getFormat();

                $element->identifier  = $this->app->utility->generateUUID();
                $config['identifier'] = $element->identifier;

                $config['name']  = $currency;
                $config['code']  = $currency;
                $config['type']  = $element->getElementType();
                $config['group'] = $element->getElementGroup();

                $element->setConfig($config);
                $position[$element->identifier] = (array)$element->config;
            }
        }
        if ($isModify) {
            $params->set(JBCart::DEFAULT_POSITION, $position);
            $this->_position->save(JBCart::CONFIG_CURRENCIES, (array)$params);
        }

        return $isModify;
    }

    /**
     * @param  $list
     * @return array
     */
    public function createPrice($list)
    {
        $list = (array)$list;
        $app  = $this->app->zoo->getApplication();

        foreach ($list as $id => $price) {
            if (!isset($price['id'])) {

                // load element
                $element             = $this->app->element->create($this->_type);
                $element->identifier = $this->app->utility->generateUUID();

                $price['config']['identifier'] = $element->identifier;
                $price['id']                   = $element->identifier;

                $element->config = $price['config'];
                $type            = $app->getType($price['type']);
                $elements        = (array)$type->config->get('elements');

                $elements[$element->identifier] = (array)$element->config;

                $list[$id] = $price;
                $type->bindElements(array('elements' => $elements))->save();
            }
        }

        return $list;
    }

    /**
     * @param $list
     */
    public function createPriceElements($list)
    {
        $list = array_filter((array)$list);
        $app  = $this->app->zoo->getApplication();

        $jbElement = $this->_element;
        $group     = 'price';
        foreach ($list as $oldId => $data) {
            $id   = $data['id'];
            $type = $app->getType($data['type']);

            $element   = $type->getElement($id);
            $elements  = array_map(function ($config) use ($jbElement, $group, $element) {
                if ($config && (!$element->getElement($config['identifier']))) {
                    return $jbElement->create($config['type'], $group, $config)->config;
                }

                return false;
            }, $data['elements']);
            $elements  = array_filter($elements);
            $positions = (array)$this->_position->loadParams(JBCart::CONFIG_PRICE . '.' . $id, false, false)->get(JBCart::DEFAULT_POSITION);

            $positions = array_merge((array)$positions, $elements);

            $this->_position->savePrice($group, array('list' => $positions), $id);
        }

        return true;
    }

    /**
     * Return array of @see ElementJBPriceAdvance objects.
     * @param string|array $types Can be id of item @see Type or array of ids.
     *
     * @return array
     */
    public function getPriceList($types)
    {
        $app     = $this->app->zoo->getApplication();
        $objects = array();

        $types = (array)$types;
        foreach ($types as $type) {
            $prices = (array)array_filter($app->getType($type)->getElements(), function ($element) {
                return $element instanceof ElementJBPriceAdvance;
            });

            if ($prices) {
                $objects = array_merge((array)$objects, $prices);
            }
        }

        return $objects;
    }

    /**
     * Extract needle data from deprecated price object.
     * @param  array $prices Array of @see ElementJBPriceAdvance instances
     * @return mixed
     */
    public function extractPriceData($prices)
    {
        $prices = (array)$prices;

        $elements = array();
        $data     = $this->app->data->create(array());

        foreach ($prices as $element) {
            $list = array_filter((array)$element->config->get('currency_list', array()));

            $elements[$element->identifier] = array(
                'elements' => $this->_extractCoreElements($element),
                'create'   => $this->_extractSimpleElements($element),
                'system'   => $this->_extractSystemElements($element),
                'config'   => $this->_extractConfig($element),
                'type'     => $element->getType()->id
            );

            if ($list) {
                $data->set('currency_list', array_merge((array)$data->get('currency_list', array()), $list));
            }
        }
        $data->set('price', $elements);

        return $data;
    }

    /**
     * @param  ElementJBPriceAdvance $element
     * @return array
     */
    protected function _extractConfig($element)
    {
        return array(
            'name'          => $element->config->get('name', ''),
            'description'   => $element->config->get('description', ''),
            'access'        => $element->config->get('access'),
            'cache'         => (int)$element->config->get('cache', 1),
            'only_selected' => (int)$element->config->get('adv_all_exists_show', 1),
            'type'          => $this->_type
        );
    }

    /**
     * @param  ElementJBPriceAdvance $element
     * @return array
     */
    protected function _extractCoreElements($element)
    {
        $config = $element->config;

        $elements = array(
            'value'   => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_VALUE_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'value',
                'group'       => 'price',
                'identifier'  => '_value'
            ),
            'sku'     => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_SKU_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'sku',
                'group'       => 'price',
                'identifier'  => '_sku'
            ),
            'balance' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'usestock'    => (int)$config->get('balance_mode', 1),
                'type'        => 'balance',
                'group'       => 'price',
                'identifier'  => '_balance'
            )
        );

        if (!empty($config['adv_field_text']) && ((int)$config['adv_field_text'] !== 3)) {
            $elements['description'] = array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_DESCRIPTION_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'description',
                'group'       => 'price',
                'identifier'  => '_description'
            );
        }
        if ($elements['balance']['usestock']) {
            $elements['hook'] = array();
        }

        return $elements;
    }

    /**
     * @param $element
     * @return array
     */
    protected function _extractSystemElements($element)
    {
        $config = $element->config;

        return array(
            'quantity' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_QUANTITY_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'default'     => (float)$config->get('quantity_default', 1),
                'min'         => (float)$config->get('quantity_min', 1),
                'step'        => (float)$config->get('quantity_step', 1),
                'decimals'    => (float)$config->get('quantity_decimals', 1),
                'type'        => 'quantity',
                'group'       => 'price',
                'identifier'  => '_quantity'
            ),
            'currency' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_CURRENCY_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'default'     => $config->get('currency_default'),
                'list'        => $config->get('currency_list'),
                'type'        => 'currency',
                'group'       => 'price',
                'identifier'  => '_currency'
            )
        );
    }

    /**
     * @param  ElementJBPriceAdvance $element
     * @return array
     */
    protected function _extractSimpleElements($element)
    {
        /**
         * @type Type    $type
         * @type AppData $config
         **/
        $config = $element->config;
        $type   = $element->getType();
        $types  = array();

        if (!empty($config['adv_field_param_1'])) {
            $types[] = $type->getElementConfig($config->get('adv_field_param_1'))->get('type');
        }

        if (!empty($config['adv_field_param_2'])) {
            $types[] = $type->getElementConfig($config->get('adv_field_param_2'))->get('type');
        }

        if (!empty($config['adv_field_param_3'])) {
            $types[] = $type->getElementConfig($config->get('adv_field_param_3'))->get('type');
        }

        if (!empty($config['adv_field_text']) && (int)$config['adv_field_text'] === 3) {
            $types[] = 'text';
        }

        return $types;
    }


}