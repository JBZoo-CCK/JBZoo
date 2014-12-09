<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
     * @var JBMoneyHelper
     */
    protected $_money = null;

    /**
     * @var JBCartElementHelper
     */
    protected $_element = null;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position = null;

    /**
     * @var JBCartVariantList
     */
    protected $_list;

    /**
     * @var JBImageHelper
     */
    protected $_image = null;

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

    const BASIC_VARIANT = '0';

    const DEFAULT_CURRENCY = 'eur';

    const PARAM_IMAGE_IDENTIFIER       = '_image';
    const PARAM_DESCRIPTION_IDENTIFIER = '_description';
    const PARAM_WEIGHT_ID              = '_weight';
    const PARAM_SKU_ID                 = '_sku';
    const PARAM_VALUE_ID               = '_value';

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
        $this->_money    = $this->app->jbmoney;
        $this->_position = $this->app->jbcartposition;
        $this->_image    = $this->app->jbimage;
        $this->_config   = JBModelConfig::model();

        $this->_element = $this->app->jbcartelement;
    }

    /**
     * Check if elements value is set
     *
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $this->_template = $params->get('template', 'default');

        $config = $this->_getRenderParams();

        if (!empty($config)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $submission
     *
     * @return null|string
     */
    public function edit($submission = array())
    {
        $config = $this->_getConfig(false);
        if (!empty($config)) {
            if ($layout = $this->getLayout('variations.php')) {
                $this->loadEditAssets();

                $variations = $this->get('variations', array('1' => array()));

                $this->_list = new JBCartVariantList($variations, $this);
                $renderer    = $this->app->jbrenderer->create('jbprice');

                return self::renderLayout($layout, array(
                    'variations' => $this->_list->all(),
                    'submission' => $submission,
                    'default'    => (int)$this->get('default_variant', self::BASIC_VARIANT),
                    'renderer'   => $renderer
                ));
            }

            return null;
        }

        $link = '<a target="_blank" href="'
            . $this->app->jbrouter->admin(array(
                'controller' => 'jbcart',
                'task'       => 'price',
                'layout'     => $this->identifier
            ))
            . '">' . JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_ELEMENTS') . '</a>';

        return JText::sprintf('JBZOO_PRICE_EDIT_ERROR_NO_ELEMENTS', $link);
    }

    /**
     * Renders the element
     *
     * @param array $params Render parameters
     *
     * @return string|void
     */
    public function render($params = array())
    {
        $this->loadAssets();
        $params = $this->app->data->create($params);

        $this->_template = $params->get('template', 'default');
        $this->_layout   = $params->get('_layout');

        $item = $this->getItem();

        $variant  = $this->getDefaultVariant();
        $renderer = $this->app->jbrenderer->create('jbprice');

        $data = $renderer->render($this->_template, array(
            'price'    => $this,
            '_variant' => $variant,
            'variant'  => $variant->id(),
            '_layout'  => $this->_layout
        ));

        //Must be after renderer
        $elements = $this->elementsInterfaceParams();
        if ($layout = $this->getLayout('render.php')) {
            return self::renderLayout($layout, array(
                'data'       => $data,
                'elements'   => $elements,
                'variantUrl' => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxChangeVariant', array(
                    'template' => $this->_template
                )),
            ));
        }

        return null;
    }

    /**
     * Render submission
     *
     * @param array $params
     *
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit();
    }

    /**
     * Validate submission
     *
     * @param $value
     * @param $params
     *
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
     *
     * @return mixed
     */
    public function defaultVariantKey()
    {
        $default = (int)$this->get('default_variant', self::BASIC_VARIANT);

        return $default;
    }

    /**
     * @return JBCartVariantList
     */
    public function getVariantList()
    {
        return $this->_list;
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
     * @param string $currency
     */
    abstract public function ajaxChangeVariant($template = 'default', $values = array(), $currency = '');

    /**
     * Ajax add to cart method
     *
     * @param string $template
     * @param int    $quantity
     * @param array  $values
     * @param bool   $sendAjax
     */
    abstract public function ajaxAddToCart($template = 'default', $quantity = 1, $values = array(), $sendAjax = true);

    /**
     * Remove from cart method
     * @param string $key Session key
     * @return mixed
     */
    abstract public function ajaxRemoveFromCart($key);

    /**
     * Get interface params for all core elements that used in widgets.
     *
     * @return array
     */
    public function elementsInterfaceParams()
    {
        $elements = $this->_getRenderParams();
        $params   = array();

        if (!empty($elements)) {
            $variant = $this->defaultVariantKey();
            foreach ($elements as $key => $element) {
                if ($element = $this->getElement($key, $variant)) {
                    if ($element->isCore()) {
                        $params[$key] = $element->interfaceParams();
                    }
                }
            }
        }

        return $params;
    }

    /**
     * //TODO Hard function
     * Get all options for element.
     * Used in element like select, color, radio etc.
     *
     * @param $identifier
     *
     * @return array
     */
    public function findOptions($identifier)
    {
        $result = array();
        if (empty($identifier)) {
            return $result;
        }

        $variations = $this->get('variations', array());
        if (!empty($variations)) {
            foreach ($variations as $key => $variant) {
                if (isset($variant[$identifier])) {

                    $element = $this->getElement($identifier);
                    $value   = $variant[$identifier];

                    $element->bindData($value);

                    $value = $element->getValue();
                    if (JString::strlen($value) !== 0) {
                        $result[$key] = array(
                            'value' => $value,
                            'name'  => $value
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Prepare element data to push into JBHtmlHelper - select, radio etc.
     *
     * @param $identifier
     *
     * @return array
     */
    public function selectedOptions($identifier)
    {
        $options = self::findOptions($identifier);
        if (empty($options)) {
            return $options;
        }

        $result = array();
        foreach ($options as $key => $value) {
            $result[$value['value']] = $value['name'];
        }

        return $result;
    }

    /**
     * Get element template in layout.
     *
     * @return string|null
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Get element layout.
     *
     * @return null|string
     */
    public function layout()
    {
        return $this->_layout;
    }

    /**
     * Set protected property value
     * @param $key - property name
     * @param $value - property value
     */
    public function setTemplate($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Check if calc element
     *
     * @return bool
     */
    public function isOverlay()
    {
        return get_class($this) == 'ElementJBPriceCalc';
    }

    /**
     * Get element data in JSONData Object
     *
     * @return JSONData
     */
    public function data()
    {
        $data = array();

        if (isset($this->_item)) {
            $data = $this->_item->elements->get($this->identifier);
        }

        return $this->app->data->create($data);
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
            foreach ($variations as $key => $variant) {

                $this->set('default_variant', $key);
                $this->_list = new JBCartVariantList(array($key => $variant), $this);

                foreach ($this->_list->byDefault()->getElements() as $id => $element) {
                    $value = JString::trim($element->getSearchData());

                    if (JSTring::strlen($value) !== 0) {

                        $n = $this->isNumeric($value) ? $value : null;
                        $d = $this->toDate($value) ? $value : null;
                        $s = $value;

                        $data[$key . '_' . $id] = array(
                            'item_id'    => $item_id,
                            'element_id' => $this->identifier,
                            'param_id'   => $element->identifier,
                            'variant'    => $key,
                            'value_s'    => $s,
                            'value_n'    => $n,
                            'value_d'    => $d
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
        return $this->app->jbpriceparams->isNumeric($number);
    }

    /**
     * Check if value seems as date
     *
     * @param $date
     * @return null|string
     */
    public function toDate($date)
    {
        return $this->app->jbdate->convertToStamp($date);
    }

    /**
     * Get control name
     *
     * @param string $id
     * @param bool   $array
     *
     * @return string
     */
    public function getControlName($id, $array = false)
    {
        return "elements[{$this->identifier}][{$id}]" . ($array ? "[]" : "");
    }

    /**
     * Get element layout path and use override if exists
     *
     * @param null $layout
     *
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
     * @param  string $identifier elementID
     * @param string  $variant variant key
     *
     * @return bool|JBCartElement|null
     */
    public function getElement($identifier, $variant = self::BASIC_VARIANT)
    {
        // has element already been loaded?
        if (!$element = isset($this->_params[$identifier]) ? $this->_params[$identifier] : null) {

            if ($config = $this->getElementConfig($identifier)) {

                if ($element = $this->_element->create($config->get('type'), $config->get('group'), $config)) {

                    $element->identifier = $identifier;

                    $this->_params[$identifier] = $element;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $element = clone($element);
        $config  = $this->getElementConfig($identifier);

        $config->set('_variant', $variant);
        $element->setConfig($config);
        $element->setJBPrice($this);

        return $element;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        if (is_null($this->params)) {
            $this->_getConfig();
        }

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
     *
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
     *
     * @return null
     */
    public function getElementConfig($identifier)
    {
        if (is_null($this->params)) {
            $this->_getConfig();
        }

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
     *
     * @param $identifier
     *
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
     *
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
     *
     * @param $quantity
     *
     * @return bool
     */
    public function inStock($quantity)
    {
        $items = JBCart::getInstance()->getItems(false);

        $quantity += (float)$items->find($this->_list->getSessionKey() . '.quantity');
        if ($this->_list->byDefault()->inStock($quantity)) {
            return true;
        }

        return false;
    }

    /**
     * Bind and validate data
     *
     * @param array $data
     */
    public function bindData($data = array())
    {
        $result = array();

        if (isset($data['variations'])) {
            $variations = $data['variations'];

            for ($i = 0; $i < count($variations); $i++) {

                foreach ($variations[$i] as $key => $variant) {

                    $param = $this->getElement($key);

                    if (!$param->isCore()) {

                        $result['values'][$i][$key] = $variant;
                        if (is_array($variant)) {
                            foreach ($variant as $j => $var) {

                                $var = JString::trim($var);

                                $variant[$j] = $var;

                                if (empty($var) && JString::strlen($var) === 0) {
                                    unset($result['variations'][$i][$key]);
                                    unset($result['values'][$i][$key]);
                                }

                            }
                        }
                    }

                    $result['variations'][$i][$key] = $variant;
                }
            }
        }
        unset($data['variations']);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $result[$key] = JString::trim($value);
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
        $this->app->jbassets->js('elements:jbprice/assets/js/edit.js');
        if ((int)$this->config->get('mode', 1)) {
            $this->app->jbassets->js('jbassets:js/admin/validator.js');
        }

        return $this;
    }

    /**
     * Load assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->js(array(
            'elements:jbprice/assets/js/jbprice.js',
            'jbassets:js/price/toggle.js'
        ));
        $this->app->jbassets->less('elements:jbprice/assets/less/jbprice.less');

        return parent::loadAssets();
    }

    /**
     * Get variation list
     *
     * @return array
     */
    protected function getVariations()
    {
        if ($this->_list instanceof JBCartVariantList) {
            return $this->_list->all();
        }

        $default = $this->defaultVariantKey();
        $basic   = self::BASIC_VARIANT;
        $values  = $this->get('values.' . $default);

        $variations = array(
            $basic   => $this->get('variations.' . $basic),
            $default => $this->get('variations.' . $default)
        );

        $this->_list = new JBCartVariantList($variations, $this, array(
            'values'   => $values,
            'template' => $this->_template,
            'currency' => $this->currency()
        ));

        return $this->_list->all();
    }

    /**
     * Get default variant
     *
     * @return JBCartVariant
     */
    protected function getDefaultVariant()
    {
        $default = $this->defaultVariantKey();

        return $this->getVariant($default);
    }

    /**
     * @param string $id
     *
     * @return JBCartVariant
     */
    protected function getVariant($id = self::BASIC_VARIANT)
    {
        if ($this->_list instanceof JBCartVariantList) {
            return $this->_list->get($id);
        }

        $default = $id = (int)$id;
        $basic   = self::BASIC_VARIANT;
        $values  = $this->get('values.' . $id);

        $variations = array(
            $basic   => $this->get('variations.' . $basic),
            $default => $this->get('variations.' . $id)
        );

        $this->_list = new JBCartVariantList($variations, $this, array(
            'values'   => $values,
            'template' => $this->_template,
            'currency' => $this->currency()
        ));

        return $this->_list->get($id);
    }

    /**
     * Load all params
     *
     * @param bool $core
     *
     * @return array
     */
    protected function _getConfig($core = true)
    {
        if (is_null($this->params)) {
            $this->params = $this->_config
                ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->identifier)
                ->get('list', array());
        }

        if ($core === true) {
            $this->_getRenderParams();
            $this->_getFilterParams();
        }

        return $this->params;
    }

    /**
     * Load elements render params for item
     *
     * @return array
     */
    protected function _getRenderParams()
    {
        if (!$this->_template) {
            return array();
        }

        if (is_null($this->_render_params)) {

            $config = JBCart::CONFIG_PRICE_TMPL . '.' . $this->identifier . '.' . $this->_template;

            $this->_render_params = $this->_position->loadParams($config);
        }

        return $this->_render_params;
    }

    /**
     * Load elements render params for @filter
     *
     * @return array
     */
    protected function _getFilterParams()
    {
        if (!$this->_filter_template) {
            return array();
        }

        if (!$this->filter_params) {

            $config = JBCart::CONFIG_PRICE_TMPL_FILTER . '.' . $this->identifier . '.' . $this->_filter_template;

            $this->filter_params = $this->_position->loadParams($config);
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
