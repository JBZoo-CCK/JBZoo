<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class ElementJBPriceAdvance
 * The Price element for JBZoo
 */
class ElementJBPriceAdvance extends Element implements iSubmittable
{
    const TYPE_PRIMARY   = 1;
    const TYPE_SECONDARY = 2;

    const BALANCE_VIEW_NO     = 0;
    const BALANCE_VIEW_SIMPLE = 1;
    const BALANCE_VIEW_FULL   = 2;

    const PRICE_VIEW_FULL     = 1;
    const PRICE_VIEW_PRICE    = 2;
    const PRICE_VIEW_TOTAL    = 3;
    const PRICE_VIEW_DISCOUNT = 4;
    const PRICE_VIEW_SAVE     = 5;

    const SALE_VIEW_NO          = 0;
    const SALE_VIEW_TEXT        = 1;
    const SALE_VIEW_TEXT_SIMPLE = 2;
    const SALE_VIEW_ICON_SIMPLE = 3;
    const SALE_VIEW_ICON_VALUE  = 4;

    const TEXT_FIELD_NONE     = 0;
    const TEXT_FIELD_SIMPLE   = 1;
    const TEXT_FIELD_AS_PARAM = 2;

    const PARAMS_TMPL_NONE   = 0;
    const PARAMS_TMPL_SELECT = 1;
    const PARAMS_TMPL_RADIO  = 2;

    const BASIC_GROUP   = 'basic';
    const VARIANT_GROUP = 'variations';

    const PRICE_MODE_DEFAULT = 1;
    const PRICE_MODE_OVERLAY = 2;

    const SIMPLE_PARAM_LENGTH = 36;

    const DEFAULT_CURRENCY = 'EUR';

    const PARAM_IMAGE_IDENTIFIER       = '_image';
    const PARAM_DESCRIPTION_IDENTIFIER = '_description';
    const PARAM_WEIGHT_ID              = '_weight';
    const PARAM_SKU_ID                 = '_sku';
    const PARAM_VALUE_ID               = '_value';

    /**
     * @var Array of params config
     */
    public $params = null;

    /**
     * @var Array of core/unique price params config
     */
    public $core_params = null;

    /**
     * @var Array of core/unique price params config
     */
    public $filter_params = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

    /**
     * @var JBCartElementHelper
     */
    protected $_jbcartelement = null;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position = null;

    /**
     * @var JBImageHelper
     */
    protected $_image = null;

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var null
     */
    protected $_layout = null;

    /**
     * @var null
     */
    protected $_filter_layout = null;

    /**
     * @var array of objects
     */
    protected $_params = array();

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
        $this->_jbmoney  = $this->app->jbmoney;
        $this->_position = $this->app->jbcartposition;
        $this->_image    = $this->app->jbimage;
        $this->_config   = JBModelConfig::model();

        $this->_jbcartelement = $this->app->jbcartelement;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $params = $this->app->data->create($params);

        if (!(int)$params->get('show_empty_price', 1)) {

            $basic = $this->getBasicData();

            if (empty($basic['_value']) || $basic['_value'] == 0) {
                return false;
            }
        }

        if (!(int)$params->get('show_empty_balance', 1)) {
            $basic = $this->getBasicData();

            if ((int)$basic['params']['_balance']['value'] == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return null
     */
    public function getSearchData()
    {
        $data = $this->getBasicData();
        if (!empty($data['params'])) {
            return isset($data['params']['_sku']['value']) ? $data['params']['_sku']['value'] : $this->getItem()->id;
        }

        return null;
    }

    /**
     * @param array $submission
     *
     * @return null|string
     */
    public function edit($submission = array())
    {
        if ($layout = $this->getLayout('edit.php')) {

            $this->app->jbassets->admin();
            $variationsHTML = null;

            $basicData = $this->getBasicReadableData();

            $variations = $this->getVariations();
            if (empty($variations) && (int)$this->config->get('mode', 0)) {
                $basic      = $this->getBasicData();
                $variations = array($basic);
            }

            $renderer = $this->app->jbrenderer->create('jbprice');

            $params = array(
                'config'       => $this->config,
                'currencyList' => $this->getCurrencyList(),
                'variations'   => $variations,
                'submission'   => $submission,
                'basicData'    => $basicData,
                'variant'      => $basicData->get('default_variant'),
                'renderer'     => $renderer
            );

            $basic = $renderer->render('_edit',
                array(
                    'price' => $this,
                    'style' => self::BASIC_GROUP
                )
            );

            if ($variantTPL = $this->getLayout('_variations.php')) {
                $variationsHTML = self::renderLayout($variantTPL, $params);
            }

            return self::renderLayout($layout, array_merge($params,
                array(
                    'variantsHTML' => $variationsHTML,
                    'basicHTML'    => $basic
                )
            ));
        }

        return null;
    }

    /**
     * Get all options for element.
     * Used in element like select, color, radio etc.
     *
     * @param $identifier
     *
     * @return array
     */
    public function getAllParamData($identifier)
    {
        $result     = array();
        $variations = $this->getVariations();

        if (!empty($variations)) {
            foreach ($variations as $variant) {

                $variant = $this->getReadableData($variant);
                $value   = $variant->get($identifier);
                if (!empty($value)) {
                    $result[] = $value;
                }

            }
        }

        return $result;
    }

    /**
     * Load static assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->initJBpriceAdvance();

        return parent::loadAssets();
    }

    /**
     * Get control name
     *
     * @param string $id
     * @param string $name
     * @param bool $array
     *
     * @return string
     */
    public function getControlName($id, $name = null, $array = false)
    {
        return "elements[{$this->identifier}][basic][{$id}]" . ($name ? "[{$name}]" : "") . ($array ? "[]" : "");
    }

    /**
     * @param string $id
     * @param string $name
     *
     * @return string
     */
    public function getControlParamName($id, $name = null)
    {
        return "elements[{$this->identifier}][basic][params][{$id}][{$name}]";
    }

    /**
     * Get name for prices variants
     *
     * @param string $id
     * @param string $name
     * @param int $index
     *
     * @return string
     */
    public function getRowControlName($id, $name = null, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][{$id}]" . ($name ? "[{$name}]" : "");
    }

    /**
     * @param string $id
     * @param string $name
     * @param int $key
     * @param int $index
     *
     * @return string
     */
    public function getParamName($id, $name = null, $key = 0, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][params][{$id}]" . ($name ? "[{$name}]" : "");
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
        return $this->edit($params);
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
     * Render for front-end
     *
     * @param array $params
     *
     * @return string|void
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);

