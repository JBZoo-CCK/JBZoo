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
     * @type JBCartVariant
     */
    public $basic;

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
     * @var array of objects
     */
    protected $_params = array();

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
    protected $_template = null;

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

        // link to money helper
        $this->_position = $this->app->jbcartposition;
        $this->_config   = JBModelConfig::model();

        $this->_helper  = $this->app->jbprice;
        $this->_cache   = $this->app->jbcache;
        $this->_storage = $this->app->jbstorage;
        
        $this->app->jbassets->tools();
    }

    public function setType($type)
    {
        $this->_getConfig();

        parent::setType($type);
    }

    public function setItem($item)
    {
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
            if ($layout = $this->getLayout('variations.php')) {
                $this->loadEditAssets();

                $variations = $this->get('variations', array('1' => array()));
                $renderer   = $this->app->jbrenderer->create('jbprice');

                if (count($variations) === 1) {
                    $variations[count($variations)] = array();
                }

                $this->getVariantList($variations);

                return self::renderLayout($layout, array(
                    'variations' => $this->_list->all(),
                    'default'    => (int)$this->get('default_variant', self::BASIC_VARIANT),
                    'renderer'   => $renderer,
                    'mode'       => (int)$this->config->get('mode', 1)
                ));
            }

            return null;
        }

        $link = '<a target="_blank" href="'
            . $this->app->jbrouter->admin(array(
                'controller' => 'jbcart',
                'task'       => 'price',
                'element'    => $this->identifier
            ))
            . '">' . JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_ELEMENTS') . '</a>';

        return JText::sprintf('JBZOO_PRICE_EDIT_ERROR_NO_ELEMENTS', $link);
    }

    /**
     * Renders the element
     * @param array $params Render parameters
     * @return string|void
     */
    public function render($params = array())
    {
        $caching    = (int)$this->config->get('cache', 0);
        $this->hash = md5($this->identifier
            . $this->_item->id
            . serialize($params)
            . serialize((array)$this->data())
            . serialize($this->params)
            . serialize($this->_render_params)
        );

        if (!$caching || $caching && !$cache = $this->_cache->get($this->hash, 'price', true)) {
            $this->loadAssets();

            $params          = new AppData($params);
            $this->_template = $params->get('template', 'default');
            $this->_layout   = $params->get('_layout');

            $renderer = $this->app->jbrenderer->create('jbprice');
            $variant  = $this->getVariantList()->byDefault();

            $data = $renderer->render($this->_template, array(
                'price'    => $this,
                '_variant' => $variant,
                'variant'  => $variant->getId(),
                '_layout'  => $this->_layout
            ));

            //Must be after renderer
            $elements = $this->elementsInterfaceParams();
            if ($layout = $this->getLayout('render.php')) {
                return self::renderLayout($layout, array(
                    'hash'       => $this->hash,
                    'data'       => $data,
                    'elements'   => $elements,
                    'variantUrl' => $this->app->jbrouter->element($this->identifier, $this->_item->id, 'ajaxChangeVariant', array(
                        'template' => $this->_template
                    )),
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
        $this->app->jbassets->less('elements:jbprice/assets/less/submission.less');
        $this->hash = md5($params . $this->getItem()->alias);

        return $this->edit($params);
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
        if ((int)$this->config->get('cache', 0)) {
            $this->_cache->set($this->hash, $html, 'price', true);
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
            //if (empty($basic['_value']) || $basic['_value'] == 0) {
            //throw new AppValidatorException('This field is required');
            //}
        }

        return $value;
    }

    /**
     * Get default variant index
     * @return mixed
     */
    public function defaultKey()
    {
        $data = $this->data();
        if (isset($data['default_variant']) && JString::strlen($data['default_variant']) > 0) {
            return (int)$data['default_variant'];
        }

        return self::BASIC_VARIANT;
    }

    /**
     * @param $variant
     * @return array
     */
    public function allData($variant)
    {
        $variations = $this->get('variations', array());
        $elements   = array();
        if (!empty($variations) && isset($variant[$variant])) {
            $variant  = $variant[$variant];
            $elements = $this->mergeSysElements($variant);
        }

        return $elements;
    }

    /**
     * @param array $elements
     * @return array
     */
    public function mergeSysElements(array $elements)
    {
        if (empty($elements)) {
            return $elements;
        }

        $elements = array_merge((array)$this->_render_params, (array)$elements);

        return $elements;
    }

    /**
     * @param array $list
     * @param array $options
     * @return \JBCartVariantList
     */
    public function getVariantList($list = array(), $options = array())
    {
        if (!$this->_list instanceof JBCartVariantList) {
            if (empty($list)) {
                $list = $this->defaultList();
            }

            $variations = $this->build($list);
            $options    = array_merge(array(
                'element'    => $this,
                'element_id' => $this->identifier,
                'item_id'    => $this->_item->id,
                'item_name'  => $this->_item->name,
                'template'   => $this->_template,
                'layout'     => $this->_layout,
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay(),
                'values'     => $this->get('values.' . $this->defaultKey()),
                'currency'   => $this->currency(),
                'default'    => $this->defaultKey(),
                'cache'      => $this->config->get('cache')
            ), (array)$options);

            $this->_list = new JBCartVariantList($variations, $options);

            return $this->_list;
        }

        if (!empty($list)) {
            $variations = $this->build($list);
            $this->_list
                ->add($variations)
                ->setOptions($options);
        }

        return $this->_list;
    }

    public function build($list, $options = array())
    {
        $variations = array();
        if (!empty($list)) {
            if (!isset($list[0])) {
                $list[0] = $this->get('variations.' . 0);
            }
            $isOverlay = $this->isOverlay();
            $isCache   = $this->isCache();
            ksort($list);
            foreach ($list as $id => $elements) {
                $elements = $this->mergeSysElements($elements);
                $elements = $this->_getElements(array_keys($elements));

                $basic = null;
                if ($id != self::BASIC_VARIANT) {
                    $basic = $variations[self::BASIC_VARIANT];
                }
                $variations[$id] = $this->_storage->create('variant', array(
                    'elements' => $elements,
                    'options'  => array(
                        'id'         => $id,
                        'data'       => $list[$id],
                        'element_id' => $this->identifier,
                        'item_id'    => $this->_item->id,
                        'item_name'  => $this->_item->name,
                        'template'   => $this->_template,
                        'layout'     => $this->_layout,
                        'hash'       => $this->hash,
                        'isOverlay'  => $isOverlay,
                        'basic'      => $basic,
                        'cache'      => $isCache
                    )
                ));
            }
        }

        return $variations;
    }

    /**
     * @return array
     */
    public function defaultList()
    {
        $default = $this->defaultKey();
        $list    = $this->get('variations');

        return array(
            0        => isset($list[0]) ? $list[0] : array(),
            $default => isset($list[$default]) ? $list[$default] : array()
        );
    }

    /**
     * @param $key
     */
    public function setDefault($key)
    {
        $old = (int)$this->get('default_variant');

        if ($old != (int)$key) {
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
        $variant  = $this->getDefaultVariant();
        $elements = $variant->getElements();
        $params   = array();

        if (!empty($elements)) {
            foreach ($elements as $key => $element) {
                if ($element->isCore()) {
                    $params[$key] = $element->interfaceParams();
                }
            }
        }

        return $params;
    }

    /**
     * //TODO Hard function
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
        $result = $this->get('selected.' . $identifier, array());

        return $result;
    }

    /**
     * Prepare element data to push into JBHtmlHelper - select, radio etc.
     * @param $identifier
     * @return array
     */
    public function selectedOptions($identifier)
    {
        return $this->findOptions($identifier);
    }

    /**
     * @param $identifier
     * @return array|void
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
        return get_class($this) == 'ElementJBPriceCalc';
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
        $variations = $this->get('variations');
        $item_id    = $this->getItem()->id;

        $data = array();
        if (!empty($variations)) {
            $_config  = $this->_position->loadElements(JBCart::CONFIG_PRICE);
            $_tmpl    = $this->_position->loadElements(JBCart::CONFIG_PRICE_TMPL);
            $elements = array_merge($_config, $_tmpl);
            unset($_config, $_tmpl);

            $this->getVariantList($variations);

            $all = $this->_list->all();
            foreach ($all as $key => $variant) {
                $this->setDefault($key);
                $this->_list->_default = $key;

                $_elements = array_merge((array)$elements, (array)$variant->getElements());
                foreach ($_elements as $id => $element) {
                    if ($element->isSystemTmpl()) {
                        $element->setJBPrice($this);
                        $element->config->set('_variant', $key);
                    }

                    $value = $element->getSearchData();
                    if (!empty($value)) {
                        $d = $s = $n = null;
                        if ($value instanceof JBCartValue) {
                            $s = $value->data(true);
                            $n = $value->val();
                        } elseif (mb_strlen($value) !== 0) {
                            $s = $value;
                            $n = $this->isNumeric($value) ? $value : null;
                            $d = $this->isDate($value) ? $value : null;
                        }

                        $data[$key . $id] = array(
                            'item_id'    => $item_id,
                            'element_id' => $this->identifier,
                            'param_id'   => $id,
                            'value_s'    => $s,
                            'value_n'    => $n,
                            'value_d'    => $d,
                            'variant'    => $key
                        );

                        unset($value, $d, $s, $n);
                    }
                }
            }
            unset($elements, $variations, $this->_list, $all, $list);
        }

        $this->_position = $this->_config = $this->_element = null;

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
        if ($this->app->jbdate->isDate($date)) {
            return $this->app->jbdate->convertToStamp($date);
        }

        return false;
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
     * Get required elements
     * @return array
     */
    public function getRequired()
    {
        //$params   = $this->_getConfig(false);

        $required = (array)$this->get('required');

        $elements = $this->_getElements(array_flip($required));

        $required = array();
        if (!empty($params)) {
            foreach ($params as $id => $param) {
                if ((int)$param['required']) {
                    $required[$id] = $id;
                }
            }
        }

        return $required;
    }

    /**
     * @param  string    $identifier elementID
     * @param int|string $variant    variant key
     * @return JBCartElementPrice
     */
    public function getElement($identifier, $variant = self::BASIC_VARIANT)
    {
        //@jbdump::mark($this->getItem()->name . '::' . __FUNCTION__ . '::identifier::' . $identifier . '::start');
        // has element already been loaded?
        if (!$element = ($this->_storage->get('elements', $identifier) ? $this->_storage->get('elements', $identifier) : null)) {
            if ($config = $this->getElementConfig($identifier)) {
                $group = $config->get('group');
                $type  = $config->get('type');

                if ($element = $this->_storage->create('elements', array(
                    'app'        => $this->app,
                    'type'       => $type,
                    'group'      => $group,
                    'config'     => $config,
                    'class'      => 'JBCartElement' . $group . $type,
                    'identifier' => $identifier
                ))
                ) {

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $element = clone($element);
        $element->config->set('_variant', $variant);
        $element->setJBPrice($this);
        $element->setProperty('isOverlay', $this->isOverlay());

        //@jbdump::mark($this->getItem()->name . '::' . __FUNCTION__ . '::identifier::' . $identifier . '::end');
        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->_getElements(array_keys(array_diff_key($this->params, $this->_getRenderParams())));
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
            return $this->app->data->create($this->params[$identifier]);
        }

        if (isset($this->filter_params[$identifier])) {
            return $this->app->data->create($this->filter_params[$identifier]);
        }

        return $this->getElementRenderParams($identifier);
    }

    /**
     * Get render params for price param
     * @param $identifier
     * @return null
     */
    public function getElementRenderParams($identifier)
    {
        $core_config = $this->_getRenderParams();

        if (isset($core_config[$identifier])) {

            $param = $core_config[$identifier];
            if ($param['system'] || !$param['system'] && isset($this->params[$identifier])) {
                return $this->app->data->create($param);
            }
        }

        return null;
    }

    /**
     * @param $identifiers
     * @return array
     */
    public function _getElements($identifiers)
    {
        if ($identifiers) {
            $params = array();
            foreach ($identifiers as $identifier) {
                if ($param = $this->getElement($identifier)) {
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
        return $this->getVariant($key)->get('_balance');
    }

    /**
     * Bind and validate data
     * @param array $data
     */
    public function bindData($data = array())
    {
        $result = array();

        if (isset($data['variations'])) {
            $variations = $data['variations'];
            foreach ($variations as $i => $variant) {
                $hasSimple = false;
                foreach ($variant as $id => $value) {

                    $element = $this->getElement($id, $i);
                    $element->bindData($value);
                    $value = $element->getValue();

                    switch (gettype($value)) {
                        case 'array': {

                            foreach ($value as $j => $var) {
                                $var       = JString::trim($var);
                                $value[$j] = $var;

                                if (JString::strlen($var) === 0) {
                                    unset($variant[$id]);
                                }
                            }

                            if (!$element->isCore() && !empty($variant[$id])) {
                                $_data                           = (array)$element->bindData($value)->data();
                                $result['values'][$i][$id]       = $_data;
                                $result['selected'][$id][$value] = $value;
                            }
                            break;
                        }
                        default: {

                            if (JString::strlen($value) === 0) {
                                unset($variant[$id]);
                            }

                            if (!$element->isCore() && !empty($variant[$id])) {
                                $_data                           = (array)$element->bindData($value)->data();
                                $result['values'][$i][$id]       = $_data;
                                $result['selected'][$id][$value] = $value;
                            }
                            break;
                        }
                    }

                    if (!$element->isCore() && isset($variant[$id]) || $i === 0) {
                        $hasSimple = true;
                    }
                }

                if (!empty($variant) && $hasSimple) {
                    $result['variations'][$i] = $variant;
                }
            }
        }
        unset($data['variations']);

        if (!empty($data)) {
            foreach ($data as $id => $unknown) {
                $result[$id] = is_string($unknown) ? JString::trim($unknown) : $unknown;
            }
        }

        if (isset($result['variations'])) {
            $result['variations'] = array_values($result['variations']);
        }

        if (isset($result['values'])) {
            $keys             = range(1, count($result['values']));
            $result['values'] = array_combine($keys, array_values($result['values']));
        }

        parent::bindData($result);
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadEditAssets()
    {
        $this->app->jbassets->admin();
        $this->app->jbassets->less('elements:jbprice/assets/less/edit.less');

        $this->app->jbassets->js('elements:jbprice/assets/js/edit.js');
        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator.js');
        }

        return $this;
    }

    /**
     * @return int
     */
    public function isCache()
    {
        return (int)$this->config->get('cache', 0);
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        $js   = 'elements:jbprice/assets/js/jbprice.js';
        $less = 'elements:jbprice/assets/less/jbprice.less';

        $this->app->jbassets->js(array(
            $js, 'jbassets:js/price/toggle.js'
        ));
        $this->app->jbassets->less($less);

        if ($this->isCache()) {
            $key = strtolower(get_called_class());
            $this->_storage->set('assets', $js, md5($key . $js));
            $this->_storage->set('assets', $less, md5($key . $less));
        }

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

        return $this->getVariantList()->all();
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
                $id => $this->data()->find('variations.' . $id)
            );

        if (!$this->_list instanceof JBCartVariantList) {
            $this->getVariantList($array);
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
        if (is_null($this->params)) {
            $this->params = (array)$this->_config
                ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->identifier)
                ->get('list', array());
        }

        $this->_getRenderParams();
        $this->_getFilterParams();

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

            $this->_render_params = new AppData($this->_position->loadParams($config));
        }

        return $this->_render_params;
    }

    /**
     * Load elements render params for @filter
     * @return array
     */
    protected function _getFilterParams()
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
