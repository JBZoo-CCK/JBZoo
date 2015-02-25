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
        $this->loadAssets();
    }

    /**
     * @param Item $item
     */
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
            if ($layout = $this->getLayout('edit.php')) {
                $this->loadEditAssets();

                $variations = $this->get('variations', array(0 => array()));
                $renderer   = $this->app->jbrenderer->create('jbprice');

                if (count($variations) === 1) {
                    $variations[count($variations)] = array();
                }

                $this->getList($variations);

                return parent::renderLayout($layout, array(
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
        $this->hash = md5($this->identifier
            . $this->_item->id
            . serialize($params)
            . serialize((array)$this->_item->elements->get($this->identifier))
            . serialize($this->params)
            . serialize($this->_render_params)
        );

        if (!$this->isCache() || $this->isCache() && !$cache = $this->_cache->get($this->hash, 'price', true)) {
            $this->loadAssets();
            $params          = new AppData($params);
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
            $elements = $this->elementsInterfaceParams();
            if ($layout = $this->getLayout('render.php')) {
                return $this->renderLayout($layout, array(
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
        $key  = strtolower(get_called_class());
        $less = 'elements:jbprice/assets/less/submission.less';
        $this->app->jbassets->less($less);
        $this->_storage->set('assets', $less, md5($key . $less));

        return $this->edit((array)$params);
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
        if ($this->isCache()) {
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
        return (int)$this->_item->elements->find("$this->identifier.default_variant", self::BASIC_VARIANT);
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
    public function mergeSysElements($elements = array())
    {
        $elements = array_merge((array)$this->params, (array)$this->_render_params, (array)$elements);

        return $elements;
    }

    /**
     * @param array $variations
     * @param array $options
     * @return \JBCartVariantList
     */
    public function getList($variations = array(), $options = array())
    {
        if (!$this->_list instanceof JBCartVariantList) {
            if (empty($variations)) {
                $variations = $this->defaultList();
            }

            if (!isset($variations[0])) {
                $variations[0] = $this->getData(0);
            }

            $list = $this->build($variations);
            unset($variations);
            $options = array_merge(array(
                'element'    => $this,
                'element_id' => $this->identifier,
                'item_id'    => $this->_item->id,
                'item_name'  => $this->_item->name,
                'template'   => $this->_template,
                'layout'     => $this->_layout,
                'hash'       => $this->hash,
                'isOverlay'  => $this->isOverlay(),
                'cache'      => $this->isCache(),
                'selected'   => $this->config->get('only_selected', self::BASIC_VARIANT),
                'currency'   => $this->currency(),
                'default'    => $this->defaultKey()
            ), (array)$options);

            $this->_list = new JBCartVariantList($list, $options);

            return $this->_list;
        }
        if (!empty($variations)) {
            $variations = $this->build($variations);
            $this->_list
                ->add($variations);

            if (!empty($options)) {
                $this->_list->setOptions((array)$options);
            }
        }

        return $this->_list;
    }

    /**
     * @param array $variations
     * @param array $options
     * @return array
     */
    public function build(array $variations, $options = array())
    {
        if (empty($variations)) {
            return $variations;
        }
        $list = array();

        ksort($variations);
        foreach ($variations as $id => $data) {
            $elements = array_merge((array)$this->params, (array)$this->_render_params, (array)$data);
            $elements = $this->_getElements(array_keys($elements));

            $list[$id] = $this->_storage->create('variant', array(
                'elements' => $elements,
                'options'  => array(
                    'id'       => $id,
                    'elements' => $data
                )
            ));
        }


        return $list;
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return string
     */
    public function getOption($key, $default = null)
    {
        $options = array(
            'element_id' => $this->identifier,
            'item_id'    => $this->_item->id,
            'item_name'  => $this->_item->name,
            'template'   => $this->_template,
            'layout'     => $this->_layout,
            'hash'       => $this->hash,
            'isOverlay'  => $this->isOverlay(),
            'cache'      => $this->isCache(),
            'selected'   => $this->config->get('only_selected', self::BASIC_VARIANT)
        );

        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * @param      $key
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
        $options  = array();

        if (!empty($elements)) {
            foreach ($elements as $key => $element) {
                if ($element->isCore()) {
                    if (isset($this->_render_params[$key])) {
                        $params        = new AppData($this->_render_params[$key]);
                        $options[$key] = $element->interfaceParams($params);
                    }
                }
            }
        }


        return $options;
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
        $list = $this->get('variations');
        $data = array();
        if (!empty($list)) {
            $variations = $this->build($list);
            /*$_config  = $this->_position->loadElements(JBCart::CONFIG_PRICE);
            $_tmpl    = $this->_position->loadElements(JBCart::CONFIG_PRICE_TMPL);
            $elements = array_merge($_config, $_tmpl);
            unset($_config, $_tmpl);
            */

            /**
             * @type string        $key
             * @type JBCartVariant $variant
             */
            foreach ($variations as $key => $variant) {
                foreach ($variant->getElements() as $id => $element) {
                    $value = $element->getSearchData();

                    if (!empty($value)) {
                        $d = $s = $n = null;
                        if ($value instanceof JBCartValue) {
                            $s = $value->data(true);
                            $n = $value->val();

                        } elseif (isset($value{0})) {
                            $s = $value;
                            $n = $this->isNumeric($value) ? $value : null;
                            $d = $this->isDate($value) ? $value : null;
                        }

                        if (isset($s) || isset($n) || isset($d)) {
                            $data[$key . $id] = array(
                                'item_id'    => $this->_item->id,
                                'element_id' => $this->identifier,
                                'param_id'   => $id,
                                'value_s'    => $s,
                                'value_n'    => $n,
                                'value_d'    => $d,
                                'variant'    => $key
                            );
                        }
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

        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->_getElements(array_keys(array_diff_key((array)$this->params, (array)$this->_getRenderParams())));
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
            return new AppData($this->params[$identifier]);
        }

        if (isset($this->filter_params[$identifier])) {
            return new AppData($this->filter_params[$identifier]);
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
                return new AppData($param);
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
            $list = $this->build($data['variations']);
            unset($data['variations']);
            /**
             * @type string        $key
             * @type JBCartVariant $variant
             */
            foreach ($list as $key => $variant) {

                $simple = 0;
                /** @type JBCartElementPrice $element */
                foreach ($variant->getElements() as $id => $element) {
                    $value = $element->getValue();

                    if ($value instanceof JBCartValue) {
                        $value = $value->data(true);
                    }

                    if (JString::strlen($value) > 0 && $element->count()) {
                        $_data = (array)$element->data();

                        $result['variations'][$key][$id] = $_data;
                        if (!$element->isCore()) {
                            $simple++;
                            $result['selected'][$id][$value] = $value;
                        }
                    }
                }
                if($simple === 0) {
                    unset($result['variations'][$key]);
                }
            }
            if (isset($result['variations'])) {
                $result['variations'] = array_values($result['variations']);
            }
        }
        if (!empty($data)) {
            foreach ($data as $id => $unknown) {
                $result[$id] = is_string($unknown) ? JString::trim($unknown) : $unknown;
            }
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
        if ($this->canAccess()) {
            return (int)$this->config->get('cache', 0);
        }

        return false;
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
                $id => $this->data()->find('variations.' . $id)
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
        if (is_null($this->params)) {
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
