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
     * @type Array of core/unique price params config
     */
    public $filter_params;

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

    /**
     * //TODO это навреное не layout а template
     * Price template that chosen in layout
     * @type null
     */
    protected $_filter_template;

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
        $params = new AppData($params);
        $config = $this->setTemplate($params->get('template', 'default'));

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
                $hash = $this->hash($params);
                $list = $this->getList($variations);

                return parent::renderLayout($layout, array(
                    'hash'        => $hash,
                    'variations'  => $list->all(),
                    'default'     => $this->defaultKey(),
                    'renderer'    => $renderer,
                    'countSimple' => count(array_filter($this->getElements(), function ($element) {
                        return !$element->isCore() ? $element : null;
                    }))
                ));
            }

            return null;
        }

        return $this->renderWarning('_warning.php', JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_ELEMENTS'));
    }

    /**
     * Renders the element
     * @param array|AppData $params Render parameters
     * @return string|void
     */
    public function render($params = array())
    {
        $params = new AppData($params);
        $hash   = $this->setTemplate($params->get('template', 'default'))->hash($params);
        $this->loadAssets();

        if (!$this->cache || ($this->cache && !$cache = $this->_cache->get($hash, 'price_elements', true))) {

            $template = $this->getTemplate();
            $_layout  = $this->setLayout($params->get('_layout'))->layout();

            $renderer = $this->app->jbrenderer->create('jbprice');
            $variant  = $this->getList()->current();

            $data = $renderer->render($template, array(
                '_variant'   => $variant,
                'element_id' => $this->identifier,
                'variant'    => $variant->getId(),
                'layout'     => $_layout
            ));

            //Must be after renderer
            $elements = $this->elementsInterfaceParams();
            if ($layout = $this->getLayout('render.php')) {
                return $this->renderLayout($layout, array(
                    'hash'       => $hash,
                    'data'       => $data,
                    'elements'   => $elements,
                    'variantUrl' => $this->app->jbrouter->element($this->identifier, $this->_item->id, 'ajaxChangeVariant')
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
                'element'    => $this->identifier
            ));
            $attributes = $this->app->jbhtml->buildAttrs(array(
                'href'   => $link,
                'target' => '_blank'
            ));

            $message = '<a ' . $attributes . '>' . $message . '</a>';

        }
        if (($layout = $this->getLayout($template)) && ($message !== '' && $message !== null)) {
            return parent::renderLayout($layout, array(
                'message' => $message
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
        if ((int)$params->get('required', 0)) {
            $basic = $value->get('basic');
            $this->app->validator
                ->create('textfilter', array(
                    'required' => $params->get('required')
                ))
                ->clean($basic['_value']);
        }

        return $value;
    }

    /**
     * Get default variant index
     * @return int
     */
    public function defaultKey()
    {
        return (int)$this->_item->elements->find($this->identifier . '.default_variant', self::BASIC_VARIANT);
    }

    /**
     * @param array $variations
     * @param array $options
     * @return JBCartVariantList
     */
    public function getList($variations = array(), $options = array())
    {
        if (!$this->_list instanceof JBCartVariantList || $this->_list->count() === 0) {
            if (!count($variations)) {
                $variations = $this->defaultList();
            }

            if (!array_key_exists(0, $variations)) {
                $variations[0] = $this->getData(0);
            }
            $list = $this->build($variations);

            if(array_key_exists('selected', $options) && !array_key_exists('values', $options))
            {
                $options['values'] = $this->getValues($options['selected']);
            }
            $values  = $this->get('values.' . $this->defaultKey());
            $options = array_merge(array(
                'element'    => $this,
                'element_id' => $this->identifier,
                'item_id'    => $this->_item->id,
                'item_name'  => $this->_item->name,
                'template'   => $this->_template,
                'layout'     => $this->layout(),
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay,
                'cache'      => $this->cache,
                'showAll'    => $this->showAll,
                'values'     => $this->getValues($values),
                'selected'   => $values,
                'currency'   => $this->currency(),
                'default'    => $this->defaultKey()
            ), (array)$options);

            $this->setList(new JBCartVariantList($list, $options));

            return $this->_list;
        }

        if (count($variations)) {
            $variations = $this->build($variations);
            $this->_list->add($variations);

            if (count($options)) {
                $this->_list->setOptions((array)$options);
            }
        }

        return $this->_list;
    }

    /**
     * @param JBCartVariantList $list
     * @throws ElementJBPriceException
     */
    public function setList($list)
    {
        if (!$list instanceof JBCartVariantList) {
            throw new ElementJBPriceException('Type of variable $list in method - ' . __FUNCTION__ . ' is not correct.');
        }

        $this->_list = $list;

        return $this;
    }

    /**
     * Build array of JBCartVariant instances from item data
     * @param array $variations
     * @param array $options
     * @return array
     */
    public function build($variations = array(), $options = array())
    {
        if (count($variations) === 0) {
            return $variations;
        }
        ksort($variations);
        $list = array();

        $params = array_merge($this->getConfigs(), $this->loadParams());
        foreach ($variations as $id => $data) {
            $elements = array_merge($params, $data);
            $elements = $this->_getElements(array_keys($elements), $id);

            $list[$id] = $this->variant($elements, array(
                'id'   => $id,
                'data' => $data
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
        $elements = array_merge($this->getConfigs(), $this->loadParams());
        $elements = $this->_getElements(array_keys($elements), $id);

        $variant  = $this->variant($elements, array(
            'id'   => $id,
            'data' => $data
        ));

        return $variant;
    }

    /**
     * @param array $elements
     * @param array $options
     * @return mixed
     */
    public function variant($elements, $options = array())
    {
        $variant = $this->_storage->create('variant', array(
            'elements' => $elements,
            'options'  => $options
        ));

        return $variant;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function key()
    {
        $args = func_get_args();
        $type = array_shift($args);

        if(method_exists($this, "get{$type}key"))
        {
            return call_user_func_array(array($this, "get{$type}key"), (array)$args);
        }

        return null;
    }

    /**
     * @param int  $key
     * @param null $default
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return $this->_item->elements->find($this->identifier . '.variations.' . $key, $default);
    }

    /**
     * @return array
     */
    public function defaultList()
    {
        $key = $this->defaultKey();

        return array(
            self::BASIC_VARIANT => $this->getData(self::BASIC_VARIANT),
            $key                => $this->getData($key)
        );
    }

    /**
     * Set new variant as default.
     * @param  int|array $key Number of variant.
     * @return $this
     */
    public function setDefault($key)
    {
        $old = $this->defaultKey();

        if ($old != $key) {
            $this->set('default_variant', $key);
            if ($this->_list instanceof JBCartVariantList) {
                $this->_list->setDefault($key);
            }
        }

        return $this;
    }

    /**
     * Get currency
     * @return string
     */
    public function currency()
    {
        $default = JBCart::val()->cur();
        $params  = $this->getParameter('_currency');

        if ($params) {
            return $params->get('currency_default', $default);
        }

        return $default;
    }

    /**
     * @param array $template
     * @param array  $values
     */
    abstract public function ajaxChangeVariant($template = array('default'), $values = array());

    /**
     * Ajax add to cart method
     * @param array $template
     * @param int    $quantity
     * @param array  $values
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
     * Get interface params for all core elements that used in widgets.
     * @return array
     */
    public function elementsInterfaceParams()
    {
        $options = array();
        $variant = $this->_list->current();

        $parameters = $this->_storage->get('parameters', $this->getPrivateKey($this->getTemplate()), array());
        foreach ($parameters as $params) {
            $element = $variant->get($params['identifier']);
            if ($element && $element->isCore()) {
                $element->setIndex($params['_index'])->setPosition($params['_position']);
                $options[$element->getElementType()] = $element->interfaceParams(new AppData($params));
            }
        }

        return $options;
    }

    /**
     * Get missing elements names.
     * @param $values
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
    public function getValues($values = array())
    {
        $result = array();
        $values = (array)$values;
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $element = $this->getElement($key);
                if ($element && !$element->isCore()) {
                    $element->bindData($value);

                    $result[$element->getName()] = $element->getValue(true);
                }
            }
        }

        return $result;
    }

    /**
     * Get all options for element.
     * Used in element like select, color, radio etc.
     * @param $identifier
     * @return array
     */
    public function findOptions($identifier)
    {
        if (empty($identifier)) {
            return array();
        }

        return $this->get('selected.' . $identifier, array());
    }

    /**
     * @param $identifier
     * @return array
     */
    public function elementOptions($identifier)
    {
        return $this->findOptions($identifier);
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
     * Get element layout.
     * @return null|string
     */
    public function layout()
    {
        return $this->_layout;
    }

    /**
     * Set protected property value
     * @param $key   - property name
     * @param $value - property value
     * @return $this
     */
    public function setProp($key, $value)
    {
        $this->$key = $value;

        return $this;
    }

    /**
     * @param $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;

        return $this;
    }

    /**
     * Set protected property value.
     * @param $template - property value
     * @deprecated
     * @return ElementJBPrice
     */
    public function setTemplate($template)
    {
        if (!$this->_helper->isEmpty($template) && $this->_template !== $template) {
            if ($this->_template !== null) {
                $this->_list = $this->hash = $this->_parameters = null;
            }
            $this->_template = $template;
        }

        return $this;
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
     * Get element search data for sku table
     * @return array
     */
    public function getIndexData()
    {
        $variations = (array)$this->get('variations', array());
        $data       = array();
        if (count($variations)) {
            $list = $this->getList($variations);
            unset($variations);

            $_default = $this->defaultKey();
            /** @type JBCartVariant $variant */
            foreach ($list->all() as $key => $variant) {
                $this->setDefault($key);

                $data = array_merge((array)$data, $this->getVariantData($variant));
            }
            $this->setDefault($_default);

            $default = $list->current();
            $default->setId(-1);

            $data = array_merge($data, $this->getVariantData($default));
        }

        return $data;
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
            $numeric = $vars->number($value) ? $vars->number($value): null;
            $date    = $this->_helper->isDate($value) ?: null;

            if (!$this->_helper->isEmpty($string) || $numeric || !$this->_helper->isEmpty($date)) {
                $key = $this->_item->id . '__' . $this->identifier . '__' . $variant->getId() . '__' . $element->id();

                $data[$key] = array(
                    'item_id'    => $this->_item->id,
                    'element_id' => $this->identifier,
                    'param_id'   => $element->id(),
                    'value_s'    => $string,
                    'value_n'    => $numeric,
                    'value_d'    => $date,
                    'variant'    => $variant->getId()
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
     * @param  string    $id      elementID
     * @param int|string $variant variant key
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
                    'variant'    => $variant
                ))
                ) {
                    if (!$element->canAccess()) {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $element = clone $element;
        $element = $this->_storage->configure($element, array(
            'variant'    => $variant,
            'hash'       => $this->hash,
            'item_id'    => $this->_item ? $this->_item->id : 0,
            'element_id' => $this->identifier,
            'identifier' => $id,
            'cache'      => $this->cache,
            'isOverlay'  => $this->isOverlay,
            'showAll'    => $this->showAll,
            'template'   => $this->getTemplate(),
            'layout'     => $this->layout()
        ));
        $element->setJBPrice($this);

        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->_getElements(array_keys(array_diff_key((array)$this->getConfigs(), (array)$this->getParameters($this->getTemplate()))));
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

        $this->_getFilterParams();
        if (isset($this->filter_params[$id])) {
            return new AppData($this->filter_params[$id]);
        }

        return $this->getParameter($id);
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
        return $this->getVariant($key)->getValue(true, '_balance');
    }

    /**
     * Bind and validate data.
     * @param array $data
     */
    public function bindData($data = array())
    {
        if (null !== $this->_item) {
            $hashes = array();

            if (array_key_exists('variations', $data)) {
                $list = $this->build($data['variations']);
                unset($data['variations']);

                // generate hashes
                $values = (array)$this->get('values', array());
                if (count($values)) {
                    $hashes = array_map(create_function('$data', ' return md5(serialize($data));'), $values);
                }

                /** @type JBCartVariant $variant */
                foreach ($list as $key => $variant) {
                    /** @type JBCartElementPrice $element */
                    if (($variant->isBasic()) || ($variant->count('simple') && !in_array($variant->hash(), $hashes, true))) {

                        //add variant hash to array based on simple elements values
                        $hashes[$key] = $variant->hash();
                    }
                }

                //leave only unique hashes. The array keys are the keys of valid variants.
                $hashes = array_unique($hashes);

                //get valid variants
                $list = array_intersect_key($list, $hashes);

                //generate array values and selected
                if (count($list)) {
                    foreach ($list as $key => $variant) {
                        $variant->setId($key)->bindData();

                        $this->bindVariant($variant);
                        $variant->clear();
                    }
                }
            }

            if (count($data)) {
                $result = $this->_item->elements->get($this->identifier);

                foreach ($data as $_id => $unknown) {
                    $result[$_id] = is_string($unknown) ? JString::trim($unknown) : $unknown;
                }
                $this->_item->elements->set($this->identifier, $result);
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

                $_selected = array_filter(array_map(create_function('$element', 'return JString::strlen($element->getValue(true)) > 0
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
        $jbAssets->less('elements:jbprice/assets/less/edit.less');
        $jbAssets->less('jbassets:less/admin.less');
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
                        $this->app->jbassets->$ext($asset);
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
     * @param int|string $id
     * @return JBCartVariant
     */
    public function getVariant($id = self::BASIC_VARIANT)
    {
        $array = $this->defaultList() + array(
                $id => $this->getData($id)
            );

        if (!$this->_list instanceof JBCartVariantList) {
            $this->getList($array);
        }

        if (!$this->_list->has($id)) {
            $variations = $this->build($array);

            $this->_list->add($variations);
        }

        return $this->_list->get($id);
    }

    /**
     * Load elements render params for @filter
     * @return array
     */
    public function _getFilterParams()
    {
        if (!$this->_filter_template) {
            return array();
        }

        if ($this->filter_params === null) {
            $config = JBCart::CONFIG_PRICE_TMPL_FILTER . '.' . $this->identifier . '.' . $this->_filter_template;

            $this->filter_params = (array)$this->_position->loadParams($config, true);
        }

        return $this->filter_params;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasParameter($id)
    {
        $parameters = $this->loadParams();
        foreach($parameters as $params) {
            if(isset($params[$id]))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Load element renderer params.
     * @param string $id Element id.
     * @return AppData
     */
    public function getParameter($id)
    {
        $params = $this->getParameters($this->getTemplate());

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
     * @param string $template
     * @return array
     * @throws ElementJBPriceException
     */
    protected function getParameters($template)
    {
        if ($this->getTemplate() === null)
        {
            //throw new ElementJBPriceException('Template is not set.');
            return array();
        }
        $template = !empty($template) ? $template : $this->getTemplate();
        $access   = $this->getPrivateKey($template);

        $parameters = $this->_storage->get('parameters', $access, array());
        $parameters = JBCart::getInstance()->index($parameters, 'identifier');

        return $parameters;
    }

    /**
     * Load and merge params for one or more price templates.
     * @return array
     */
    protected function loadParams()
    {
        if ($this->_parameters === null)
        {
            $templates  = (array)$this->_template;
            $parameters = array();
            foreach ($templates as $template) {
                $parameters = array_merge_recursive($parameters, $this->getParameters($template));
            }

            $this->_parameters = $parameters;
        }

        return $this->_parameters;
    }

    /**
     * @param array|AppData $params
     * @return string
     */
    protected function hash($params = array())
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
            $this->getTemplate(),
            (array)JFactory::getUser()->groups
        ));

        return $this->hash;
    }

    /**
     * Set price hash.
     */
    protected function getPublicKey()
    {
        return JBCart::CONFIG_PRICE . '.' . $this->identifier;
    }

    /**
     * @return string
     */
    protected function getPrivateKey($template)
    {
        return JBCart::CONFIG_PRICE_TMPL . ".$this->identifier." . $template;
    }

    /**
     * Get unique string by some properties.
     *
     * @param array|string|mixed $params Additional parameters for session key.
     * @see Item::$id, $identifier, defaultKey(), getValues().
     *
     * @return string
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
     * @param string     $message
     * @param int        $code
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