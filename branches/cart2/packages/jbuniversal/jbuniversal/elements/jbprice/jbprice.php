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
     * @type string Unique string for each item and his params
     */
    public $hash;

    /**
     * @type bool
     */
    public $isCache;

    /**
     * Is this is ElementJBPriceCalc
     * @type bool
     */
    public $isOverlay;

    /**
     * Show only selected options in elements
     * @type bool
     */
    public $showAll;

    /**
     * @var Array of core/unique price params config
     */
    public $filter_params;

    /**
     * @var JBCartPositionHelper
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
     * @var JBCartVariantList
     */
    protected $_list;

    /**
     * @type JBPriceHelper
     */
    protected $_helper;

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * Price template that chosen in layout
     * @var null|string
     */
    protected $_template;

    /**
     * Layout - full, teaser, submission etc.
     * @var null|string
     */
    protected $_layout;

    /**
     * //TODO это навреное не layout а template
     * Price template that chosen in layout
     * @var null
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
        $this->_config   = JBModelConfig::model();

        $this->_helper  = $this->app->jbprice;
        $this->_cache   = $this->app->jbcache;
        $this->_storage = $this->app->jbstorage;
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        parent::setType($type);

        if ($this->canAccess()) {
            $this->isCache = (bool)$this->config->get('cache', 0);
        }
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
        $config = $this->setTemplate($params->get('template', 'default'))->getParameters();
        $this->hash($params);

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
                    'countSimple' => count($this->getSimpleElements())
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
        $params   = new AppData($params);
        $template = $this->setTemplate($params->get('template', 'default'))->getTemplate();
        $hash     = $this->hash($params);

        $this->loadAssets();
        if (!$this->isCache || ($this->isCache && !$cache = $this->_cache->get($hash, 'price_elements', true))) {
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
                    'variantUrl' => $this->app->jbrouter->element($this->identifier, $this->_item->id, 'ajaxChangeVariant', array(
                        'template' => $template
                    ))
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
        if ($this->isCache) {
            $this->_cache->set($this->hash, $html, 'price_elements', true);

            $storage = $this->_storage->get('assets', $this->hash, '');

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
     * @return mixed
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

            $options = array_merge(array(
                'element'    => $this,
                'element_id' => $this->identifier,
                'item_id'    => $this->_item->id,
                'item_name'  => $this->_item->name,
                'template'   => $this->getTemplate(),
                'layout'     => $this->layout(),
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay,
                'cache'      => $this->isCache,
                'showAll'    => $this->showAll,
                'values'     => $this->get('values.' . $this->defaultKey(), array()),
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
        $list = array();

        ksort($variations);
        foreach ($variations as $id => $data) {
            $list[$id] = $this->buildVariant($data, $id);
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
        $elements = array_merge((array)$this->getConfigs(), (array)$this->getParameters(), (array)$data);
        $elements = $this->_getElements(array_keys($elements), $id);

        $variant = $this->_storage->create('variant', array(
            'elements' => $elements,
            'options'  => array(
                'id'       => $id,
                'elements' => $data
            )
        ));

        return $variant;
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
            (array)$this->getParameters(),
            (array)JFactory::getUser()->groups
        ));

        return $this->hash;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function key($type = 'template')
    {
        if ($type === 'private') {
            return $this->getPrivateKey();
        }

        if ($type === 'public') {
            return $this->getPublicKey();
        }

        return null;
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
    protected function getPrivateKey()
    {
        return JBCart::CONFIG_PRICE_TMPL . ".$this->identifier." . $this->getTemplate();
    }

    /**
     * @param array  $array
     * @param string $inner
     * @param string $outer
     * @return string
     */
    public function arrayToString($array = array(), $inner = ':', $outer = '///')
    {
        $output = array();

        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $output[] = $key;
                if (count($item) > 1) {
                    $inner = ':';
                    $outer = '|||';
                }

                $output[] = $this->arrayToString($item, $inner, $outer);
            } else {
                $output[] = $this->_helper->clean($key . $inner . $item);
            }
        }

        return implode($outer, $output);
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
    public function defaultData()
    {
        return $this->_item->elements->find($this->identifier . '.variations.0');
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
     * @param JBCartVariantList $list
     * @throws ElementJBPriceException
     */
    public function setList($list)
    {
        if (!$list instanceof JBCartVariantList) {
            throw new ElementJBPriceException('Type of variable $list in method - ' . __FUNCTION__ . ' is not correct.');
        }

        $this->_list = $list;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setDefault($key)
    {
        $old = (int)$this->get('default_variant', self::BASIC_VARIANT);

        if ($old !== (int)$key) {
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
     * @param string $template
     * @param array  $values
     */
    abstract public function ajaxChangeVariant($template = 'default', $values = array());

    /**
     * Ajax add to cart method
     * @param string $template
     * @param int    $quantity
     * @param array  $values
     * @return
     */
    abstract public function ajaxAddToCart($template = 'default', $quantity = 1, $values = array());

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
        if ($variant->count()) {
            foreach ($variant->all() as $element) {
                if ($element->isCore() && $params = $this->getParameter($element->id())) {
                    $params                              = new AppData($params);
                    $options[$element->getElementType()] = $element->interfaceParams($params);
                }
            }
        }

        return $options;
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
        return $this->_template;
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
        if ($template !== '' && $template !== null && $this->_template !== $template) {
            if ($this->_template !== null) {
                $this->_list = null;
                $this->hash  = null;
            }

            $this->_template = $template;
        }

        return $this;
    }

    /**
     * Check if calc element
     * @return bool
     */
    public function isOverlay()
    {
        return $this->isOverlay;
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

                $data = array_merge($data, $this->getVariantData($variant));
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
        foreach ($variant->all() as $paramId => $element) {
            $value = $element->getSearchData();
            $id    = $element->identifier;

            $date = $string = $num = null;
            if ($value instanceof JBCartValue) {
                $value->convert('eur');
                $string = $value->data(true);
                $num    = $value->val();
            } else {
                $value  = JString::trim((string)$value);
                $string = $value;
                $num    = $this->isNumeric($value) ? $vars->number($value) : null;
                $date   = $this->isDate($value);
            }

            if (($string !== '' && $string !== null) || (is_float($num) || is_int($num)) || ($date !== '' && $date !== null)) {
                $key = $this->_item->id . '__' . $this->identifier . '__' . $variant->getId() . '__' . $id;

                $data[$key] = array(
                    'item_id'    => $this->_item->id,
                    'element_id' => $this->identifier,
                    'param_id'   => $id,
                    'value_s'    => $string,
                    'value_n'    => $num,
                    'value_d'    => $date,
                    'variant'    => $variant->getId()
                );
            }
        }

        return $data;
    }

    /**
     * Check if value seems as numeric
     * @param $number
     * @return bool|int|string
     */
    public function isNumeric($number)
    {
        return $this->_helper->isNumeric($number);
    }

    /**
     * Check if value seems as date
     * @param $date
     * @return null|string
     */
    public function isDate($date)
    {
        $result = $this->app->jbdate->convertToStamp($date);

        return isset($result[0]) ? $result[0] : null;
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
     * @param null $layout
     * @return string
     */
    public function getLayout($layout = null)
    {
        // init vars
        $type = $this->getElementType();

        // set default
        if (empty($layout)) {
            $layout = $type . '.php';
        }

        $parent = strtolower(str_replace('Element', '', get_parent_class($this)));
        $class  = $this->getElementType();

        $layoutPath = $this->app->path->path('elements:' . $class . '/tmpl/' . $layout);
        if (empty($layoutPath)) {
            $layoutPath = $this->app->path->path('elements:' . $parent . '/tmpl/' . $layout);
        }

        return $layoutPath;
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
            'cache'      => $this->isCache,
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
        return $this->_getElements(array_keys(array_diff_key((array)$this->getConfigs(), (array)$this->getParameters())));
    }

    /**
     * @return array
     */
    public function getRenderElements()
    {
        return $this->_getElements(array_keys($this->getParameters()));
    }

    /**
     * @param $type
     * @return array
     */
    public function getElementsByType($type)
    {
        return array_filter($this->getElements(),
            create_function('$element', 'return $element->getElementType() == "' . $type . '";'));
    }

    /**
     * @return array
     */
    public function getSimpleElements()
    {
        return array_filter($this->getElements(),
            create_function('$element', 'return $element->isCore() == false;'));
    }

    /**
     * @return array
     */
    public function getSystemElementsParams()
    {
        return array_filter($this->getParameters(),
            create_function('$element', 'return $element[\'system\'] == 1;'));
    }

    /**
     * @param $identifier
     * @return null
     */
    public function getElementConfig($identifier)
    {
        if ($config = $this->_storage->get('config', $this->key('public'), $identifier)) {
            return new AppData($config);
        }
        $this->_getFilterParams();
        if (isset($this->filter_params[$identifier])) {
            return new AppData($this->filter_params[$identifier]);
        }

        return new AppData($this->getParameter($identifier));
    }

    /**
     * @param     $identifiers
     * @param int $key
     * @return array
     */
    public function _getElements($identifiers, $key = self::BASIC_VARIANT)
    {
        $params = array();
        if ($identifiers) {
            foreach ($identifiers as $identifier) {
                if ($param = $this->getElement($identifier, $key)) {
                    $params[$identifier] = $param;
                }
            }

            return $params;
        }

        return $params;
    }

    /**
     * Is in stock item
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
     * Get balance from variant
     * @param $key
     * @return mixed
     */
    public function getBalance($key)
    {
        return $this->getVariant($key)->getValue(true, '_balance');
    }

    /**
     * Bind and validate data
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
            $simple = $variant->simple();
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
            $variations = array_values($variations);
            $values     = array_values($values);

            $data->set('variations', $variations);
            $data->set('selected', $selected);
            $data->set('values', $values);

            $this->_item->elements->set($this->identifier, (array)$data);
        }

        return $this;
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
        if ((int)$this->config->get('mode', 1)) {
            $jbAssets->js('jbassets:js/admin/validator.js');
        }
        $jbAssets->js('elements:jbprice/assets/js/edit.js');

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
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        /** @type JBAssetsHelper $jbAssets */
        $jbAssets = $this->app->jbassets;

        // Include libraries
        $jbAssets->tools();
        $jbAssets->jqueryui();
        $jbAssets->fancybox();

        //Add assets from cache
        if ($this->isCache) {
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
     * @param array  $list   Array of keys.
     * @param string $target Can be 'value' or 'key'.
     *                       Show where to find the number of variant in the key or value of array.
     * @param bool   $addKey Add current key from list to array.
     * @param string $searchIn
     * @return array
     */
    public function quickSearch($list = array(), $target = 'value', $addKey = true, $searchIn = 'variations')
    {
        $variations = array();
        if (!empty($list)) {
            $data = $this->data();
            foreach ($list as $key => $value) {
                $result = $data->find($searchIn . '.' . $$target);
                if (!empty($result)) {
                    if ($addKey) {
                        $variations[$$target] = $result;
                    } else {
                        $variations = array_merge($variations, $result);
                    }
                }
            }
        }

        return $variations;
    }

    /**
     * Get variation list
     * @return array
     */
    protected function getVariations()
    {
        if ($this->_list instanceof JBCartVariantList) {
            return $this->_list->all();
        }

        return $this->getList()->all();
    }

    /**
     * Get default variant
     * @return JBCartVariant
     */
    protected function getDefaultVariant()
    {
        $default = $this->defaultKey();

        return $this->getVariant($default);
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
     * @param string $id Element id.
     * @return AppData
     */
    public function getParameter($id)
    {
        return new AppData($this->_storage->get('parameter', $this->key('private'), $id, array()));
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
     * @param string $access
     * @return array
     * @throws ElementJBPriceException
     */
    protected function getParameters($access = '')
    {
        if ($this->getTemplate() === null)
        {
            return array();
            //throw new ElementJBPriceException('Template is not set.');
        }
        $access = ($access === '' ? $this->key('private') : $access);

        return $this->_storage->get('parameters', $access, array());
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

            $this->filter_params = (array)$this->_position->loadParams($config);
        }

        return $this->filter_params;
    }

    /**
     * Get url to basket
     * @return string
     */
    protected function _getBasketUrl()
    {
        $menu = (int)$this->config->get('basket_menuitem');
        $url  = $this->app->jbrouter->basket($menu);

        return $url;
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


class ElementJBPriceException extends ElementException
{
}