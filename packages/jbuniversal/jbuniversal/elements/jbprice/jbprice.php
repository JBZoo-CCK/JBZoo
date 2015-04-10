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
     * @var Array of params config
     */
    public $params = null;

    /**
     * @var Array of core/unique price params config
     */
    public $_render_params = null;

    /**
     * @var Array of core/unique price params config
     */
    public $filter_params = null;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position = null;

    /**
     * @type JBCacheHelper
     */
    protected $_cache = null;

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
    public $_template = null;

    /**
     * Layout - full, teaser, submission etc.
     * @var null|string
     */
    protected $_layout = null;

    /**
     * //TODO это навреное не layout а template
     * Price template that chosen in layout
     * @var null
     */
    protected $_filter_template = null;

    const BASIC_VARIANT       = 0;
    const SIMPLE_PARAM_LENGTH = 36;

    /**
     * Constructor
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

        $this->app->jbassets->tools();
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        parent::setType($type);

        $this->_getConfig();

        if ($this->canAccess()) {
            $this->isCache = (bool)$this->config->get('cache', 0);
        }
        $this->showAll = (bool)!$this->config->get('only_selected', 0);
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->_getRenderParams();
        $this->_getConfig();

        parent::setItem($item);
    }

    /**
     * Check if elements value is set
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $params          = $this->app->data->create($params);
        $this->_template = $params->get('template', 'default');

        $data   = (array)$this->data();
        $config = $this->_getRenderParams();
        if (!empty($config) && !empty($data)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function edit($params = array())
    {
        $config = $this->_getConfig();
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

        return $this->renderWarning();
    }

    /**
     * Renders the element
     * @param array|AppData $params Render parameters
     * @return string|void
     */
    public function render($params = array())
    {
        $hash = $this->hash($params);
        $this->loadAssets();
        if (!$this->isCache || ($this->isCache && !$cache = $this->_cache->get($hash, 'price_elements', true))) {
            $params = new AppData($params);

            $this->_template = $params->get('template', 'default');
            $this->_layout   = $params->get('_layout');

            $renderer = $this->app->jbrenderer->create('jbprice');
            $variant  = $this->getList()->byDefault();

            $data = $renderer->render($this->_template, array(
                '_variant'   => $variant,
                'element_id' => $this->identifier,
                'variant'    => $variant->getId(),
                'layout'     => $this->_layout
            ));

            //Must be after renderer
            $elements = $this->elementsInterfaceParams($params);
            if (!$layout = $this->getLayout($params->get('layout', $this->_template) . '.php')) {
                $layout = $this->getLayout('render.php');
            }

            if ($layout) {
                return $this->renderLayout($layout, array(
                    'hash'       => $hash,
                    'data'       => $data,
                    'elements'   => $elements,
                    'variantUrl' => $this->app->jbrouter->element($this->identifier, $this->_item->id, 'ajaxChangeVariant', array(
                        'template' => $this->_template
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
        $key  = strtolower(get_called_class());
        $less = 'elements:jbprice/assets/less/submission.less';
        $this->app->jbassets->less($less);
        $this->_storage->set('assets', $less, md5($key . $less));

        return $this->edit((array)$params);
    }

    /**
     * @param string $layout
     * @param string $link
     * @param string $message
     * @return string
     */
    public function renderWarning($layout = '_warning.php', $link = '', $message = '')
    {
        $link = $this->app->jbrouter->admin(array(
            'controller' => 'jbcart',
            'task'       => 'price',
            'element'    => $this->identifier
        ));

        return parent::renderLayout($this->getLayout($layout), array(
            'link'    => $link,
            'message' => JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_ELEMENTS')
        ));
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

            $storage = $this->_storage->get('assets', $this->hash, true);

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
            $this->app->validator->create('textfilter', array('required' => $params->get('required')))
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
        return (int)$this->_item->elements->find("$this->identifier.default_variant", self::BASIC_VARIANT);
    }

    /**
     * @param array $variations
     * @param array $options
     * @return JBCartVariantList
     */
    public function getList($variations = array(), $options = array())
    {
        if (!$this->_list instanceof JBCartVariantList) {
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
                'template'   => $this->_template,
                'layout'     => $this->_layout,
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay,
                'cache'      => $this->isCache,
                'showAll'    => $this->showAll,
                'values'     => $this->get('values.' . $this->defaultKey(), array()),
                'currency'   => $this->currency(),
                'default'    => $this->defaultKey()
            ), (array)$options);

            $this->_list = new JBCartVariantList($list, $options);

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
        $elements = array_merge((array)$this->params, (array)$this->_render_params, (array)$data);
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
     * @param array $params
     * @return string
     */
    public function hash($params = array())
    {
        if ($this->hash !== null) {
            return $this->hash;
        }

        $this->hash = md5(serialize(array(
            $params,
            $this->identifier,
            $this->_item->id,
            (array)$this->_item->elements->get($this->identifier),
            (array)$this->_getConfig(),
            (array)$this->_getRenderParams(),
            (array)JFactory::getUser()->groups
        )));

        return $this->hash;
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
        return $this->_item->elements->find("{$this->identifier}.variations.{$key}", $default);
    }

    /**
     * @return array
     */
    public function defaultData()
    {
        return $this->_item->elements->find("{$this->identifier}.variations.0");
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
     * @param $key
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
    }

    /**
     * Get currency
     * @return string
     */
    public function currency()
    {
        $default = JBCart::val()->cur();
        $params  = $this->getElementRenderParams('_currency');

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
        $variant = $this->_list->byDefault();
        if ($variant->count()) {
            foreach ($variant->all() as $element) {
                if ($element->isCore()) {
                    $params        = new AppData(isset($this->_render_params[$element->identifier]) ? $this->_render_params[$element->identifier] : array());
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
        $result = array();
        if (empty($identifier)) {
            return $result;
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
     * Set protected property value
     * @param $key   - property name
     * @param $value - property value
     * @deprecated
     * @return ElementJBPrice
     */
    public function setTemplate($key, $value)
    {
        return $this->setProp($key, $value);
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
        $jbvars = $this->app->jbvars;
        $itemId = $this->getItem()->id;

        $variations = (array)$this->get('variations', array());
        $data       = array();

        if (!empty($variations)) {

            $list = $this->getList($variations);
            unset($variations);

            /** @type JBCartVariant $variant */
            foreach ($list->all() as $key => $variant) {

                $this->setDefault($key);
                foreach ($variant->all() as $paramId => $element) {
                    $value = $element->getSearchData();

                    $valDate = $valString = $valNum = null;
                    if ($value instanceof JBCartValue) {
                        $valString = $value->data(true);
                        $valNum    = $value->val();
                    } else {
                        $value     = JString::trim((string)$value);
                        $valString = $value;
                        $valNum    = $this->isNumeric($value) ? $jbvars->number($value) : null;
                        $valDate   = $this->isDate($value);
                    }

                    if (isset($valString{1}) || (is_int($valNum) || is_float($valNum)) || isset($valDate{1})) {
                        $key = $itemId . '__' . $variant->getId() . '__' . $paramId;

                        $data[$key] = array(
                            'item_id'    => $itemId,
                            'element_id' => $this->identifier,
                            'param_id'   => $paramId,
                            'value_s'    => $valString,
                            'value_n'    => $valNum,
                            'value_d'    => $valDate,
                            'variant'    => $variant->getId()
                        );
                    }
                }

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
        return "elements[{$this->identifier}][{$id}]" . ($array ? "[]" : "");
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
            $layout = "{$type}.php";
        }

        $parent = strtolower(str_replace('Element', '', get_parent_class($this)));
        $class  = $this->getElementType();

        $layoutPath = $this->app->path->path("elements:{$class}/tmpl/{$layout}");
        if (empty($layoutPath)) {
            $layoutPath = $this->app->path->path("elements:{$parent}/tmpl/{$layout}");
        }

        return $layoutPath;
    }

    /**
     * @param  string    $identifier elementID
     * @param int|string $variant    variant key
     * @return JBCartElementPrice
     */
    public function getElement($identifier, $variant = self::BASIC_VARIANT)
    {
        // has element already been loaded?
        if (!$element = ($this->_storage->getElement($identifier) ? $this->_storage->getElement($identifier) : null)) {
            if ($config = $this->getElementConfig($identifier)) {
                $group = $config->get('group');
                $type  = $config->get('type');

                if ($element = $this->_storage->create('element', array(
                    'app'        => $this->app,
                    'type'       => $type,
                    'group'      => $group,
                    'identifier' => $identifier,
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

        $element = clone($element);
        $element = $this->_storage->configure($element, array(
            'variant'    => $variant,
            'hash'       => $this->hash,
            'item_id'    => $this->_item ? $this->_item->id : 0,
            'element_id' => $this->identifier,
            'identifier' => $identifier,
            'cache'      => $this->isCache,
            'isOverlay'  => $this->isOverlay,
            'showAll'    => $this->showAll,
            'template'   => $this->_template,
            'layout'     => $this->_layout
        ));
        $element->setJBPrice($this);

        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->_getElements(array_keys(array_diff_key((array)$this->_getConfig(), (array)$this->_getRenderParams())));
    }

    /**
     * @return array
     */
    public function getRenderElements()
    {
        return $this->_getElements(array_keys($this->_getRenderParams()));
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
        return array_filter($this->_getRenderParams(),
            create_function('$element', 'return $element[\'system\'] == 1;'));
    }

    /**
     * @param $identifier
     * @return null
     */
    public function getElementConfig($identifier)
    {
        if (isset($this->params[$identifier])) {
            return new AppData($this->params[$identifier]);
        }

        if (isset($this->filter_params[$identifier])) {
            return new AppData($this->filter_params[$identifier]);
        }

        return $this->getElementRenderParams($identifier);
    }

    /**
     * Get render params for price param
     * @param  string $identifier
     * @return array
     */
    public function getElementRenderParams($identifier)
    {
        $core_config = $this->_getRenderParams();
        if (isset($core_config[$identifier])) {
            $param = $core_config[$identifier];
            if ($param['system'] || (!$param['system'] && isset($this->params[$identifier]))) {
                return new AppData($param);
            }
        }

        return null;
    }

    /**
     * @param     $identifiers
     * @param int $key
     * @return array
     */
    public function _getElements($identifiers, $key = self::BASIC_VARIANT)
    {
        if ($identifiers) {

            $params = array();
            foreach ($identifiers as $identifier) {
                if ($param = $this->getElement($identifier, $key)) {
                    $params[$identifier] = $param;
                }
            }

            return $params;
        }

        return array();
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
        if (null !== $this->_item) {
            $simple = $variant->simple();

            $values     = (array)$this->_item->elements->find($this->identifier . '.values', array());
            $selected   = (array)$this->_item->elements->find($this->identifier . '.selected', array());
            $variations = (array)$this->_item->elements->find($this->identifier . '.variations', array());

            $variations[$variant->getId()] = $variant->data();
            if (!$variant->isBasic()) {
                $values[$variant->getId()] = array_filter(array_map(create_function('$element',
                    'return JString::strlen($element->getValue(true)) > 0 ? (array)$element->data() : null;'), $simple
                ));

                $_selected = array_filter(array_map(create_function('$element', 'return JString::strlen($element->getValue(true)) > 0
                    ? array(JString::trim($element->getValue(true)) => $element->getValue(true)) : null;'), $simple)
                );

                if ($_selected) {
                    foreach ($_selected as $key => $value) {
                        $selected[$key] = array_merge(isset($selected[$key]) ? $selected[$key] : array(), (array)$value);
                    }
                }
            }
            $variations = array_values($variations);
            $values     = array_values($values);

            $this->_item->elements->set($this->identifier, array(
                'variations' => $variations,
                'values'     => $variant->isBasic() ? array(self::BASIC_VARIANT => array()) : $values,
                'selected'   => $selected
            ));
        }

        return $this;
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadEditAssets()
    {
        $this->app->jbassets->admin();
        $this->app->jbassets->less('elements:jbprice/assets/less/edit.less');

        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator.js');
        }

        $this->app->jbassets->js('elements:jbprice/assets/js/edit.js');

        return $this;
    }

    /**
     * @param        $path
     * @return bool
     */
    public function toStorage($path)
    {
        return $this->_storage->add('assets', $path, $this->hash);
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        $js   = 'elements:jbprice/assets/js/jbprice.js';
        $less = 'elements:jbprice/assets/less/jbprice.less';
        if ($this->isCache) {
            if ($assets = $this->_cache->get($this->hash, 'price_assets', true)) {
                if (count($assets)) {
                    foreach ($assets as $asset) {
                        if ($ext = JFile::getExt($asset)) {
                            $this->app->jbassets->$ext($asset);
                        }
                    }
                }

                return true;
            }
        }
        $this->toStorage($js);
        $this->toStorage($less);

        $this->app->jbassets->less($less);
        $this->app->jbassets->js($js);

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
     * Load all params
     * @return array
     */
    protected function _getConfig()
    {
        if (null === $this->params) {
            $this->params = (array)$this->_config
                ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->identifier)
                ->get('list', array());
        }

        return $this->params;
    }

    /**
     * Load elements render params for item
     * @return array
     */
    public function _getRenderParams()
    {
        if (!$this->_template) {
            return array();
        }

        if (!isset($this->_render_params)) {
            $config = JBCart::CONFIG_PRICE_TMPL . '.' . $this->identifier . '.' . $this->_template;

            $this->_render_params = (array)$this->_position->loadParams($config);
        }

        return $this->_render_params;
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

        if (!isset($this->filter_params)) {
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
        $url = null;

        $menu = (int)$this->config->get('basket_menuitem');
        $url  = $this->app->jbrouter->basket($menu);

        return $url;
    }
}