        $this->_layout = $params->get('template', 'default');
        //$this->_position = $params->get('_position');
        $this->_index = $params->get('_index');

        $this->_renderLayout = $params->get('_layout');

        $cart = JBCart::getInstance();
        $item = $this->getItem();

        $renderer = $this->app->jbrenderer->create('jbprice');
        $elements = $renderer->render($this->_layout, array(
            'price'    => $this,
            '_layout'  => $params->get('_layout'),
            '_variant' => $this->defaultVariantKey()
        ));

        if ($layout = $this->getLayout('render.php')) {
            return self::renderLayout($layout, array(
                'elements'          => $elements,
                'prices'            => $this->getBasicPrices(),
                'default_variant'   => $this->getDefaultVariantPrices(),
                'isInCart'          => (int)$cart->inCart($item->id),
                'basketUrl'         => $this->_getBasketUrl(),
                'addToCartUrl'      => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxAddToCart'),
                'removeFromCartUrl' => $this->app->jbrouter->element($this->identifier, $item->id,
                        'ajaxRemoveFromCart'),
                'changeVariantUrl'  => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxChangeVariant',
                        array(
                            'template' => $this->_layout
                        )),
                'modalUrl'          => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxModalWindow',
                        array(
                            'elem_layout'   => $params->get('_layout'),
                            'elem_position' => $params->get('_position'),
                            'elem_index'    => $params->get('_index'),
                        )),
                'interfaceParams'   => array(
                    'currencyDefault' => $this->config->get('currency_default', 'EUR'),
                    'startValue'      => (float)$params->get('count_default', 1),
                    'multipleValue'   => (float)$params->get('count_multiple', 1),
                    'advFieldText'    => (int)$this->config->get('adv_field_text', 0),
                    'advAllExistShow' => (int)$this->config->get('adv_all_exists_show', 1),
                )
            ));
        }

        return null;
    }

    /**
     * Get default variant index
     *
     * @return mixed
     */
    public function defaultVariantKey()
    {
        $data    = $this->getBasicReadableData();
        $default = $data->get('default_variant');

        return JString::trim($default);
    }

    /**
     * Get default variant if it is
     *
     * @return array|bool
     */
    public function getDefaultVariant()
    {
        $default = $this->defaultVariantKey();

        if (JString::strlen($default) >= 1) {

            $variant = $this->getVariations($default);

            return $variant;
        }

        return false;
    }

    /**
     * Get prices for default variant
     * @return array|bool
     */
    public function getDefaultVariantPrices()
    {
        $prices = array();

        if ($default = $this->getDefaultVariant()) {
            $prices = $this->getPricesByVariant($default);
        }

        return $prices;
    }

    /**
     * Render "Modal window" template
     *
     * @param $params
     *
     * @return string
     */
    protected function _renderTmplModal($params)
    {
        $layout = $this->getLayout('tmpl_modal.php');
        $prices = $this->_getTmplPrices($params);
        $item   = $this->getItem();

        $mode = $params->get('button_mode_popup', 'normal');
        $params->set('button_mode', $mode);

        return self::renderLayout($layout, array(
            'skuTmpl'           => $this->_renderSku($params),
            'balanceTmpl'       => $this->_renderBalance($params),
            'countTmpl'         => $this->_renderCount($params),
            'pricesTmpl'        => $this->_renderPrices($params, $prices),
            'buttonsTmpl'       => $this->_renderButtons($params),
            'prices'            => $prices,
            'basketUrl'         => $this->_getBasketUrl(),
            'addToCartUrl'      => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxAddToCart'),
            'removeFromCartUrl' => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxRemoveFromCart'),
            'modalUrl'          => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxModalWindow', array(
                    'elem_layout'   => $params->get('_layout'),
                    'elem_position' => $params->get('_position'),
                    'elem_index'    => $params->get('_index'),
                )),
            'interfaceParams'   => array(
                'currencyDefault' => $params->get('currency_default', 'EUR'),
                'startValue'      => (float)$params->get('count_default', 1),
                'multipleValue'   => (float)$params->get('count_multiple', 1),
                'advFieldText'    => (int)$this->config->get('adv_field_text', 0),
                'advAllExistShow' => (int)$this->config->get('adv_all_exists_show', 1),
            ),
        ));
    }

    /**
     * Overload params by prefix in key
     * TODO Remove this hack
     *
     * @param JSONData|array $params
     * @param string $prefix
     *
     * @return JSONData|array
     */
    protected function _overloadParamsByPrefix($params, $prefix)
    {
        foreach ($params as $key => $value) {
            if (strpos($key, $prefix) === 0) {

                $key = str_replace($prefix, '', $key);
                if (is_array($params)) {
                    $params[$key] = $value;
                } else {
                    $params->set($key, $value);
                }
            }
        }

        return $params;
    }

    /**
     * @param $image
     * @param $params
     *
     * @return JSONData|string
     */
    public function getImage($image, $params = array())
    {
        if (empty($image)) {
            return $image;
        }

        if (is_array($image)) {
            $image = $image['value'];
        }

        if (empty($params)) {
            $params = $this->getCoreParamConfig(self::PARAM_IMAGE_IDENTIFIER);
        }

        if (!$params) {
            $params = $this->app->data->create($params);
        }

        $width  = $params->get('width');
        $height = $params->get('height');

        $url = new stdClass();

        $url->url  = $this->_image->getUrl($image);
        $url->orig = $image;
        if ($width || $height) {
            $url = $this->_image->resize($image, $width, $height);
        }

        $width_pop  = $params->get('width_popup');
        $height_pop = $params->get('height_popup');

        $url->pop_up = $url->url;

        if ($width_pop || $height_pop) {

            $pop_up = $this->_image->resize($image, $width_pop, $height_pop);

            $url->pop_up = $pop_up->url;
        }

        return $url;
    }

    /**
     * Get currency list
     *
     * @param array $params
     *
     * @return array
     */
    public function getCurrencyList($params = array())
    {
        $all = $this->app->jbmoney->getCurrencyList(true);

        $default = $this->_getDefaultCurrency();

        if (empty($list)) {
            return $all;
        }

        if (!in_array($default, $list)) {
            $list[] = $default;
        }
        $list = array_unique($list);

        $result = array();
        foreach ($list as $currency) {
            if (isset($all[$currency])) {
                $result[$currency] = $all[$currency];
            }
        }

        return $result;
    }

    /**
     * Get variant from $this->data() by values
     * MODE: DEFAULT
     *
     * @param  array $values values from front end
     *
     * @return array
     */
    public function getVariantByValues($values = array())
    {
        $data = $this->_getValues();

        if (empty($values) || empty($data)) {
            return $values;
        }

        $variations = $this->getVariations();

        $i = null;
        foreach ($data as $i => $value) {
            foreach ($values as $identifier => $fields) {
                $valError = false;
                $idError  = false;

                if (!isset($value[$identifier])
                    || count($values) !== count($value)
                ) {
                    $idError = true;
                }

                if ($idError === false) {
                    if (isset($fields['value']) && empty($fields['value'])) {
                        unset($fields);
                    }

                    if (!empty($fields)) {
                        $diff = array_diff_assoc($fields, $value[$identifier]);
                    }

                    if (!empty($diff)) {
                        $valError = true;
                    }
                }

                if ($idError === true || $valError === true) {
                    unset($variations[$i]);
                }
            }
        }

        //$this->getPricesByVariant($variations[key($variations)])
        $variant = !empty($variations) ? $variations[key($variations)] : array();

        if ($variant) {
            $variant['no'] = (int)key($variations);
        }

        return $variant;
    }

    /**
     * Get variant from $this->data() by values
     * MODE: OVERLAY
     *
     * @param  array $values
     *
     * @return array
     */
    public function getVariantByValuesOverlay($values = array())
    {
        $data = $this->_getValues();

        if (empty($values) || empty($data)) {
            return $values;
        }

        $basicData = $this->getBasicData();
        $basic     = $this->getReadableData($basicData);
        $result    = array();

        foreach ($data as $i => $value) {
            foreach ($value as $identifier => $fields) {

                if (isset($values[$identifier])) {
                    $diff = array_diff_assoc($fields, $values[$identifier]);

                    if (empty($diff)) {
                        $result[$i] = $this->getVariations($i);
                    }
                }
            }
        }

        $value    = $basic->find('_value.value', 0);
        $currency = $basic->find('_currency.value');

        if (!empty($result)) {
            $basicData['no'] = '';

            foreach ($result as $key => $variant) {
                $variant = $this->getReadableData($variant);

                $variantValue    = $variant->find('_value.value', 0);
                $variantCurrency = $variant->find('_currency.value');

                $value = $this->_jbmoney->calcDiscount($value, $currency, $variantValue, $variantCurrency);

                $basicData['no'] .= $key . '-';

            }

            $basicData['_value']['value'] = $value;
        }

        return !empty($basicData) ? $basicData : array();
    }

    /**
     * @param array $data
     * @param array $values
     *
     * @return bool
     */
    public function next($data = array(), &$values = array())
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $identifier => $element) {

            $valError = false;
            $idError  = false;

            if (!isset($values[$identifier])) {
                $idError = true;
            }

            if ($idError === false) {
                $diff = array_diff_assoc($element, $values[$identifier]);

                if (!empty($diff)) {
                    $valError = true;
                }
            }

            if ($idError === false && $valError === false) {
                return true;
            } else {
                $count = count($values);

                if ($count == 0) {
                    return false;
                }

                end($values);
                $key = key($values);

                unset($values[$key]);

                $this->next($data, $values);
            }
        }

        return false;
    }

    /**
     * Get prices for variant or basic after calc method
     *
     * @param array $data
     *
     * @return array|bool
     */
    public function getPrices($data = array())
    {
        if (empty($data)) {
            return false;
        }

        $currencies = $this->getCurrencyList();
        $default    = $this->_getDefaultCurrency();
        $result     = array();

        foreach ($currencies as $currency) {

            $priceNoFormat = $this->_jbmoney->convert($default, $currency, $data['price']);
            $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

            $totalNoFormat = $this->_jbmoney->convert($default, $currency, $data['total']);
            $total         = $this->_jbmoney->toFormat($totalNoFormat, $currency);

            $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
            $save         = $this->_jbmoney->toFormat($saveNoFormat, $currency);

            $result[$currency] = array(
                'totalNoFormat' => $totalNoFormat,
                'priceNoFormat' => $priceNoFormat,
                'saveNoFormat'  => $saveNoFormat,
                'total'         => $total,
                'price'         => $price,
                'save'          => $save,
            );
        }

        return $result;
    }

    /**
     * Get basic prices with currency list
     *
     * @return array
     */
    public function getBasicPrices()
    {
        $calc  = $this->calcBasic();
        $image = $this->getImage($calc['image']);

        $result = array(
            'sku'         => $this->renderElementHTML('_sku'),
            'balance'     => $this->renderElementHTML('_balance'),
            'image'       => !empty($image) ? $image->url : $image,
            'pop_up'      => isset($image->pop_up) ? $image->pop_up : null,
            'description' => $this->renderElementHTML('_description'),
            'value'       => $this->renderElementHTML('_value')
        );

        $result = array_merge($result, $this->getPrices($calc));

        return $result;
    }

    /**
     * Get total price for basic data
     *
     * @param string $currency
     *
     * @return mixed
     */
    public function calcBasic($currency = self::DEFAULT_CURRENCY)
    {
        $basic = $this->getBasicReadableData();

        $discountVal = $basic->find('_discount.value');
        $discountCur = $basic->find('_discount.currency');

        $price     = $this->_jbmoney->convert($basic->find('_currency.value'), $currency, $basic->find('_value.value'));
        $basePrice =
            $this->_jbmoney->calcDiscount($basic->find('_value.value'), $basic->find('_currency.value'), $discountVal,
                $discountCur);
        $total     = $this->_jbmoney->convert($basic->find('_currency.value'), $currency, $basePrice);

        $result = array(
            'total'       => $total,
            'price'       => $price,
            'item_id'     => $this->getItem()->id,
            'sku'         => $basic->find('_sku.value', $this->getItem()->id),
            'name'        => $this->getItem()->name,
            'currency'    => $basic->find('_currency.value', 'EUR'),
            'image'       => $basic->find('_image.value'),
            'params'      =>
                array(
                    'width'  => (float)$basic->find('_properties.width', 0),
                    'height' => (float)$basic->find('_properties.height', 0),
                    'length' => (float)$basic->find('_properties.length', 0),
                    'weight' => (float)$basic->find('_weight.value', 0)
                ),
            'priceParams' => array()
        );

        return $result;
    }

    /**
     * @param array $variant
     *
     * @return array|bool
     */
    public function getPricesByVariant($variant = array())
    {
        if (empty($variant)) {
            return false;
        }

        $variant = $this->getReadableData($variant);

        $calc  = $this->calcVariant($variant);
        $image = $this->getImage($calc['image']);
        $no    = $calc['no'];

        $result = array(
            'sku'         => $this->renderElementHTML('_sku', $no),
            'balance'     => $this->renderElementHTML('_balance', $no),
            'image'       => !empty($image) ? $image->url : $image,
            'pop_up'      => isset($image->pop_up) ? $image->pop_up : null,
            'description' => $this->renderElementHTML('_description', $no),
            'value'       => $this->renderElementHTML('_value', $no)
        );

        $result = array_merge($result, $this->getPrices($calc));

        return $result;
    }

    /**
     * @param array $variant
     * @param string $currency
     *
     * @return array|float|mixed
     */
    public function calcVariant($variant = array(), $currency = self::DEFAULT_CURRENCY)
    {
        if (empty($variant)) {
            return $variant;
        }

        $default = $currency;

        $basic = $this->getBasicReadableData();

        /*$variant = array_filter(array_map('array_filter', (array)$variant));
        $variant = array_merge((array)$basic, (array)$variant);
        $variant = $this->app->data->create($variant);*/

        $variant = $this->getReadableData((array)$variant);

        $basicCurrency = $basic->find('_currency.value', $default);
        $basicValue    = $basic->find('_value.value', 0);

        $bDiscount = $basic->get('_discount');

        $variantValue    = $variant->find('_value.value', $basicValue);
        $variantCurrency = $variant->find('_currency.value', $default);

        $discountVal = (float)$variant->find('_discount.value', 0);
        $discountCur = $variant->find('_discount.currency', $default);

        $price = $this->_jbmoney->calc($basicValue, $basicCurrency, $variantValue, $variantCurrency);
        $price = $this->_jbmoney->convert($basicCurrency, $default, $price);

        if (true) {
            //$value = $this->_jbmoney->calcDiscount($value, $basicCurrency, $bDiscount['value'], $bDiscount['currency']);
        }

        $total = $this->_jbmoney->calcDiscount($price, $variantCurrency, $discountVal, $discountCur);
        $total = $this->_jbmoney->convert($variantCurrency, $default, $total);

        $sku = $variant->find('_sku.value');
        $sku = JString::strlen($sku) === 0 ? $basic->find('_sku.value') : $sku;

        $result = array(
            'total'       => $total,
            'price'       => $price,
            'item_id'     => $this->getItem()->id,
            'sku'         => $sku,
            'name'        => $this->getItem()->name,
            'currency'    => $default,
            'image'       => $variant->find('_image.value'),
            'params'      => array(
                'width'  => (float)$variant->find('_properties.width', 0),
                'height' => (float)$variant->find('_properties.height', 0),
                'length' => (float)$variant->find('_properties.length', 0),
                'weight' => (float)$variant->find('_weight.value', 0)
            ),
            'priceParams' => array(),
            'no'          => $variant->get('no')
        );

        return $result;
    }

    /**
     * Render JBPriceAdvance elements html
     *
     * @param string $identifier - element identifier
     * @param null|int $variant - number of variant
     *
     * @return mixed|null|string
     */
    public function renderElementHTML($identifier, $variant = null)
    {
        $params = $this->getCoreParamConfig($identifier);

        $html = null;
        if ($params) {
            $element = $this->getParam($identifier, $variant);
            $html    = $element->render($params);
        }

        return $html;
    }

    public function mergeVariant($variant, $basic = array())
    {
        $basic = $this->getBasicReadableData();
    }

    /**
     * @return array
     */
    public function getIndexData()
    {
        $result = array();
        $data   = $this->calcBasic();
        $basic  = $this->getBasicReadableData();
        unset($data['priceParams']);
        unset($data['name']);

        $description = JString::trim($basic->find('_description.value', null));

        $data['balance']     = $basic->find('_balance.value', -1);
        $data['description'] = empty($description) ? null : $description;
        $data['discount']    = (int)($basic->find('_discount.value') < 0);
        $data['params']      = $this->app->data->create($data['params']);
        $data['type']        = self::TYPE_PRIMARY;
        $data['element_id']  = $this->identifier;

        $result[$this->getItem()->id] = $data;

        /*$variations = $this->getVariations();

        if (!empty($variations)) {

            foreach ($variations as $key => $variant) {

                $readable = $this->getReadableData($variant);
                $variant  = $this->calcVariant($variant);

                $variant['params']     = $this->app->data->create($variant['params']);
                $variant['type']       = self::TYPE_SECONDARY;
                $variant['element_id'] = $this->getKey($variant);

                unset($variant['no']);
                unset($variant['name']);
                unset($variant['priceParams']);
                $result[$this->getKey($variant)] = $variant;
            }
        }*/

        return $result;
    }

    /**
     * Get convenient format basic data
     *
     * @return mixed
     */
    public function getBasicReadableData()
    {
        $basic = $this->getBasicData();
        $basic = $this->getReadableData($basic->getArrayCopy());

        return $basic;
    }

    /**
     *
     * @param array $data
     *
     * @return mixed
     */
    public function getReadableData($data = array())
    {
        if (!is_array($data)) {
            return $data;
        }

        $params = isset($data['params']) ? $data['params'] : array();
        unset($data['params']);

        $result = array_merge($data, $params);
        $result = $this->app->data->create($result);

        return $result;
    }

    /**
     * @return array
     */
    protected function _getValues()
    {
        $data = $this->data();

        return isset($data['values']) ? $data['values'] : array();
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $value
     * @param  array $attrs
     *
     * @return mixed
     */
    protected function _renderRow($id, $name, $value, $attrs = array())
    {
        $attribes = array_merge(array(
            'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
            'title'       => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
            'class'       => 'row-' . $name . ' hasTip basic-sku',
            'id'          => $this->app->jbstring->getId() . '-' . $name
        ), $attrs);


        return $this->app->html->_('control.text', $this->getParamName($id, $name), $value,
            $this->app->jbhtml->buildAttrs($attribes));
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        parent::setType($type);
        //$this->getParams();
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        parent::setItem($item);
        $this->getParamsConfig();
    }

    /**
     * Get weight by key
     *
     * @param $key
     *
     * @return int
     */
    public function getWeight($key)
    {
        $variant = $this->getVariations($key);
        $variant = $this->getReadableData($variant);

        $weight = $variant->get(self::PARAM_WEIGHT_ID);

        return isset($weight) && !empty($weight['value']) ? $weight['value'] : 1;
    }

    /**
     * Get data by param
     *
     * @param $param
     *
     * @return mixed
     */
    public function getParamData($param)
    {
        $basic   = $this->getBasicReadableData();
        $variant = $param->config->get('_variant');
        $variant = JString::trim($variant);

        $data = $basic->get($param->identifier, array());
        if (JString::strlen($variant) >= 1) {

            $variant = $this->getVariations($variant);
            $variant = $this->getReadableData($variant);

            $data = $variant->get($param->identifier, array());
        }

        return $data;
    }

    /**
     * @param  string $identifier elementID
     * @param  null|int $variant variant key
     *
     * @return bool|JBCartElement|null
     */
    public function getParam($identifier, $variant = null)
    {
        // has element already been loaded?
        if (!$param = isset($this->_params[$identifier]) ? $this->_params[$identifier] : null) {

            if ($config = $this->getParamConfig($identifier)) {

                if ($param = $this->_jbcartelement->create($config->get('type'), $config->get('group'), $config)) {

                    $param->identifier = $identifier;

                    $this->_params[$identifier] = $param;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $param = clone($param);

        if (JString::strlen($variant) >= 1) {

            $config = $this->getParamConfig($identifier);
            $config->set('_variant', $variant);

            $param->setConfig($config);
        }

        $data = $this->getParamData($param);
        $param->bindData($data);

        $param->setJBPrice($this);

        return $param;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        if (is_null($this->params)) {
            $this->getParamsConfig();
        }

        return $this->_getParams(array_keys(array_diff_key($this->params, $this->getCoreParamsConfig())));
    }

    /**
     * @return array
     */
    public function getCoreParams()
    {
        return $this->_getParams(array_keys($this->getCoreParamsConfig()));
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getParamsByType($type)
    {
        return array_filter($this->getParams(),
            create_function('$element', 'return $element->getElementType() == "' . $type . '";'));
    }

    /**
     * @param $identifier
     *
     * @return null
     */
    public function getParamConfig($identifier)
    {
        if (is_null($this->params)) {
            $this->getParamsConfig();
        }

        if (isset($this->params[$identifier])) {
            return $this->app->data->create($this->params[$identifier]);
        }

        if (isset($this->filter_params[$identifier])) {
            return $this->app->data->create($this->filter_params[$identifier]);
        }

        return $this->getCoreParamConfig($identifier);
    }

    /**
     * Get render params for price param
     *
     * @param $identifier
     *
     * @return null
     */
    public function getCoreParamConfig($identifier)
    {
        $core_config = $this->getCoreParamsConfig();

        if (isset($core_config[$identifier])) {

            $param = $core_config[$identifier];
            if ($param['system'] || !$param['system'] && isset($this->params[$identifier])) {
                return $this->app->data->create($param);
            }
        }

        return null;
    }

    /**
     * Load core params
     *
     * @param bool $core
     *
     * @return array
     */
    public function getParamsConfig($core = true)
    {
        if (!$this->params) {
            $this->params = $this->_config
                ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->identifier)
                ->get('list', array());
        }

        if ($core === true) {
            $this->getCoreParamsConfig();
            $this->getFilterParamsConfig();
        }

        return $this->params;
    }

    /**
     * Load params for core/unique price params
     *
     * @return array
     */
    public function getCoreParamsConfig()
    {
        if (!$this->_layout) {
            return array();
        }

        if (!$this->core_params) {

            $config = JBCart::CONFIG_PRICE_TMPL . '.' . $this->identifier . '.' . $this->_layout;

            $this->core_params = $this->_position->loadParams($config);
        }

        return $this->core_params;
    }

    /**
     * Load params for core/unique price params
     *
     * @return array
     */
    public function getFilterParamsConfig()
    {
        if (!$this->_filter_layout) {
            return array();
        }

        if (!$this->filter_params) {

            $config = JBCart::CONFIG_PRICE_TMPL_FILTER . '.' . $this->identifier . '.' . $this->_filter_layout;

            $this->filter_params = $this->_position->loadParams($config);
        }

        return $this->filter_params;
    }

    /**
     * @param $identifiers
     *
     * @return array
     */
    protected function _getParams($identifiers)
    {
        if ($identifiers) {
            $params = array();
            foreach ($identifiers as $identifier) {

                if ($param = $this->getParam($identifier)) {
                    $params[$identifier] = $param;
                }
            }

            return $params;
        }

        return array();
    }

    /**
     * Get variation list
     *
     * @param  int $variant
     *
     * @return array
     */
    public function getVariations($variant = null)
    {
        $result = array();

        if (!(int)$this->config->get('mode', 0)) {
            return $result;
        }
        $data    = $this->data();
        $default = $this->_getDefaultData();

        if (isset($variant)) {

            if ($data->find('variations.' . $variant)) {

                $data = $data->find('variations.' . $variant);

                $data['no'] = $variant;

                return $data;
            }

            return $result;
        }

        if (isset($data['variations'])) {

            foreach ($data['variations'] as $key => $variant) {
                $variant['no'] = $key;

                $result[] = array_merge($default, $variant);
            }

        }

        return $result;
    }

    /**
     * Get element JSONData Object
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
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setLayout($name, $value)
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Get general data values
     * @return array
     */
    public function getBasicData()
    {
        $data = $this->data();

        if ($data->has('basic')) {

            $data->exchangeArray($data->get('basic'));

            $id = $this->getItem()->id;
            if (JString::strlen($data->find('params._sku.value') === 0)) {
                $data->params['_sku']['value'] = $id;
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function _getDefaultCurrency()
    {
        $config = $this->_config->getGroup('cart.config');

        return $config->get('default_currency', 'EUR');
    }

    /**
     * Bind and validate data
     *
     * @param array $data
     */
    public function bindData($data = array())
    {
        $result    = array();
        $basicData = $data['basic'];

        foreach ($basicData as $key => $basic) {

            $result['basic'][$key] = $basic;

            if (!is_array($basic)) {
                $result['basic'][$key] = JString::trim($basic);
            }
        }

        foreach ($basicData['params'] as $key => $basic) {
            $result['basic']['params'][$key] = $basic;
        }

        if (isset($data['variations'])) {
            $variations = $data['variations'];

            for ($i = 0; $i < count($variations); $i++) {

                $result['variations'][$i]['_value']['value']    = JString::trim($variations[$i]['_value']['value']);
                $result['variations'][$i]['_currency']['value'] = JString::trim($variations[$i]['_currency']['value']);

                foreach ($variations[$i]['params'] as $key => $variant) {

                    if (mb_strlen($key) == self::SIMPLE_PARAM_LENGTH) {

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
                    //eva::p($variant);
                    /*if (is_array($variant)) {
                        $variantKey = key($variant);
                        $variant    = $variant[$variantKey];
                    }*/

                    $result['variations'][$i]['params'][$key] = $variant;
                }
            }
        }

        parent::bindData($result);
    }

    /**
     * Get hash for variant
     *
     * @param array $variant
     *
     * @return string
     */
    protected function _getHash(array $variant = array())
    {
        $itemId    = $this->_getItemId();
        $paramsArr = array();

        if (empty($variant)) {
            return (string)$itemId;
        }

        $i = 1;
        if (!empty($variant['params'])) {
            foreach ($variant['params'] as $key => $value) {

                if (strpos($key, '_') === 0) {
                    continue;
                }

                if (is_array($value)) {
                    if (!empty($value['color'])) {
                        $value = implode('-', $value['color']);
                    } else {
                        $value = implode('-', $value);
                    }
                }

                $value = JString::strtolower($value);

                $paramsArr[] = 'p' . $i . '-' . (isset($value) ? $value : '');

                $i++;
            }
        }

        $result = implode('_', $paramsArr);

        $result = $itemId . '-' . $result;

        return (string)$result;
    }

    /**
     * Get url to basket
     * @return string
     */
    protected function _getBasketUrl()
    {
        $basketUrl = null;

        $basketMenuitem = (int)$this->config->get('basket_menuitem');

        $basketUrl = $this->app->jbrouter->basket($basketMenuitem);

        return $basketUrl;
    }

    /**
     * Ajax add to cart (multi)
     * Experimental
     */
    public function ajaxAddToCartMulti()
    {
        $elements = $this->getItem()->getElementsByType('relatedproducts');
        reset($elements);
        $element = current($elements);

        $items = $element->getRelatedItems();
        foreach ($items as $item) {
            $elements = $item->getElementsByType('jbpriceadvance');
            reset($elements);
            $price = current($elements);
            $price->ajaxAddToCart(1, array(), false);
        }

        $this->app->jbajax->send();
    }

    /**
     * Ajax add to cart method
     *
     * @param int $quantity
     * @param array $values
     * @param bool $sendAjax
     */
    public function ajaxAddToCart($quantity = 1, $values = array(), $sendAjax = true)
    {
        $jbAjax = $this->app->jbajax;
        $cart   = JBCart::getInstance();
        $mode   = (int)$this->config->get('price_mode', 1);

        if ($mode == self::PRICE_MODE_OVERLAY) {
            $variant = $this->getVariantByValuesOverlay($values);
        } else if ($mode == self::PRICE_MODE_DEFAULT) {
            $variant = $this->getVariantByValues($values);
        }

        if (empty($variant)) {
            $item    = $this->calcBasic();
            $variant = $this->getBasicData();
        } else {
            $item = $this->calcVariant($variant);
        }

        $item['price'] = $item['total'];
        unset($item['total']);

        $key = $this->getKey($variant);
        if ($this->inStock($key, $quantity)) {

            $params = array(
                'key' => $key
            );

            $item['priceParams'] = $this->getParamsNames($values);
            $item['quantity']    = $quantity;

            $cart->addItem($item, $params);

            $sendAjax && $jbAjax->send(array(), true);

        } else {

            $sendAjax && $jbAjax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY')), false);
        }

        $sendAjax && $jbAjax->send(array('added' => 0, 'message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')));


        /*$hash    = $this->_getHash(array(
            'param1'      => isset($params['1']) ? $params['1'] : '',
            'param2'      => isset($params['2']) ? $params['2'] : '',
            'param3'      => isset($params['3']) ? $params['3'] : '',
            'description' => isset($params['desc']) ? $params['desc'] : '',
        ));

        $quantity += $this->app->jbcart->getQuantityByHash($hash);

        if ($this->isInStock($hash, $quantity)) {

            $price      = $this->_getPriceByHash($hash);
            $item       = $this->getItem();
            $textParams = $this->_getFormatedParams($params);
            $variations = $this->getIndexData();

            if (isset($variations[$hash]) || (int)$this->config->get('adv_all_exists_show', 1)) {

                $this->app->jbcart->addItem($item, array(
                    'hash'        => $hash,
                    'sku'         => $price['sku'],
                    'itemId'      => $item->id,
                    'quantity'    => (int)$quantity,
                    'price'       => isset($price['total']) ? $price['total'] : '',
                    'currency'    => $this->_getDefaultCurrency(),
                    'priceDesc'   => isset($price['params']['description']) ? $price['params']['description'] : '',
                    'priceParams' => $textParams,
                ), true);
                $sendAjax && $jbajax->send(array(), true);

            } else {
                $sendAjax && $jbajax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NOT_FOUND')), false);
            }

        } else {
            $sendAjax && $jbajax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY')), false);
        }

        $sendAjax && $jbajax->send(array('added' => 0, 'message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')));*/
    }

    /**
     * Remove from cart method
     */
    public function ajaxRemoveFromCart()
    {
        $cart = JBCart::getInstance();

        $result = $cart->remove($this->getItem()->id);
        $this->app->jbajax->send(array('removed' => $result));
    }

    /**
     * @param string $template
     * @param array $values
     */
    public function ajaxChangeVariant($template = 'default', $values = array())
    {
        $this->_layout = $template;

        $priceMode = (int)$this->config->get('price_mode', 1);
        $variant   = array();

        if ($priceMode == self::PRICE_MODE_OVERLAY) {
            $variant = $this->getVariantByValuesOverlay($values);
        } else if ($priceMode == self::PRICE_MODE_DEFAULT) {
            $variant = $this->getVariantByValues($values);
        }

        $prices = $this->getPricesByVariant($variant);

        if (!empty($prices)) {
            $this->app->jbajax->send(
                $prices
            );
        }

        $this->app->jbajax->send(array(), false);

    }

    /**
     * Ajax method for modal window
     *
     * @param string $layout
     * @param string $position
     * @param int $index
     */
    public function ajaxModalWindow($layout = 'full', $position = '', $index = 1)
    {
        if ($params = $this->_getRenderParams($layout, $position, $index)) {

            $params['template'] = 'modal';

            $params = $this->_overloadParamsByPrefix($params, 'modal_');

            $complexRender = $this->render($params);

            echo self::renderLayout($this->getLayout('modal.php'), array(
                'complexRender' => $complexRender,
            ));
        }

        echo '';
    }

    /**
     * Get key for session
     *
     * @param array $variant
     *
     * @return string
     */
    public function getKey($variant = array())
    {
        $key = $this->getItem()->id;
        $no  = null;

        if (!empty($variant)) {
            $no = (isset($variant['no']) || isset($variant['no']) && $variant['no'] === 0 ? '_' . $variant['no'] : '');
        }

        $id = '_' . $this->identifier;

        return $key . $no . $id;
    }

    /**
     * Is in stock item
     *
     * @param $key
     * @param $quantity
     *
     * @return bool
     */
    public function inStock($key, $quantity)
    {
        $cart  = JBCart::getInstance();
        $items = $cart->getItems(false);
        $keys  = explode('_', $key);

        $no = false;
        if (count($keys) === 3) {
            list(, $no,) = $keys;
        }

        $data = (array)$this->getBasicData();
        if (JString::strlen($no) > 0) {
            $data = $this->getVariations($no);
        }

        $data  = $this->getReadableData($data);
        $value = $data->find('_balance.value');

        $quantity += (float)$items->find($key . '.quantity');

        if (!empty($data)) {

            if (isset($value) && $value == 0) {
                return false;

            } else if ($value == -1 || $value >= $quantity) {
                return true;

            } else if (!isset($value)) {
                return true;

            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Check is good in stock
     *
     * @param $hash
     * @param $quantity
     *
     * @return bool
     */
    public function isInStock($hash, $quantity)
    {
        $data     = $this->getIndexData();
        $mainHash = $this->_getHash();

        if ($mainHash !== $hash) {
            foreach ($data as $variant) {
                if ($hash === $variant['hash']) {

                    if ($variant['balance'] == 0) {
                        return false;
                    }

                    if ($variant['balance'] == -1 || $variant['balance'] >= $quantity) {
                        return true;
                    }

                    return false;
                }
            }
        }

        $mainBalance = $data[$this->_getHash()]['balance'];
        if ($mainBalance == 0) {
            return false;
        }

        if ($mainBalance == -1 || $mainBalance >= $quantity) {
            return true;
        }

        return false;
    }

    /**
     * Get params name by identifier
     *
     * @param  array $identifiers
     *
     * @return array
     */
    public function getParamsNames(array $identifiers = array())
    {
        $result = array();
        if (!empty($identifiers)) {
            foreach ($identifiers as $id => $value) {

                if (isset($value['value']) && empty($value['value'])) {
                    continue;
                }
                $result[$this->paramName($id)] = implode($value);
            }
        }

        return $result;
    }

    /**
     * Get param name by identifier
     *
     * @param $identifier
     *
     * @return null|string
     */
    public function paramName($identifier)
    {
        $string = $this->app->jbstring;
        if ($element = $this->params[$identifier]) {

            $name = ucfirst($string->clean($element['name']));

            return $name;
        }

        return null;
    }

    /**
     * Get render params
     *
     * @param $layout
     * @param $position
     * @param $index
     *
     * @return null|array
     */
    protected function _getRenderParams($layout, $position, $index)
    {
        $template = $this->getItem()->getApplication()->getTemplate()->name;

        // TODO remove paths hardcode
        $paths = array(
            $this->app->path->path('jbapp:templates/' . $template . '/renderer/item/positions.config'),
            JPATH_BASE . '/modules/mod_zooitem/renderer/item/positions.config',
            JPATH_BASE . '/plugins/system/widgetkit_zoo/widgets/slideset/renderer/item/positions.config',
            $this->app->path->path('mod_jbzoo_item:renderer/item/positions.config')
        );

        foreach ($paths as $path) {
            $config = $this->app->parameter->create($this->app->jbfile->read($path));
            $params = $config->get(JBZOO_APP_GROUP . '.' . $this->getItem()->type . '.' . $layout);
            if (!empty($params)) {
                break;
            }
        }

        $currentIndex = 0;
        if (!empty($params)) {
            foreach ($params[$position] as $key => $element) {

                if (isset($element['element']) && $element['element'] == $this->identifier) {
                    if ($currentIndex == $index) {
                        return $element;
                    }
                }

                $currentIndex++;
            }
        }

        return null;
    }

    /**
     * Get default value for SKU
     * @return string
     */
    public function getDefaultSku()
    {
        $basic = $this->getBasicReadableData();

        if (!$basic->find('_sku.value', $this->getItem()->id)) {
            return $this->getItem()->id;
        }

        return $basic->find('_sku.value');
    }

    /**
     * Get default data
     * @return array
     */
    protected function _getDefaultData()
    {
        return array();
    }

    /**
     * Merge default data
     *
     * @param $data
     *
     * @return array
     */
    protected function _mergeDefaultData($data)
    {
        $defaultData = $this->_getDefaultData();

        if (isset($data['basic'])) {
            $data['basic'] = array_merge($defaultData, $data['basic']);
        }

        if (isset($data['variations'])) {
            foreach ($data['variations'] as $key => $variant) {
                $data['variations'][$key] = array_merge($defaultData, $variant);
            }
        }

        return $data;
    }

    /**
     * Reduce the balance value by hash
     *
     * @param $hash
     * @param $quantity
     *
     * @return bool
     */
    public function balanceReduce($hash, $quantity)
    {
        if ($this->isInStock($hash, $quantity)) {
            $data = $this->data();

            if ($this->_getHash() === $hash) {
                $balance = $data['basic']['balance'];
                if ($balance == 0) {
                    return false;
                }

                if ($balance == -1) {
                    return true;
                }

                if ($balance >= $quantity) {
                    $data['basic']['balance'] -= $quantity;
                }

            } else if (isset($data['variations'][$hash])) {

                $balance = $data['variations'][$hash]['balance'];

                if ($balance == 0) {
                    return false;
                }

                if ($balance == -1) {
                    return true;
                }

                if ($balance >= $quantity) {
                    $data['variations'][$hash]['balance'] -= $quantity;
                }

            }

            $this->bindData($data);
            $this->app->table->item->save($this->getItem());

            return true;
        }

        return false;
    }

    /**
     * Get item id
     * @return int
     */
    protected function _getItemId()
    {
        $itemId = (int)$this->getItem()->id;
        if (empty($itemId)) {
            $itemId = JBModelItem::model()->getNextItemId();
        }

        return $itemId;
    }

    /**
     * @return bool
     */
    public function isOverlay()
    {
        return $this->config->get('price_mode') == self::PRICE_MODE_OVERLAY;
    }

}
