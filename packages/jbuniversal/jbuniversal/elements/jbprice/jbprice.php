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

App::getInstance('zoo')->loader->register('JBCartVariantList', 'jbapp:framework/classes/cart/jbvariantlist.php');
App::getInstance('zoo')->loader->register('JBCartVariant', 'jbapp:framework/classes/cart/jbvariant.php');

/**
 * Class ElementJBPrice
 * The Core Price element for JBZoo
 */
abstract class ElementJBPrice extends Element implements iSubmittable
{
    /**
     * Unique string for each item and his params.
     * @type string
     */
    public $hash;

    /**
     * On/off cache.
     * @type bool
     */
    public $cache;

    /**
     * Is this is ElementJBPriceCalc.
     * @type bool
     */
    public $isOverlay;

    /**
     * Show only selected options in elements.
     * @type bool
     */
    public $showAll;

    /**
     * @type JBCartPositionHelper
     */
    protected $_position;

    /**
     * @type JBCacheHelper
     */
    protected $_cache;

    /**
     * @type JBStorageHelper
     */
    protected $_storage;

    /**
     * @type JBCartVariantList
     */
    protected $_list;

    /**
     * @type JBPriceHelper
     */
    protected $_helper;

    /**
     * Elements render parameters for one or more price temples.
     * @type array
     */
    protected $_parameters;

    /**
     * Price template that chosen in Item layout.
     * @type null|string|array
     */
    protected $_template;

    /**
     * Layout - full, teaser, submission etc.
     * @type null|string
     */
    protected $_layout;

    const BASIC_VARIANT       = 0;
    const SIMPLE_PARAM_LENGTH = 36;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        // add callbacks
        $this->registerCallback('ajaxAddToCart');
        $this->registerCallback('ajaxRemoveFromCart');
        $this->registerCallback('ajaxModalWindow');
        $this->registerCallback('ajaxChangeVariant');

        $this->_position = $this->app->jbcartposition;

