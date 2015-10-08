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
 * Class JBMigratePriceHelper
 */
class JBMigratePriceHelper extends AppHelper
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

    /**
     * @type JBMigrateHelper
     */
    protected $_migrate;

    /** Class constructor
     * @param App $app A reference to an App Object
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_element  = $this->app->jbcartelement;
        $this->_position = $this->app->jbcartposition;
        $this->_migrate  = $this->app->jbmigrate;
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

        /** @var Application $app */
        $app = $this->app->zoo->getApplication();

        foreach ($list as $id => $price) {
            if (!isset($price['id'])) {

                /** @var Type $type */
                $type = $app->getType($price['type']);

                $existedElements = $type->getElementsByType('jbpriceplain');

                /** @var ElementJBPriceAdvance $element */
                if (!$existedElements) {
                    $element             = $this->app->element->create($this->_type);
                    $element->identifier = $this->app->utility->generateUUID();
                } else {
                    reset($existedElements);
                    $element = current($existedElements);
                }

                // configs for migration
                $price['config']['identifier'] = $element->identifier;
                $price['id']                   = $element->identifier;
                $element->config               = $price['config'];

                $elements = (array)$type->config->get('elements');

                $elements[$element->identifier] = (array)$element->config;

                $list[$id] = $price;

                $type
                    ->bindElements(array('elements' => $elements))
                    ->save();
            }
        }

        return $list;
    }

    /**
     * @param $list
     * @return bool
     */
    public function createPriceElements($list)
    {
        $list = array_filter((array)$list);

        /** @var Application $app */
        $app = $this->app->zoo->getApplication();

        $jbElement = $this->_element;
        $group     = 'price';

        foreach ($list as $oldId => $data) {

            $id = $data['id'];

            $oldList = $this->_position->loadPositions(JBCart::CONFIG_PRICE . '.' . $id, array(JBCart::DEFAULT_POSITION));
            $oldList = isset($oldList[JBCart::DEFAULT_POSITION]) ? $oldList[JBCart::DEFAULT_POSITION] : array();

            /**
             * @var Type          $type
             * @var JBCartElement $element
             */
            $type    = $app->getType($data['type']);
            $element = $type->getElement($id);

            // core elements
            $elements = array_map(function ($config) use ($jbElement, $group, $element) {
                if ($config && (!$element->getElement($config['identifier']))) {
                    return $jbElement->create($config['type'], $group, $config)->config;
                }
                return false;
            }, $data['elements']);

            // price params
            $elements += array_map(function ($config) use ($jbElement, $group, $element, $oldList, $id) {

                // if already exists
                foreach ($oldList as $oldElement) {
                    if ($oldElement->config->get('old_id') == $config->identifier) {
                        return false;
                    }
                }

                if ($options = $config->get('option', array())) {
                    $newOptions = array();
                    foreach ($options as $option) {
                        $newOptions[$option['value']] = $option['name'];
                    }

                    $config->set('options', implode("\n", $newOptions));
                    $config->set('options-map', $newOptions);
                    $config->remove('option');
                }

                return $jbElement->create($config['type'], $group, $config)->config;
            }, $data['params']);

            // saving
            $elements  = array_filter($elements);
            $positions = (array)$this->_position
                ->loadParams(JBCart::CONFIG_PRICE . '.' . $id, false, false)
                ->get(JBCart::DEFAULT_POSITION);

            $positions = array_merge((array)$positions, $elements);

            $this->_position->savePrice($group, array('list' => $positions), $id);

            $list[$oldId]['map'] = $positions;
        }

        return $list;
    }

    /**
     * Return array of @see ElementJBPriceAdvance objects.
     * @param string|array $types Can be id of item @see Type or array of ids.
     * @return array
     */
    public function getPriceList($types)
    {
        /** @var Application $app */
        $app = $this->app->zoo->getApplication();

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

        /** @var ElementJBPriceAdvance $element */
        foreach ($prices as $element) {
            $list = array_filter((array)$element->config->get('currency_list', array()));

            $elements[$element->identifier] = array(
                'elements' => $this->_extractCoreElements($element),
                'params'   => $this->_extractSimpleElements($element),
                'system'   => $this->_extractSystemElements($element),
                'config'   => $this->_extractConfig($element),
                'type'     => $element->getType()->id,
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
            'name'          => $element->config->get('name', '') . ' (migrated)',
            'description'   => $element->config->get('description', ''),
            'access'        => $element->config->get('access'),
            'cache'         => (int)$element->config->get('cache', 1),
            'only_selected' => (int)$element->config->get('adv_all_exists_show', 1),
            'type'          => $this->_type,
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
            '_sku'      => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_SKU_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'sku',
                'group'       => 'price',
                'identifier'  => '_sku',
            ),
            '_value'    => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_VALUE_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'value',
                'group'       => 'price',
                'identifier'  => '_value',
            ),
            '_discount' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_DISCOUNT_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'discount',
                'group'       => 'price',
                'identifier'  => '_discount',
            ),
            '_balance'  => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'usestock'    => (int)$config->get('balance_mode', 1),
                'type'        => 'balance',
                'group'       => 'price',
                'identifier'  => '_balance',
            ),

        );

        if (!empty($config['adv_field_text']) && ((int)$config['adv_field_text'] == 1)) {
            $elements['_description'] = array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_DESCRIPTION_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'type'        => 'description',
                'group'       => 'price',
                'identifier'  => '_description',
            );
        }

        if ($elements['_balance']['usestock']) {
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
            '_quantity' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_QUANTITY_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'default'     => (float)$config->get('quantity_default', 1),
                'min'         => (float)$config->get('quantity_min', 1),
                'step'        => (float)$config->get('quantity_step', 1),
                'decimals'    => (float)$config->get('quantity_decimals', 1),
                'type'        => 'quantity',
                'group'       => 'price',
                'identifier'  => '_quantity',
            ),
            '_currency' => array(
                'name'        => JText::_('JBZOO_ELEMENT_PRICE_CURRENCY_NAME'),
                'description' => '',
                'access'      => $element->config->get('access'),
                'default'     => $config->get('currency_default'),
                'list'        => $config->get('currency_list'),
                'type'        => 'currency',
                'group'       => 'price',
                'identifier'  => '_currency',
            ),
        );
    }

    /**
     * @param  ElementJBPriceAdvance $element
     * @return array
     */
    protected function _extractSimpleElements($element)
    {
        /**
         * @type AppData $config
         * @type Type    $type
         **/
        $elemConfig = $element->config;
        $type       = $element->getType();

        $types = array();
        if ($elemId = $elemConfig->get('adv_field_param_1')) {
            if ($config = $type->getElementConfig($elemId)) {
                $config['old_id']     = $elemId;
                $config['identifier'] = 'param1';
                $types['param1']      = $config;
            }
        }

        if ($elemId = $elemConfig->get('adv_field_param_2')) {
            if ($config = $type->getElementConfig($elemId)) {
                $config['old_id']     = $elemId;
                $config['identifier'] = 'param2';
                $types['param2']      = $config;
            }
        }

        if ($elemId = $elemConfig->get('adv_field_param_3')) {
            if ($config = $type->getElementConfig($elemId)) {
                $config['old_id']     = $elemId;
                $config['identifier'] = 'param3';
                $types['param3']      = $config;
            }
        }

        if ($elemConfig->get('adv_field_text') && (int)$elemConfig['adv_field_text'] === 2) {
            $elemId         = 'old-description';
            $types[$elemId] = $this->app->data->create(array(
                'type'       => 'text',
                'old_id'     => $elemId,
                'identifier' => $elemId,
            ));
        }

        return $types;
    }

    /**
     * Convert items
     */
    public function convertItems($page)
    {
        $params   = $this->_migrate->getParams();

        $realStep = $page - $params->find('steps.system_steps') - $params->find('steps.orders_steps');
        $size     = $params->find('steps.step');

        if ($realStep <= 0) {
            return -1;
        }

        $items = JBModelItem::model()->getList($params->get('prices_app'), null, $params->get('prices_types'), array(
            'limit'     => array(($realStep - 1) * $size, $size),
            'published' => 0,
            'state'     => -1,
            'order'     => 'id',
        ));

        if ($items) {
            foreach ($items as $item) {
                if ($this->_convertItem($item)) {
                    $this->app->table->item->save($item);
                }
            }

            return $page + 1;
        }

        return false;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function _convertItem(Item $item)
    {
        $oldPrices = $item->getElementsByType('jbpriceadvance');
        $oldPrice  = current($oldPrices);
        if (!$oldPrice) {
            return false;
        }
        $oldData   = $this->app->data->create($oldPrice->data());

        $params = $this->app->data->create($this->_migrate->getParams()->find('elements.' . $oldPrice->identifier));

        $newPriceData = array(
            'default_variant' => 0,
            'variations'      => array(array(
                '_sku'         => array('value' => $oldData->find('basic.sku')),
                '_balance'     => array('value' => $oldData->find('basic.balance')),
                '_value'       => array('value' => $oldData->find('basic.value') . ' ' . $oldData->find('basic.currency')),
                '_discount'    => array('value' => $oldData->find('basic.discount') . ' ' . $oldData->find('basic.discount_currency')),
                '_description' => array('value' => $oldData->find('basic.description')),
            )),
        );

        $variations = (array)$oldData->find('variations', array());
        foreach ($variations as $vari) {

            /** @var AppData $vari */
            $vari = $this->app->data->create($vari);

            $newPriceData['variations'][] = array(
                '_value'          => array('value' => $vari->get('value') . ' ' . $vari->get('currency')),
                '_balance'        => array('value' => $vari->get('balance')),
                '_sku'            => array('value' => $vari->get('sku')),
                'param1'          => array('value' => $params->find('map.param1.options-map.' . $vari->get('param1'))),
                'param2'          => array('value' => $params->find('map.param2.options-map.' . $vari->get('param2'))),
                'param3'          => array('value' => $params->find('map.param3.options-map.' . $vari->get('param3'))),
                'old-description' => array('value' => $vari->get('description')),
            );
        }

        /** @var ElementJBPricePlain $newPrice */
        $newPriceId = $params->get('id');
        if ($newPriceId && $newPrice = $item->getElement($newPriceId)) {
            $newPrice->bindData($newPriceData);
        }

        return true;
    }

}