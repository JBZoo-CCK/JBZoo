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

    /**
     * @var
     */
    public $elementsConfig = array();

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
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var null
     */
    protected $_layout = null;

    /**
     * @var array
     */
    protected $_elements = array();

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
        $this->_jbmoney       = $this->app->jbmoney;
        $this->_jbcartelement = $this->app->jbcartelement;
        $this->_position      = $this->app->jbcartposition;
        $this->_config        = JBModelConfig::model();
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
            return isset($data['params']['_sku']) ? $data['params']['_sku'] : $this->getItem()->id;
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
        $this->app->jbassets->admin();
        $basicData          = $this->getBasicData();
        $basicData['basic'] = 1;

        if ($layout = $this->getLayout('edit.php')) {
            $variationsTpl = $this->getLayout('_variations.php');
            $variations    = $this->getVariations();

            if (empty($variations) && (int)$this->config->get('mode', 0)) {
                $basic      = $this->getBasicData();
                $variations = array($basic);

                $basic['description'] = '';
            }

            $renderer = $this->app->jbrenderer->create('jbprice');
            $params   = array(
                'config'       => $this->config,
                'currencyList' => $this->getCurrencyList(),
                'variations'   => $variations,
                'submission'   => $submission,
                'basicData'    => $this->getBasicData(),
                'renderer'     => $renderer
            );
            $basic    = $renderer->render('_edit',
                array(
                    'price' => $this,
                    'style' => self::BASIC_GROUP,
                    'data'  => $basicData
                )
            );

            $variationsHTML = self::renderLayout($variationsTpl, $params);

            $params['variationsTmpl'] = $variationsHTML;

            $params['basic'] = $basic;

            return self::renderLayout($layout, $params);
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
        $result = array();
        $data   = $this->data();

        if (!empty($data['basic'])) {
            $basicData   = $this->app->data->create($data['basic']);
            $basicParams = $this->app->data->create($basicData->get('params'));


            if ($value = $basicData->get($identifier)) {
                $result[] = $value;
            }

            if ($value = $basicParams->get($identifier)) {
                $result[] = $value;
            }
            unset($data['basic']);

            if (isset($data['variations'])) {
                for ($i = 0; $i < count($data['variations']); $i++) {

                    if (!isset($data['variations'][$i])) {
                        continue;
                    }

                    $variant       = $this->app->data->create($data['variations'][$i]);
                    $variantParams = $this->app->data->create($variant->get('params'));

                    if ($value = $variant->get($identifier)) {
                        $result[] = $value;
                    }

                    if ($value = $variantParams->get($identifier)) {
                        $result[] = $value;
                    }
                }
            }
        }
        //$result[] = $basicData->get($identifier);
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
     * @param      $name
     * @param bool $array
     *
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        return "elements[{$this->identifier}][basic][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getControlParamName($name)
    {
        return "elements[{$this->identifier}][basic][params][{$name}]";
    }

    /**
     * Get name for prices variants
     *
     * @param     $name
     * @param int $index
     *
     * @return string
     */
    public function getRowControlName($name, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][{$name}]";
    }

    /**
     * @param     $name
     * @param int $key
     * @param int $index
     *
     * @return string
     */
    public function getParamName($name, $key = 0, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][params][{$name}]";
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
            $this->app->validator->create('textfilter', array('required' => $params->get('required')))->clean($basic['_value']);
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
        $basicData  = $this->getBasicData();
        $mainPrices = $this->getPricesByVariant($basicData);

        $cart   = JBCart::getInstance();
        $params = $this->app->data->create($params);
        $item   = $this->getItem();
        $prices = $this->getDefaultVariantPrices();


        $this->_layout = $params->get('template', 'default');

        $renderer = $this->app->jbrenderer->create('jbprice');
        $elements = $renderer->render($this->_layout, array('price' => $this));

        if ($layout = $this->getLayout('render.php')) {
            return self::renderLayout($layout, array(
                'elements'          => $elements,
                'prices'            => $mainPrices,
                'default_variant'   => $prices,
                'isInCart'          => (int)$cart->inCart($item->id),
                'basketUrl'         => $this->_getBasketUrl(),
                'addToCartUrl'      => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxAddToCart'),
                'removeFromCartUrl' => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxRemoveFromCart'),
                'changeVariantUrl'  => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxChangeVariant', array(
                        'elem_layout'   => $params->get('_layout'),
                        'elem_position' => $params->get('_position'),
                        'elem_index'    => $params->get('_index'),
                    )),
                'modalUrl'          => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxModalWindow', array(
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

        return null;
    }

    /**
     * Get default variant if it is
     * @return array|bool
     */
    public function getDefaultVariant()
    {
        $data = $this->getBasicData();
        $data = $this->app->data->create($data);

        $default = $data->get('default_variant');
        if (!empty($default) || $default === 0) {

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
     * @param       $prices
     * @param array $data
     * @param       $params
     *
     * @return mixed
     */
    protected function _prepareData($prices, $params, $data = array())
    {
        if (empty($data)) {
            $data = $this->data();
        }
        $jbimage    = $this->app->jbimage;
        $relatedImg = $this->_getRelatedImageParams($params);

        foreach ($data['variations'] as $key => $variant) {
            $variantParams = $this->app->data->create($variant['params']);
            $image         = $variantParams->get('_image');

            if (!empty($image)) {
                $file       = $jbimage->resize($image, $relatedImg->get('width'), $relatedImg->get('height'));
                $file_popup = $jbimage->resize($image, $relatedImg->get('width_popup'), $relatedImg->get('height_popup'));

                if (isset($prices[$variant['hash']])) {
                    $prices[$variant['hash']]['image']      = $file->url;
                    $prices[$variant['hash']]['file_popup'] = $file_popup->url;
                }

            }
        }

        reset($prices);
        if (!empty($data['basic']['params']['_image'])) {
            $file       = $jbimage->resize($data['basic']['params']['_image'], $relatedImg->get('width'), $relatedImg->get('height'));
            $file_popup = $jbimage->resize($data['basic']['params']['_image'], $relatedImg->get('width_popup'), $relatedImg->get('height_popup'));
            $url        = $file->url;
            $url_popup  = $file_popup->url;

            $prices[key($prices)]['image']      = $url;
            $prices[key($prices)]['file_popup'] = $url_popup;
        }

        return $prices;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function _getRelatedImageParams($params = array())
    {
        $item = $this->getItem();

        $relatedElParams = json_decode($params->get('relatedimage', ''), true);
        $relatedElParams = $this->app->data->create($relatedElParams);
        $relatedImageElm = $item->getElement($relatedElParams->get('element'));

        $relatedParams = array();
        if ($relatedImageElm) {
            $relatedParams = array(
                'unique'       => $relatedImageElm->unique,
                'width'        => $relatedElParams->get('width', 300),
                'height'       => $relatedElParams->get('height', 300),
                'width_popup'  => $relatedElParams->get('width_popup', 800),
                'height_popup' => $relatedElParams->get('height_popup', 1000),
                'popup'        => $relatedElParams->get('template') == 'popup' ? 1 : 0
            );
        }

        return $this->app->data->create($relatedParams);
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
     * @param  bool  $no     return number of variant
     *
     * @return array
     */
    public function getVariantByValues($values = array(), $no = false)
    {
        $data = $this->_getValues();

        if (empty($values) || empty($data)) {
            return $values;
        }

        $variations = $this->getVariations();

        foreach ($data as $i => $value) {
            foreach ($values as $identifier => $fields) {
                $valError = false;
                $idError  = false;

                if (!isset($value[$identifier]) ||
                    count($values) !== count($value)
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
        if (!empty($variant) && $no === true) {
            $variant['no'] = key($variations);
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

        $val      = 0;
        $value    = $basicData['_value'];
        $currency = $basicData['_currency'];
        if (!empty($result)) {
            foreach ($result as $variant) {

                $variantValue    = $variant['_value'];
                $variantCurrency = $variant['_currency'];

                $newVal = $this->_jbmoney->calcDiscount($value, $currency, $variantValue, $variantCurrency);

                if ($newVal > $value) {
                    $val = $newVal - $value;
                }

                $basicData['_value'] += $val;
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
     * @param array $variant
     *
     * @return array|bool
     */
    public function getPricesByVariant($variant = array())
    {
        if (empty($variant)) {
            return false;
        }

        $currencyList  = $this->getCurrencyList();
        $variant       = $this->app->data->create($variant);
        $variantParams = $this->app->data->create($variant->get('params'));

        $default = $this->_getDefaultCurrency();

        $calc   = $this->calcVariant($variant);
        $result = array(
            'sku'         => $variantParams->get('_sku'),
            'balance'     => $variantParams->get('_balance', -1),
            'image'       => $variantParams->get('_image', ''),
            'description' => $variantParams->get('_description', '')
        );

        foreach ($currencyList as $currency) {

            $priceNoFormat = $this->_jbmoney->convert($default, $currency, $calc['price']);
            $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

            $totalNoFormat = $this->_jbmoney->convert($default, $currency, $calc['total']);
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
        $weight  = $basic->get('_weight', 1);

        $basicCurrency = $basic->get('_currency');
        $basicValue    = $basic->get('_value');

        $bDiscount = $basic->get('_discount');
        $variant   = $this->app->data->create($variant);
        $params    = $this->app->data->create($variant->get('params'));
        $discount  = $params->get('_discount');

        $variantValue    = $variant->get('_value');
        $variantCurrency = $variant->get('_currency');
        $discountVal     = $discount['value'];
        $discountCur     = $discount['currency'];

        $properties = $params->get('properties');
        $properties = $this->app->data->create($properties);

        $price = $this->_jbmoney->calc($basicValue, $basicCurrency, $variantValue, $variantCurrency);
        $price = $this->_jbmoney->convert($basicCurrency, $default, $price);
        if (true) {
            //$value = $this->_jbmoney->calcDiscount($value, $basicCurrency, $bDiscount['value'], $bDiscount['currency']);
        }
        $total = $this->_jbmoney->calcDiscount($price, $variantCurrency, $discountVal, $discountCur);
        $total = $this->_jbmoney->convert($variantCurrency, $currency, $total);

        $result = array(
            'total'       => $total,
            'price'       => $price,
            'item_id'     => $this->getItem()->id,
            'sku'         => $basic->get('_sku'),
            'name'        => $this->getItem()->name,
            'currency'    => $variantCurrency,
            'image'       => '',
            'params'      =>
                array(
                    'width'  => $properties->get('width', 1),
                    'height' => $properties->get('height', 1),
                    'length' => $properties->get('length', 1),
                    'weight' => $weight['value']
                ),
            'priceParams' => array()
        );

        return $result;
    }

    /**
     * Get total price for basic data
     *
     * @param string $currency
     *
     * @return mixed
     */
    public function basicCalc($currency = self::DEFAULT_CURRENCY)
    {
        $basic  = $this->getBasicReadableData();
        $weight = $basic->get('_weight', 1);

        $discount    = $basic->get('_discount');
        $discountVal = $discount['value'];
        $discountCur = $discount['currency'];

        $price     = $this->_jbmoney->convert($basic->get('_currency'), $currency, $basic->get('_value'));
        $basePrice = $this->_jbmoney->calcDiscount($basic->get('_value'), $basic->get('_currency'), $discountVal, $discountCur);
        $total     = $this->_jbmoney->convert($basic->get('_currency'), $currency, $basePrice);

        $properties = $basic->get('properties');
        $properties = $this->app->data->create($properties);

        $result = array(
            'total'       => $total,
            'price'       => $price,
            'item_id'     => $this->getItem()->id,
            'sku'         => $basic->get('_sku'),
            'name'        => $this->getItem()->name,
            'currency'    => $basic->get('_currency', 'EUR'),
            'image'       => '',
            'params'      =>
                array(
                    'width'  => $properties->get('width', 1),
                    'height' => $properties->get('height', 1),
                    'length' => $properties->get('length', 1),
                    'weight' => $weight['value']
                ),
            'priceParams' => array()
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
        $basic  = $this->getBasicData();
        $params = $basic['params'];
        unset($basic['params']);
        unset($basic['default_variant']);

        $result = array_merge($basic, $params);

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
     * @param        $name
     * @param        $value
     * @param  array $attrs
     *
     * @return mixed
     */
    protected function _renderRow($name, $value, $attrs = array())
    {
        $attribes = array_merge(
            array(
                'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
                'title'       => JText::_('JBZOO_JBPRICE_VARIATION' . JString::strtoupper($name)),
                'class'       => 'row-' . $name . ' hasTip',
                'id'          => $this->app->jbstring->getId() . '-' . $name,
                'class'       => 'basic-sku',
            ), $attrs
        );


        return $this->app->html->_('control.text', $this->getParamName($name), $value, $this->app->jbhtml->buildAttrs($attribes));
    }

    /**
     * @param  array $data
     *
     * @return JBCartElement|null
     */
    public function loadElement($data = array())
    {
        $identifier = $data;
        if (is_array($data)) {
            $identifier = $data['identifier'];
            $type       = $data['type'];
            $group      = $data['group'];
        }

        if (!$element = isset($this->_elements[$identifier]) ? $this->_elements[$identifier] : null) {
            if ($config = $this->_getElementConfig($identifier)) {

                if ($element = $this->_jbcartelement->create($config->get('type'), $config->get('group'))) {

                    $element->identifier = $identifier;
                    $element->config     = $config;

                    $this->_elements[$identifier] = $element;
                } else {
                    return false;
                }
            } else {

                if (strpos($identifier, '_') === 0 &&
                    isset($type) &&
                    isset($group) &&
                    $element = $this->_jbcartelement->create($type, $group)
                ) {

                    $element->identifier = $identifier;
                    $element->config     = $this->app->data->create($data);

                    $this->_elements[$identifier] = $element;
                } else {
                    return false;
                }
            }
        }

        $element = clone($element);
        $element->setJBPrice($this);

        return $element;
    }

    /**
     * @param $identifier
     *
     * @return null
     */
    protected function _getElementConfig($identifier)
    {
        $groupConfig = $this->_config->getGroup('cart.' . JBCart::CONFIG_PRICE);
        $config      = $groupConfig->get($this->identifier . '.list');

        if (isset($config[$identifier])) {
            $this->elementsConfig[$identifier] = $config[$identifier];
            return $this->app->data->create($this->elementsConfig[$identifier]);
        }

        return null;
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

        return $data['basic'];
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

                $basic = JString::trim($basic);
                if (empty($basic)) {
                    $basic = 0;
                }
                $result['basic'][$key] = $basic;
            }
        }

        foreach ($basicData['params'] as $key => $basic) {
            $result['basic']['params'][$key] = $basic;
        }

        if (isset($data['variations'])) {
            $variations = $data['variations'];

            for ($i = 0; $i < count($variations); $i++) {

                $result['variations'][$i]['_value']    = JString::trim($variations[$i]['_value']);
                $result['variations'][$i]['_currency'] = JString::trim($variations[$i]['_currency']);

                foreach ($variations[$i]['params'] as $key => $variant) {

                    if (strlen($key) == 36) {

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
            $price->ajaxAddToCart(1, array(), false);
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
    public function ajaxAddToCart($quantity = 1, $values = array(), $sendAjax = true)
    {
        $jbAjax  = $this->app->jbajax;
        $cart    = JBCart::getInstance();
        $variant = $this->getVariantByValues($values, true);
        $params  = array(
            'key' => $this->getKey($variant)
        );

        if (empty($variant)) {
            $item = $this->basicCalc();
        } else {
            $item = $this->calcVariant($variant);
        }

        $item['priceParams'] = $this->getValueName($values);
        $item['quantity']    = $quantity;
        $cart->addItem($item, $params);

        $this->app->jbajax->send(array());
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

            $this->app->jbajax->send(array(), false);
        }

        $this->app->jbajax->send(array(), false);
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
     * Get key
     *
     * @param int   $no - index of variant
     * @param array $variant
     *
     * @return string
     */
    public function getKey($variant = array(), $no = null)
    {
        $key = $this->getItem()->id;
        $no  = null;

        if (!empty($variant)) {
            $no = (isset($variant['no']) || isset($variant['no']) && $variant['no'] === 0 ? '-' . $variant['no'] : '');
        }

        return $key . $no;
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
     * @param  array $identifiers
     *
     * @return array
     */
    public function getValueName($identifiers = array())
    {
        $string = $this->app->jbstring;
        $result = array();
        if (!empty($identifiers)) {
            foreach ($identifiers as $id => $value) {
                if ($element = $this->loadElement($id)) {

                    if (isset($value['value']) && empty($value['value'])) {
                        continue;
                    }

                    $name = ucfirst($string->clean($element->getName()));

                    $result[$name] = implode($value);
                }
            }
        }

        return $result;
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
        return array(
            '_value'    => 0,
            '_currency' => $this->_getDefaultCurrency(),
            'params'    => array(
                '_sku'         => $this->_getDefaultSku(),
                '_new'         => 0,
                '_balance'     => -1,
                '_discount'    => array(
                    'value'    => 0,
                    'currency' => $this->_getDefaultCurrency()
                ),
                '_description' => '',
                '_image'       => '',
            ),
        );
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

}