        $this->_helper  = $this->app->jbprice;
        $this->_cache   = $this->app->jbcache;
        $this->_storage = $this->app->jbstorage;
    }

    /**
     * Set related type object.
     * @param Type $type
     */
    public function setType($type)
    {
        parent::setType($type);

        $this->cache   = (bool)($this->config->get('cache', 0) && $this->canAccess());
        $this->showAll = (bool)!$this->config->get('only_selected', 0);
    }


    /**
     * Check if elements value is set
     * @param array|AppData $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $params   = new AppData($params);
        $template = $params->get('template', 'default');

        $config = $this->setTemplate($template)->getParameters($template);

        return !empty($config);
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function edit($params = array())
    {
        $config = $this->getConfigs();
        if (!empty($config)) {
            if ($layout = $this->getLayout('edit.php')) {
                $this->loadEditAssets();

                $variations = $this->get('variations', array(0 => array()));
                $renderer   = $this->app->jbrenderer->create('jbprice');

                if (count($variations) === 1) {
                    $variations[count($variations)] = array();
                }
                $hash = $this->getHash($params);
                $list = $this->getList($variations);

                return parent::renderLayout($layout, array(
                    'hash'        => $hash,
                    'variations'  => $list->all(),
                    'default'     => $this->defaultKey(),
                    'renderer'    => $renderer,
                    'countSimple' => count(array_filter($this->getElements(), function ($element) {
                        return !$element->isCore() ? $element : null;
                    })),
                ));
            }

            return null;
        }

        return $this->renderWarning('_warning.php', JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_ELEMENTS'));
    }

    /**
     * Renders the element.
     * @param array|AppData $params Element render parameters
     * @return string|null
     */
    public function render($params = array())
    {
        $params = new AppData($params);
        $hash   = $this->setTemplate($params->get('template', 'default'))->getHash($params);
        $this->loadAssets();

        if (!$this->cache || ($this->cache && !$cache = $this->_cache->get($hash, 'price_elements', true))) {

            $template      = $this->getTemplate();
            $this->_layout = $params->get('_layout');

            $renderer = $this->app->jbrenderer->create('jbprice');
            $variant  = $this->getList()->current();

            $data = $renderer->render($template, array(
                '_variant'   => $variant,
                'element_id' => $this->identifier,
                'variant'    => $variant->getId(),
                'layout'     => $this->_layout,
                'item'       => $this->getItem(),
                'element'    => $this,
            ));

            //Must be after renderer
            $elements = $this->elementsInterfaceParams();
            if ($layout = $this->getLayout('render.php')) {
                return $this->renderLayout($layout, array(
                    'hash'       => $hash,
                    'data'       => $data,
                    'elements'   => $elements,
                    'variantUrl' => $this->app->jbrouter->element($this->identifier, $this->_item->id, 'ajaxChangeVariant'),
                ));
            }

            return null;
        }

        return $cache;
    }

    /**
     * Render submission
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        $this->toStorage('elements:jbprice/assets/less/submission.less');

        return $this->edit((array)$params);
    }

    /**
     * @param string $template
     * @param string $message
     * @return string
     */
    public function renderWarning($template = '_warning.php', $message = '')
    {
        if (!$this->app->jbenv->isSite()) {

            $link       = $this->app->jbrouter->admin(array(
                'controller' => 'jbcart',
                'task'       => 'price',
                'element'    => $this->identifier,
            ));
            $attributes = $this->app->jbhtml->buildAttrs(array(
                'href'   => $link,
                'target' => '_blank',
            ));

            $message = '<a ' . $attributes . '>' . $message . '</a>';
        }
        if (($layout = $this->getLayout($template)) && !$this->_helper->isEmpty($message)) {
            return parent::renderLayout($layout, array(
                'message' => $message,
            ));
        }

        return null;
    }

    /**
     * Renders the element using template layout file.
     * @param       $__layout
     * @param array $__args
     * @return string
     */
    public function renderLayout($__layout, $__args = array())
    {
        $html = parent::renderLayout($__layout, $__args);
        if ($this->cache) {
            $storage = $this->_storage->get('assets', $this->hash, '');

            $this->_cache->set($this->hash, $html, 'price_elements', true);
            $this->_cache->set($this->hash, $storage, 'price_assets', true);
        }

        return $html;
    }

    /**
     * Validate submission
     * @param $value
     * @param $params
     * @return mixed
     * @throws AppValidatorException
     */
    public function validateSubmission($value, $params)
    {
        $this->app->validator;
        if ((int)$params->get('required', 0)) {

            $variations = array_filter($value->get('variations'));
            $list       = $this->getList($variations);

            if ($list->count() === 0) {
                throw new AppValidatorException('This field is required');
            }

            foreach ($list as $variant) {
                if (count($variant->data()) === 0) {
                    throw new AppValidatorException('This field is required');
                }
            }
        }

        return $value;
    }

    /**
     * Clear variation list.
     * @return $this
     */
    public function clearList()
    {
        if ($this->_list !== null) {
            $this->_list->clear();
        };
        $this->_list = null;

        return $this;
    }

    /**
     * Check if isset variant list object.
     * @return bool
     */
    public function hasList()
    {
        return ($this->_list instanceof JBCartVariantList && $this->_list->count());
    }

    /**
     * @param array $data
     * @param array $options
     * @return JBCartVariantList
     */
    public function getList($data = array(), $options = array())
    {

        $data    = (array)$data;
        $hasData = !empty($data);

        if ($hasData || !$this->hasList()) {
            $data = $hasData ? $data : $this->defaultData();
            // If basic variant not exists, add him.
            if ($hasData && !array_key_exists(self::BASIC_VARIANT, $data)) {
                $data[self::BASIC_VARIANT] = $this->getData(self::BASIC_VARIANT);
            }

            if (!array_key_exists('selected', $options)) {
                $options['selected'] = $this->get('values.' . $this->defaultKey());
            }
            if (!array_key_exists('values', $options)) {
                $options['values'] = $this->getValues($options['selected']);
            }
            // Create variants objects.
            $data = $this->prepareList($data);

            // Merge default options with incoming.
            $options = array_merge($this->itemOptions($options), array(
                'element' => $this,
            ));

            $this->createList($data, $options);

            if ($this->_template === null) {
                //$this->clearList(); // fatal on edit and submission
            }
        }

        return $this->_list;
    }

    /**
     * Build array of JBCartVariant instances from item data
     * @param array $variations
     * @param array $options
     * @return array
     */
    public function prepareList(array $variations, $options = array())
    {
        ksort($variations, SORT_NUMERIC);

        $list   = array();
        $params = array_merge((array)$this->getConfigs(), (array)$this->getParameters());
        foreach ($variations as $id => $data) {
            $list[$id] = $this->doVariant(array_keys($params), array(
                'id'   => $id,
                'data' => $data,
            ));
        }

        return $list;
    }

    /**
     * Build JBCartVariant instance from items data
     * @param array $data Array of price elements data.
     * @param int   $id   Variant id
     * @return JBCartVariant
     */
    public function buildVariant($data = array(), $id = self::BASIC_VARIANT)
    {
        $elements = array_merge($this->getConfigs(), $this->getParameters());
        $variant  = $this->doVariant(array_keys($elements), array(
            'id'   => $id,
            'data' => $data,
        ));

        return $variant;
    }

    /**
     * @param array $ids
     * @param array $options
     * @return mixed
     */
    public function doVariant(array $ids, array $options = array())
    {
        $options['id'] = isset($options['id']) ? $options['id'] : self::BASIC_VARIANT;

        $elements = $this->_getElements($ids, $options['id']);
        $variant  = $this->_storage->create('variant', array(
            'elements' => $elements,
            'options'  => $options,
        ));

        return $variant;
    }

    /**
     * @param  array $options
     * @return array
     */
    public function itemOptions(array $options = array())
    {
        return array_merge(array(
            'item_id'    => $this->_item ? $this->_item->id : null,
            'item_name'  => $this->_item ? $this->_item->name : null,
            'element_id' => $this->identifier,
            'layout'     => $this->_layout,
            'template'   => $this->_template,
            'hash'       => $this->hash,
            'isOverlay'  => $this->isOverlay,
            'cache'      => $this->cache,
            'showAll'    => $this->showAll,
            'default'    => $this->_item ? $this->defaultKey() : self::BASIC_VARIANT,
        ), (array)$options);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        $args = func_get_args();
        $type = array_shift($args);

        if (method_exists($this, "get{$type}key")) {
            return call_user_func_array(array($this, "get{$type}key"), (array)$args);
        }

        return null;
    }

    /**
     * Get element data in JSONData Object
     * @return JSONData
     */
    public function data()
    {
        return new AppData(parent::data());
    }

    /**
     * Get variation data.
     * @param int   $key     Index/key of variant.
     * @param mixed $default Default value
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return $this->_item->elements->find($this->identifier . '.variations.' . $key, $default);
    }

    /**
     * Get elements default data.
     * Default data are consist of the basic variant @see ElementJBPrice::BASIC_VARIANT
     * and default variant                           @see ElementJBPrice::defaultKey()
     *
     * @return array
     */
    public function defaultData()
    {
        $list    = array($this->getData(self::BASIC_VARIANT));
        $default = $this->defaultKey();

        if ($default !== self::BASIC_VARIANT) {
            $list[$default] = $this->getData($default);
        }

        return $list;
    }

    /**
     * Get default variant index/key.
     * @return int
     */
    public function defaultKey()
    {
        return $this->_item ? (int)$this->get('default_variant', self::BASIC_VARIANT) : self::BASIC_VARIANT;
    }

    /**
     * Set new variant as default.
     * @param  int|array $key Number of variant.
     * @return $this
     */
    public function setDefault($key)
    {
        $old = $this->defaultKey();

        if ($old !== (int)$key) {
            $this->set('default_variant', $key);
            if ($this->_list instanceof JBCartVariantList) {
                $this->_list->setDefault($key);
            }
        }

        return $this;
    }

    /**
     * @param array $template
     * @param array $values
     */
    abstract public function ajaxChangeVariant($template = array('default'), $values = array());

    /**
     * Ajax add to cart method
     * @param array $template
     * @param int   $quantity
     * @param array $values
     * @return
     */
    abstract public function ajaxAddToCart($template = array('default'), $quantity = 1, $values = array());

    /**
     * @param string $template Template to render
     * @param string $layout   Current price layout
     * @param string $hash     Hash string for communication between the elements in/out modal window
     */
    abstract public function ajaxModalWindow($template = 'default', $layout = 'default', $hash);

    /**
     * Remove from cart method
     * @param string|bool $key
     * @return mixed
     */
    abstract public function ajaxRemoveFromCart($key = null);

    /**
     * Render default variant when he changes.
     * Calling this method after calculation in @see ElementJBPrice::ajacChangeVariant()
     * @return array
     */
    protected function renderVariant()
    {
        $result = array();

        $parameters = $this->loadParams();
        $variant    = $this->_list->current();
        $storage    = $this->_storage;
        array_walk($parameters, function ($params) use ($variant, $storage, &$result) {
            if (($element = $variant->get($params['identifier'])) && $element->isCore()) {
                $params  = new AppData($params);
                $options = array(
                    'template' => $params->get('_template'),
                    'position' => $params->get('_position'),
                    'index'    => $params->get('_index'),
                );
                $element = $storage->configure($element, $options);

                // Render element new data.
                $data = $element->renderAjax($params);

                //return data if not null
                if ($data !== null) {
                    $key = strtolower('jselement' . implode($options));

                    $result[$params->get('type')][$key] = $data;
                }
            }
        });

        return $result;
    }

    /**
     * Get interface params for all core elements that used in widgets.
     * @return array
     */
    public function elementsInterfaceParams()
    {
        $options = array();

        $parameters = $this->loadParams();
        $variant    = $this->_list->current();
        foreach ($parameters as $params) {
            if (($element = $variant->get($params['identifier'])) && $element->isCore()) {
                $params = new AppData($params);

                $element = $this->_storage->configure($element, array(
                    'index'    => $params->get('_index'),
                    'position' => $params->get('_position'),
                    'template' => $params->get('_template'),
                ));

                $options[$element->getElementType()] = $element->interfaceParams($params);
            }
        }

        return $options;
    }

    /**
     * Get missing elements names.
     * @param $values
     * @return array
     */
    protected function getMissing($values)
    {
        $missing = array_map(function ($element) {
            return $element ? $element->getName() : null;
        },
            array_diff_key($this->getRequired(), $values)
        );

        return $missing;
    }

    /**
     * Get required elements.
     * @return array
     */
    protected function getRequired()
    {
        $variant = $this->_list->current();

        return $variant->getRequired();
    }

    /**
     * @param array $values
     * @return mixed
     */
    protected function getValues($values = array())
    {
        $result = array();
        $values = (array)$values;

        foreach ($values as $key => $value) {
            if (($element = $this->getElement($key)) && !$element->isCore()) {
                $name          = $element->bindData($value)->getName();
                $result[$name] = $element->getValue(true);
            }
        }

        return $result;
    }

    /**
     * @param  string $id
     * @return array
     */
    public function elementOptions($id)
    {
        return (array)$this->get('selected.' . $id, array());
    }

    /**
     * Get element template in layout.
     * @return string|null
     */
    public function getTemplate()
    {
        return is_array($this->_template) ? end($this->_template) : $this->_template;
    }

    /**
     * Set protected property value.
     * @param $template - property value
     * @deprecated
     * @return ElementJBPrice
     */
    public function setTemplate($template)
    {
        if ($this->_template !== $template && !$this->_helper->isEmpty($template)) {
            if ($this->_template !== null) {
                $this->_list = $this->hash = $this->_parameters = null;
            }
            $this->_template = $template;
        }

        return $this;
    }

    /**
     * Get element search data for sku table
     * @return array
     */
    public function getIndexData()
    {
        $variations = (array)$this->get('variations', array());
        $indexData  = array();
        if (count($variations)) {
            $list = $this->getList($variations);
            unset($variations);
            $oldKey = $this->defaultKey();
            /** @type JBCartVariant $variant */
            foreach ($list->all() as $variant) {
                $this->setDefault($variant->getId());
                $indexData = array_merge((array)$indexData, $this->getVariantData($variant));
            }
            $this->setDefault($oldKey);
            $list->current()->setId(-1);

            $indexData = array_merge($indexData, $this->getVariantData($list->current()));
        }

        return $indexData;
    }

    /**
     * @param JBCartVariant $variant
     * @return array
     */
    protected function getVariantData(JBCartVariant $variant)
    {
        $vars = $this->app->jbvars;
        $data = array();
        foreach ($variant->all() as $element) {
            $value = $element->getSearchData();
            $value = $this->_helper->getValue($value);

            $string  = (string)$value;
            $numeric = is_numeric($value) ? $vars->number($value) : null;
            $date    = $this->_helper->isDate($value) ?: null;

            if (
                (!$this->_helper->isEmpty($string) || (is_numeric($numeric) || !$this->_helper->isEmpty($date))) ||
                ($element->id() == '_value')
            ) {
                $key = $this->_item->id . '__' . $this->identifier . '__' . $variant->getId() . '__' . $element->id();

                $data[$key] = array(
                    'item_id'    => $this->_item->id,
                    'element_id' => $this->identifier,
                    'param_id'   => $element->id(),
                    'value_s'    => $string,
                    'value_n'    => $numeric,
                    'value_d'    => $date,
                    'variant'    => $variant->getId(),
                );
            }
        }

        return $data;
    }

    /**
     * Get control name
     * @param string $id
     * @param bool   $array
     * @return string
     */
    public function getControlName($id, $array = false)
    {
        return 'elements[' . $this->identifier . '][' . $id . ']' . ($array ? '[]' : '');
    }

    /**
     * Get element layout path and use override if exists
     * @param string|null $layout
     * @return string
     */
    public function getLayout($layout = null)
    {
        // init vars
        $type = $this->getElementType();

        // set default
        if (!$layout) {
            $layout = $type . '.php';
        }

        $path = $this->app->path->path('elements:' . $type . '/tmpl/' . $layout);
        if (!$path) {
            $parent = strtolower(str_replace('Element', '', get_parent_class($this)));
            $path   = $this->app->path->path('elements:' . $parent . '/tmpl/' . $layout);
        }

        return $path;
    }

    /**
     * @param  string    $id      Identifier of element
     * @param int|string $variant Variant index/key
     * @return JBCartElementPrice
     */
    public function getElement($id, $variant = self::BASIC_VARIANT)
    {
        // has element already been loaded?
        if (!$element = ($this->_storage->hasElement($id) ? $this->_storage->getElement($id) : null)) {
            if ($config = $this->getElementConfig($id)) {
                $group = $config->get('group');
                $type  = $config->get('type');

                if ($element = $this->_storage->create('element', array(
                    'app'        => $this->app,
                    'type'       => $type,
                    'group'      => $group,
                    'identifier' => $id,
                    'config'     => $config,
                    'class'      => 'JBCartElement' . $group . $type,
                    'variant'    => $variant,
                ))
                ) {
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        if (!$element->canAccess()) {
            return false;
        }

        $element = clone $element;
        $element = $this->_storage->configure($element, $this->itemOptions(array(
            'identifier' => $id,
            'variant'    => $variant,
            'template'   => $this->getTemplate(),
            'layout'     => $this->_layout,
            'jbprice'    => $this,
            'config'     => $this->getElementConfig($id),
        )));

        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->_getElements(array_keys(array_diff_key((array)$this->getConfigs(), (array)$this->getParameters())));
    }

    /**
     * @param $id
     * @return AppData|bool
     */
    public function getElementConfig($id)
    {
        if ($config = $this->_storage->getConfig($this->key('public'), $id)) {
            return new AppData($config);
        }

        $parameter = $this->getParameter($id);

        return (int)$parameter->get('system', 0) ? $parameter : false;
    }

    /**
     * @param     $ids
     * @param int $key
     * @return array
     */
    public function _getElements($ids, $key = self::BASIC_VARIANT)
    {
        $elements = array();
        if ($ids) {
            foreach ($ids as $id) {
                if ($element = $this->getElement($id, $key)) {
                    $elements[$id] = $element;
                }
            }

            return $elements;
        }

        return $elements;
    }

    /**
     * Get default currency.
     * @return string
     */
    public function currency()
    {
        $default = JBCart::val()->cur();
        $params  = $this->getParameter('_currency');

        $currencyList = $params->get('currency_list', array());
        if (count($currencyList) == 1 && !in_array('all', $currencyList)) {
            reset($currencyList);
            $key = current($currencyList);
            return $key;
        }

        if ((array)$params) {
            return $params->get('currency_default', $default);
        }

        $variant = $this->getList()->current();

        if ($variant->has('_value')) {
            return $variant->getValue(false, '_value')->cur();
        }

        return $default;
    }

    /**
     * Is in stock item.
     * @param float|int $quantity
     * @param int       $variant
     * @return bool
     */
    public function inStock($quantity, $variant = self::BASIC_VARIANT)
    {
        if ($this->getVariant($variant)->inStock($quantity)) {
            return true;
        }

        return false;
    }

    /**
     * Get balance from variant.
     * @param $key
     * @return mixed
     */
    public function getBalance($key)
    {
        return $this->getVariant($key)->getValue(true, '_balance', false);
    }

    /**
     * Bind and validate data.
     * @param array $data
     */
    public function bindData($data = array())
    {
        if ($this->_item !== null) {
            $hashTable = array();
            if (array_key_exists('variations', $data)) {
                $list = $this->prepareList($data['variations']);
                unset($data['variations']);

                // generate hashes
                $values = (array)$this->get('values', array());
                if ($values) {
                    $hashTable = array_map(function ($array) {
                        asort($array);

                        return md5(serialize($array));
                    }, $values);
                }
                //Check if variant with same options exists
                $list = array_filter($list, function ($variant) use (&$hashTable) {
                    return ($variant->isBasic() || $variant->count('simple') && !in_array($variant->hash(), $hashTable, true))
                        ? $hashTable[$variant->getId()] = $variant->hash() //add variant hash to array based on simple elements values
                        : null;
                });

                //leave only unique hashes. The array keys are the keys of valid variants.
                $hashTable = array_unique($hashTable);
                //get valid variants
                $list = array_intersect_key($list, $hashTable);

                //generate array values and selected
                if (count($list)) {
                    foreach ($list as $key => $variant) {
                        $variant->setId($key)->bindData();

                        $this->bindVariant($variant);
                    }
                }

                if (isset($data['default_variant']) && !array_key_exists($data['default_variant'], $list)) {
                    unset($data['default_variant']);
                }
            }

            if (count($data)) {
                $result = $this->_item->elements->get($this->identifier);

                foreach ($data as $_id => $unknown) {
                    $result[$_id] = is_string($unknown) ? JString::trim($unknown) : $unknown;
                }
                $this->_item->elements->set($this->identifier, (array)$result);
            }
        }
    }

    /**
     * @param JBCartVariant $variant
     * @return $this
     */
    public function bindVariant(JBCartVariant $variant)
    {
        if ($this->_item !== null) {
            $simple = $variant->getSimple();
            $data   = $this->data();

            $values     = (array)$data->get('values', array());
            $selected   = (array)$data->get('selected', array());
            $variations = (array)$data->get('variations', array());

            $variations[$variant->getId()] = $variant->data();
            if (!$variant->isBasic()) {
                $values[$variant->getId()] = array_filter(array_map(create_function('$element',
                    'return JString::strlen($element->getValue(true)) > 0 ? (array)$element->data() : null;'), $simple
                ));
                $_selected                 = array_filter(array_map(create_function('$element', 'return JString::strlen($element->getValue(true)) > 0
                    ? JString::trim($element->getValue(true)) : null;'), $simple)
                );
                if ($_selected) {
                    foreach ($_selected as $key => $value) {
                        $selected[$key][$value] = $value;
                    }
                }
            }
            // Reset keys
            $variations = array_values($variations);
            $values     = array_values($values);

            $data->set('variations', $variations);
            $data->set('selected', $selected);
            $data->set('values', $variant->isBasic() ? array(self::BASIC_VARIANT => array()) : $values);

            $this->_item->elements->set($this->identifier, (array)$data);
        }

        return $this;
    }

    /**
     * @param  string $path
     * @return bool
     */
    public function toStorage($path)
    {
        $ext = JFile::getExt($path);
        $this->app->jbassets->$ext($path);

        return $this->_storage->add('assets', $path, $this->hash);
    }

    /**
     * Load assets.
     * @return $this
     */
    public function loadEditAssets()
    {
        /** @type JBAssetsHelper $jbAssets */
        $jbAssets = $this->app->jbassets;
        $jbAssets->admin();
        $jbAssets->less(array(
            'jbassets:less/admin.less',
            'elements:jbprice/assets/less/edit.less',
        ));
        if ((int)$this->config->get('mode', 1)) {
            $jbAssets->js('jbassets:js/admin/validator.js');
        }
        $jbAssets->js('elements:jbprice/assets/js/edit.js');

        return $this;
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        /** @type JBAssetsHelper $jbAssets */
        $jbAssets = $this->app->jbassets;

        // Include libraries
        $jbAssets->tools();
        $jbAssets->jQueryUI();
        $jbAssets->fancybox();

        //Add assets from cache
        if ($this->cache) {
            $assets = $this->_cache->get($this->hash, 'price_assets', true);
            if (!empty($assets)) {
                foreach ($assets as $asset) {
                    if ($ext = JFile::getExt($asset)) {
                        $jbAssets->$ext($asset);
                    }
                }

                return true;
            }
        }
        $assets = 'elements:jbprice/assets/';
        // Important. Main Price widget.
        $this->toStorage($assets . 'js/jbprice.js');
        $this->toStorage($assets . 'less/jbprice.less');

        // Important. Default widget for price elements.
        $this->toStorage('cart-elements:core/price/assets/js/price.js');

        return parent::loadAssets();
    }

    /**
     * @param  int $id
     * @return JBCartVariant|mixed
     * @throws JBCartVariantListException
     */
    public function getVariant($id = self::BASIC_VARIANT)
    {
        try {
            $variant = $this->getList()->get($id);
        } catch (JBCartVariantListException $e) {
            $ids     = array_keys(array_merge((array)$this->getConfigs(), (array)$this->getParameters()));
            $variant = $this->doVariant($ids, array(
                'id'   => $id,
                'data' => $this->getData($id),
            ));

            $this->_list->set($id, $variant);
        }

        return $variant;
    }

    /**
     * Load element renderer params.
     * @param string $id Element id.
     * @return AppData
     */
    public function getParameter($id)
    {
        $params = (array)$this->getParameters();

        return new AppData(isset($params[$id]) ? $params[$id] : array());
    }

    /**
     * Load all params
     * @return array
     */
    protected function getConfigs()
    {
        return $this->_storage->get('configs', $this->key('public'), array());
    }

    /**
     * @todo If template is not set && JDEBUG === 1  throw exception
     * @param string|null $template
     * @return array
     * @throws ElementJBPriceException
     */
    protected function getParameters($template = '')
    {
        if ($this->getTemplate() === null) {
            //throw new ElementJBPriceException('Template is not set.');
            return array();
        }

        if ($template === '') {
            $parameters = $this->loadParams();

        } else {
            $access     = $this->getPrivateKey($this->getTemplate());
            $parameters = $this->_storage->get('parameters', $access, array());
        }
        $parameters = $this->app->jbarray->index($parameters, 'identifier');

        return $parameters;
    }

    /**
     * Load and merge params for one or more price templates.
     * @return array
     */
    protected function loadParams()
    {
        if ($this->_parameters === null) {
            $templates  = (array)$this->_template;
            $parameters = array();
            foreach ($templates as $template) {
                $params = $this->_storage->get('parameters', $this->getPrivateKey($template), array());
                if ($params) {
                    array_walk($params, function ($parameter) use (&$parameters) {
                        $parameters[] = $parameter;
                    });
                }
            }

            $this->_parameters = $parameters;
        }

        return $this->_parameters;
    }

    /**
     * @param array|AppData $params
     * @return string
     */
    protected function getHash($params = array())
    {
        if ($this->hash !== null) {
            return $this->hash;
        }

        $this->hash = $this->encrypt(array(
            $this->identifier,
            $this->getItem()->id,
            (array)$params,
            (array)$this->getItem()->elements->get($this->identifier),
            (array)$this->getConfigs(),
            (array)$this->getParameters($this->getTemplate()),
            (array)$this->config,
            $this->_template,
            $this->_layout,
            (array)JFactory::getUser()->groups,
        ));

        return $this->hash;
    }

    /**
     * Create variant list object and set to the price property.
     *
     * @param array $list    Array of variant list objects*
     * @param array $options By default item and price options
     *
     * @return JBCartVariantList
     * @throws ElementJBPriceException
     */
    protected function createList($list, $options)
    {
        $object = new JBCartVariantList($list, $options);

        $this->setList($object);

        return $object;
    }

    /**
     * @param JBCartVariantList $list
     * @return $this
     * @throws ElementJBPriceException
     */
    protected function setList($list)
    {
        if (!$list instanceof JBCartVariantList) {
            throw new ElementJBPriceException('Type of variable $list in method - ' . __FUNCTION__ . ' is not correct.');
        }

        $this->_list = $list;

        return $this;
    }

    /**
     * @return string
     */
    protected function getPublicKey()
    {
        return JBCart::CONFIG_PRICE . '.' . $this->identifier;
    }

    /**
     * @param string $template
     * @return string
     */
    protected function getPrivateKey($template)
    {
        return JBCart::CONFIG_PRICE_TMPL . ".$this->identifier." . $template;
    }

    /**
     * Get unique string by some properties.
     * @return string
     * @see      Item::$id, $identifier, defaultKey(), getValues().
     *
     */
    protected function getSessionKey()
    {
        return $this->getList()->getSessionKey();
    }

    /**
     * @param  array $data
     * @return string
     */
    private function encrypt(array $data = array())
    {
        return md5(serialize((array)$data));
    }

    /**
     *
     */
    public function cleanVariations()
    {
        $this->set('variations', array());
        $this->set('selected', array());
        $this->set('values', array());
    }
}

/**
 * ElementJBPriceException identifies an Exception in the Element class
 * @see ElementException
 */
class ElementJBPriceException extends ElementException
{
    /**
     * A App object.
     * @type App
     */
    public $app;

    /**
     * Detect ajax request.
     * @type bool
     */
    protected $isAjax;

    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->app    = App::getInstance('zoo');
        $this->isAjax = $this->app->jbrequest->isAjax();
    }

    /**
     * Converts the exception to a human readable string
     *
     * @return string The error message
     *
     * @since 1.0.0
     */
    public function __toString()
    {
        $message = $this->getMessage();
        if ($this->isAjax) {
            $this->app->jbajax->send(array('message' => $message), false);
        }

        return $message;
    }
}