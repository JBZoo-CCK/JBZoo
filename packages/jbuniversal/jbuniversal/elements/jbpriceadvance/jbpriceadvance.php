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

    /**
     * @var Array of params config
     */
    public $params = array();

    /**
     * @var Array of core/unique price params config
     */
    public $core_params = array();

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = NULL;

    /**
     * @var JBCartElementHelper
     */
    protected $_jbcartelement = NULL;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position = NULL;

    /**
     * @var JBImageHelper
     */
    protected $_image = NULL;

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var null
     */
    protected $_layout = NULL;

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
                return FALSE;
            }
        }

        if (!(int)$params->get('show_empty_balance', 1)) {
            $basic = $this->getBasicData();

            if ((int)$basic['params']['_balance']['value'] == 0) {
                return FALSE;
            }
        }

        return TRUE;
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

        return NULL;
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
            $variationsHTML = NULL;
            //eva::p($this->getVariations());
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

        return NULL;
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
     * @param bool   $array
     *
     * @return string
     */
    public function getControlName($id, $name = NULL, $array = FALSE)
    {
        return "elements[{$this->identifier}][basic][{$id}]" . ($name ? "[{$name}]" : "") . ($array ? "[]" : "");
    }

    /**
     * @param string $id
     * @param string $name
     *
     * @return string
     */
    public function getControlParamName($id, $name = NULL)
    {
        return "elements[{$this->identifier}][basic][params][{$id}][{$name}]";
    }

    /**
     * Get name for prices variants
     *
     * @param string $id
     * @param string $name
     * @param int    $index
     *
     * @return string
     */
    public function getRowControlName($id, $name = NULL, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][{$id}]" . ($name ? "[{$name}]" : "");
    }

    /**
     * @param string $id
     * @param string $name
     * @param int    $key
     * @param int    $index
     *
     * @return string
     */
    public function getParamName($id, $name = NULL, $key = 0, $index = 0)
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
                            'elem_layout'   => $params->get('_layout'),
                            'elem_position' => $params->get('_position'),
                            'elem_index'    => $params->get('_index'),
                        )),
                'modalUrl'          => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxModalWindow',
                        array(
                            'elem_layout'   => $params->get('_layout'),
                            'elem_position' => $params->get('_position'),
                            'elem_index'    => $params->get('_index'),
                        )),
                'interfaceParams'   => array(
                    'currencyDefault' => $this->config->get('currency_default', 'EUR'),
                    'startValue'      => (int)$params->get('count_default', 1),
                    'multipleValue'   => (int)$params->get('count_multiple', 1),
                    'advFieldText'    => (int)$this->config->get('adv_field_text', 0),
                    'advAllExistShow' => (int)$this->config->get('adv_all_exists_show', 1),
                )
            ));
        }

        return NULL;
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

        return !empty($default) ? (int)$default : $default;
    }

    /**
     * Get default variant if it is
     *
     * @return array|bool
     */
    public function getDefaultVariant()
    {
        $default = $this->defaultVariantKey();

        if (!empty($default) || $default === 0) {

            $variant = $this->getVariations($default);

            return $variant;
        }

        return FALSE;
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
                'startValue'      => (int)$params->get('count_default', 1),
                'multipleValue'   => (int)$params->get('count_multiple', 1),
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
     * @param string         $prefix
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

        if(!$params) {
            $params = $this->app->data->create($params);
        }

        $width  = $params->get('width');
        $height = $params->get('height');

        $related = json_decode($params->get('image'), TRUE);
        $related = $this->app->data->create($related);

        $url = new stdClass();

        $url->url  = $this->_image->getUrl($image);
        $url->orig = $image;
        if ($width || $height) {
            $url = $this->_image->resize($image, $width, $height);
        }

        if ($related->get('template') == 'popup') {

            $width_pop  = $related->get('width_pop');
            $height_pop = $related->get('height_pop');

            $url->pop_up = $url->url;

            if ($width_pop || $height_pop) {

                $pop_up = $this->_image->resize($image, $width_pop, $height_pop);

                $url->pop_up = $pop_up->url;
            }
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
        $all = $this->app->jbmoney->getCurrencyList(TRUE);

        if (empty($params)) {
            $params = $this->app->data->create($this->config);
        }

        $default = $params->get('currency_default', 'EUR');
        $list    = $params->get('currency_list', array());

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

        $i = NULL;
        foreach ($data as $i => $value) {
            foreach ($values as $identifier => $fields) {
                $valError = FALSE;
                $idError  = FALSE;

                if (!isset($value[$identifier])
                    || count($values) !== count($value)
                ) {
                    $idError = TRUE;
                }

                if ($idError === FALSE) {
                    if (isset($fields['value']) && empty($fields['value'])) {
                        unset($fields);
                    }

                    if (!empty($fields)) {
                        $diff = array_diff_assoc($fields, $value[$identifier]);
                    }

                    if (!empty($diff)) {
                        $valError = TRUE;
                    }
                }

                if ($idError === TRUE || $valError === TRUE) {
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

        $price    = 0;
        $value    = $basic->find('_value.value', 0);
        $currency = $basic->find('_currency.value');

        if (!empty($result)) {
            $basicData['no'] = '';
            foreach ($result as $key => $variant) {

                $variant = $this->getReadableData($variant);

                $variantValue    = $variant->find('_value.value', 0);
                $variantCurrency = $variant->find('_currency.value');

                $newVal = $this->_jbmoney->calcDiscount($value, $currency, $variantValue, $variantCurrency);

                if ($newVal > $value) {
                    $price = $newVal - $value;
                }

                $basicData['_value']['value'] += $price;

                $basicData['no'] .= '-' . $key;
            }
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
            return FALSE;
        }

        foreach ($data as $identifier => $element) {

            $valError = FALSE;
            $idError  = FALSE;

            if (!isset($values[$identifier])) {
                $idError = TRUE;
            }

            if ($idError === FALSE) {
                $diff = array_diff_assoc($element, $values[$identifier]);

                if (!empty($diff)) {
                    $valError = TRUE;
                }
            }

            if ($idError === FALSE && $valError === FALSE) {
                return TRUE;
            } else {
                $count = count($values);

                if ($count == 0) {
                    return FALSE;
                }

                end($values);
                $key = key($values);

                unset($values[$key]);

                $this->next($data, $values);
            }
        }

        return FALSE;
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
            return FALSE;
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
        $basic = $this->getBasicReadableData();
        $calc  = $this->calcBasic();
        $image = $this->getImage($calc['image']);

        $result = array(
            'sku'         => $basic->find('_sku.value', $this->getItem()->id),
            'balance'     => $basic->find('_balance.value', -1),
            'image'       => !empty($image) ? $image->url : $image,
            'pop_up'      => isset($image->pop_up) ? $image->pop_up : NULL,
            'description' => $basic->find('_description.value', '')
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
            $this->_jbmoney->calcDiscount($basic->find('_value.value'), $basic->find('_currency.value'),
                $discountVal, $discountCur);
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
            return FALSE;
        }

        $variant = $this->getReadableData($variant);
        $calc    = $this->calcVariant($variant);
        $image   = $this->getImage($calc['image']);

        $result = array(
            'sku'         => $variant->find('_sku.value', $this->getItem()->id),
            'balance'     => $variant->find('_balance.value', -1),
            'image'       => !empty($image) ? $image->url : $image,
            'pop_up'      => isset($image->pop_up) ? $image->pop_up : NULL,
            'description' => $variant->find('_description.value', '')
        );

        $result = array_merge($result, $this->getPrices($calc));

        return $result;
    }

    /**
     * @param array  $variant
     * @param string $currency
     *
     * @return array|float|mixed
     */
    public function calcVariant($variant = array(), $currency = self::DEFAULT_CURRENCY)
    {
        if (empty($variant)) {
            return $variant;
        }

        $default = $this->_getDefaultCurrency();
        $basic   = $this->getBasicReadableData();

        $basicCurrency = $basic->find('_currency.value', $default);
        $basicValue    = $basic->find('_value.value', 0);

        $bDiscount = $basic->get('_discount');

        $variant = $this->getReadableData($variant);

        $variantValue    = $variant->find('_value.value', 0);
        $variantCurrency = $variant->find('_currency.value');

        $discountVal = $variant->find('_discount.value', 0);
        $discountCur = $variant->find('_discount.currency');

        $price = $this->_jbmoney->calc($basicValue, $basicCurrency, $variantValue, $variantCurrency);
        $price = $this->_jbmoney->convert($basicCurrency, $default, $price);

        if (TRUE) {
            //$value = $this->_jbmoney->calcDiscount($value, $basicCurrency, $bDiscount['value'], $bDiscount['currency']);
        }

        $total = $this->_jbmoney->calcDiscount($price, $variantCurrency, $discountVal, $discountCur);
        $total = $this->_jbmoney->convert($variantCurrency, $currency, $total);

        $result = array(
            'total'       => $total,
            'price'       => $price,
            'item_id'     => $this->getItem()->id,
            'sku'         => $variant->find('_sku.value', $this->getItem()->id),
            'name'        => $this->getItem()->name,
            'currency'    => $variantCurrency,
            'image'       => $variant->find('_image.value'),
            'params'      =>
                array(
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
     * Get convenient format basic data
     *
     * @return mixed
     */
    public function getBasicReadableData()
    {
        $basic = $this->getBasicData();

        return $this->getReadableData($basic);
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

        return $this->app->data->create($result);
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
        $attribes = array_merge(
            array(
                'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
                'title'       => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
                'class'       => 'row-' . $name . ' hasTip basic-sku',
                'id'          => $this->app->jbstring->getId() . '-' . $name
            ), $attrs
        );


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

        $data = $basic->get($param->identifier, array());
        if (!empty($variant) || $variant === 0) {
            $variant = $this->getVariations($variant);
            $variant = $this->getReadableData($variant);

            $data = $variant->get($param->identifier, array());
        }

        return $data;
    }

    /**
     * @param  string   $identifier elementID
     * @param  null|int $variant    variant key
     *
     * @return bool|JBCartElement|null
     */
    public function getParam($identifier, $variant = NULL)
    {
        // has element already been loaded?
        if (!$param = isset($this->_params[$identifier]) ? $this->_params[$identifier] : NULL) {

            if ($config = $this->getParamConfig($identifier)) {

                if ($param = $this->_jbcartelement->create($config->get('type'), $config->get('group'), $config)) {

                    $param->identifier = $identifier;

                    $this->_params[$identifier] = $param;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }

        $param = clone($param);

        if (!empty($variant) || $variant === 0) {

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
        if (!$this->params) {
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
        if (isset($this->params[$identifier])) {
            return $this->app->data->create($this->params[$identifier]);
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
            return $this->app->data->create($core_config[$identifier]);
        }

        return NULL;
    }

    /**
     * Load core params
     *
     * @param bool $core
     *
     * @return array
     */
    public function getParamsConfig($core = TRUE)
    {
        if (!$this->params) {
            $this->params =
                $this
                    ->_config
                    ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->identifier)
                    ->get('list', array());
        }

        if ($core === TRUE) {
            $this->getCoreParamsConfig();
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
        if (!$this->core_params) {

            $config = JBCart::CONFIG_PRICE_TMPL . '.' . $this->identifier . '.' . $this->_layout;

            $this->core_params = $this->_position->loadParams($config);
        }

        return $this->core_params;
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
    public function getVariations($variant = NULL)
    {
        $result = array();

        if (!(int)$this->config->get('mode', 0)) {
            return $result;
        }
        $data    = $this->data();
        $default = $this->_getDefaultData();

        if (isset($variant)) {
            if (isset($data['variations'][$variant])) {
                return array_merge($default, $data['variations'][$variant]);
            }

            return $result;
        }

        if (isset($data['variations'])) {

            foreach ($data['variations'] as $variant) {
                $result[] = array_merge($default, $variant);
            }

        }

        return $result;
    }

    /**
     * Get general data values
     * @return array
     */
    public function getBasicData()
    {
        $data = $this->data();

        return isset($data['basic']) ? $data['basic'] : array();
    }

    /**
     * @return string
     */
    protected function _getDefaultCurrency()
    {
        $config = $this->_config->getGroup('cart.config');

        return $config->get('default_currency');
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

                    if (strlen($key) == self::SIMPLE_PARAM_LENGTH) {

                        $result['values'][$i][$key] = $variant;
                        if (is_array($variant)) {
                            foreach ($variant as $j => $var) {

                                $var = JString::trim($var);

                                $variant[$j] = $var;
                                if (empty($var)) {
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
        $basketUrl = NULL;

        $basketMenuitem = (int)$this->config->get('basket_menuitem');
        $basketAppid    = (int)$this->config->get('basket_appid');

        if ($basketMenuitem && $basketAppid) {
            $basketUrl = $this->app->jbrouter->basket($basketMenuitem, $basketAppid);
        }

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
            $price->ajaxAddToCart(1, array(), FALSE);
        }

        $this->app->jbajax->send();
    }

    /**
     * Ajax add to cart method
     *
     * @param int   $quantity
     * @param array $values
     * @param bool  $sendAjax
     */
    public function ajaxAddToCart($quantity = 1, $values = array(), $sendAjax = TRUE)
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

        if ($this->inStock($variant, $quantity)) {

            $params = array(
                'key' => $this->getKey($variant)
            );

            $item['priceParams'] = $this->getParamsNames($values);
            $item['quantity']    = $quantity;

            $cart->addItem($item, $params);

            $sendAjax && $jbAjax->send(array(), TRUE);

        } else {

            $sendAjax && $jbAjax->send(array('message' => JText::_('JBZOO_JBPRICE_ITEM_NO_QUANTITY')), FALSE);
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
     * @param string $layout
     * @param string $position
     * @param array  $values
     * @param int    $index
     */
    public function ajaxChangeVariant($layout = 'full', $position = '', $index = 1, $values = array())
    {
        if ($params = $this->_getRenderParams($layout, $position, $index)) {

            $priceMode = (int)$this->config->get('price_mode', 1);

            $variant = array();
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

            $this->app->jbajax->send(array(), FALSE);
        }

        $this->app->jbajax->send(array(), FALSE);
    }

    /**
     * Ajax method for modal window
     *
     * @param string $layout
     * @param string $position
     * @param int    $index
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
        $no  = NULL;

        if (!empty($variant)) {
            $no = (isset($variant['no']) || isset($variant['no']) && $variant['no'] === 0 ? '_' . $variant['no'] : '');
        }

        $id = '_' . $this->identifier;

        return $key . $no . $id;
    }

    /**
     * Is in stock item
     *
     * @param $variant
     * @param $quantity
     *
     * @return bool
     */
    public function inStock($variant, $quantity)
    {
        if (is_array($variant)) {
            $variant = $this->getReadableData($variant);
            $value   = $variant->find('_balance.value');
        }

        if (!empty($variant)) {

            if (!isset($value)) {

                return TRUE;

            } elseif ($value == -1) {

                return TRUE;

            } elseif ($value >= $quantity) {

                return TRUE;

            } else {

                return FALSE;

            }

        }

        return FALSE;
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
                        return FALSE;
                    }

                    if ($variant['balance'] == -1 || $variant['balance'] >= $quantity) {
                        return TRUE;
                    }

                    return FALSE;
                }
            }
        }

        $mainBalance = $data[$this->_getHash()]['balance'];
        if ($mainBalance == 0) {
            return FALSE;
        }

        if ($mainBalance == -1 || $mainBalance >= $quantity) {
            return TRUE;
        }

        return FALSE;
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

        return NULL;
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

        return NULL;
    }

    /**
     * Get default value for SKU
     * @return string
     */
    protected function _getDefaultSku()
    {
        $basicData = $this->getBasicData();

        return !isset($basicData['params']['_sku']) ? $this->getItem()->id : $basicData['params']['_sku'];
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
                    return FALSE;
                }

                if ($balance == -1) {
                    return TRUE;
                }

                if ($balance >= $quantity) {
                    $data['basic']['balance'] -= $quantity;
                }

            } else if (isset($data['variations'][$hash])) {

                $balance = $data['variations'][$hash]['balance'];

                if ($balance == 0) {
                    return FALSE;
                }

                if ($balance == -1) {
                    return TRUE;
                }

                if ($balance >= $quantity) {
                    $data['variations'][$hash]['balance'] -= $quantity;
                }

            }

            $this->bindData($data);
            $this->app->table->item->save($this->getItem());

            return TRUE;
        }

        return FALSE;
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

}
