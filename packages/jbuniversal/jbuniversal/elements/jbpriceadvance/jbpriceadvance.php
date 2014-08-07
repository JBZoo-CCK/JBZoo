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

    const CONFIG_GROUP  = 'cart.priceparams';
    const RENDER_GROUP  = 'cart.jbpricetmpl';
    const BASIC_GROUP   = 'basic';
    const VARIANT_GROUP = 'variations';

    const PRICE_MODE_DEFAULT = 1;
    const PRICE_MODE_OVERLAY = 2;

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
     * @var
     */
    public $elementsConfig;

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

        $groupConfig = $this->_config->getGroup(self::CONFIG_GROUP);

        $this->elementsConfig = $groupConfig->get('list');
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $params = $this->app->data->create($params);

        if (!(int)$params->get('show_empty_price', 1)) {

            $basic = $this->getBasicData();

            if (empty($basic['value']) || $basic['value'] == 0) {
                return false;
            }
        }

        if (!(int)$params->get('show_empty_balance', 1)) {
            $basic = $this->getBasicData();

            if ((int)$basic['balance'] == 0) {
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
     * @return null|string
     */
    public function edit()
    {
        $this->app->jbassets->admin();
        $basicData = $this->getBasicData();

        $basicData['basic'] = 1;

        if ($layout = $this->getLayout('edit.php')) {
            $variationsTpl = $this->getLayout('_variations.php');
            $variations    = $this->_getVariations();

            if (empty($variations) && (int)$this->config->get('mode', 0)) {
                $basic      = $this->getBasicData();
                $variations = array($basic);

                $basic['description'] = '';
            }

            $params = array(
                'config'       => $this->config,
                'currencyList' => $this->getCurrencyList($this->config),
                'variations'   => $variations,
                'basicData'    => $this->getBasicData()
            );

            $variationsHTML = self::renderLayout($variationsTpl, $params);

            $renderer = $this->app->jbrenderer->create('jbprice');

            $basic = $renderer->render('_edit',
                array(
                    'price' => $this,
                    'style' => self::BASIC_GROUP,
                    'data'  => $basicData
                )
            );

            $params['variationsTmpl'] = $variationsHTML;

            $params['basic'] = $basic;

            return self::renderLayout($layout, $params);
        }

        return null;
    }

    public function getAllParamData($identifier)
    {
        $result      = array();
        $data        = $this->data();
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
     * @param $name
     * @param bool $array
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        return "elements[{$this->identifier}][basic][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param $name
     * @return string
     */
    public function getControlParamName($name)
    {
        return "elements[{$this->identifier}][basic][params][{$name}]";
    }

    /**
     * Get name for prices variants
     * @param $name
     * @param int $index
     * @return string
     */
    public function getRowControlName($name, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][{$name}]";
    }

    /**
     * @param $name
     * @param int $key
     * @param int $index
     * @return string
     */
    public function getParamName($name, $key = 0, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][params][{$name}]";
    }

    /**
     * Render submission
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit($params);
    }

    /**
     * Render New partial
     * @param $params
     * @return null|string
     */
    public function _renderNew($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('new_show', 1) && $layout = $this->getLayout('_new.php')) {

            $basic = $this->getBasicData();

            return JString::trim(self::renderLayout($layout, array(
                'isNew' => $basic['new'],
            )));
        }

        return null;
    }

    /**
     * Render Hit partial
     * @param $params
     * @return null|string
     */
    public function _renderHit($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('hit_show', 1) && $layout = $this->getLayout('_hit.php')) {

            $basic = $this->getBasicData();

            return JString::trim(self::renderLayout($layout, array(
                'isHit' => $basic['hit'],
            )));
        }

        return null;
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
            if (empty($basic['value']) || $basic['value'] == 0) {
                throw new AppValidatorException('This field is required');
            }
        }

        return $value;
    }

    public function getArray()
    {
        $a = array(
            '8b36e9e0-8efa-458f-9571-cc2c4276b53a' => array(
                '0' => 'белый1'
            ),
            '97377ede-c0a5-4bec-a3eb-8fdb231a137a' => array(
                'value' => 'option2'
            )
        );

        return $a;
    }

    /**
     * Render for front-end
     * @param array $params
     * @return string|void
     */
    public function render($params = array())
    {
        $params    = $this->app->data->create($params);
        $item      = $this->getItem();
        $basicData = $this->getBasicData();
        $variant   = isset($basicData['default_variant']) ? $basicData['default_variant'] : null;
        $prices    = $this->getPricesByVariant($basicData);

        if (isset($variant)) {
            $dataVariant = $this->_getVariations($variant);
            $prices      = $this->getPricesByVariant($dataVariant);
        }
        $this->getIndexDataParameters();
        $var = $this->getVariantByValuesOverlay($this->getArray());

        //$pr = $this->getPricesByVariant($var);

        $this->_layout = $params->get('template', 'default');

        $renderer = $this->app->renderer->create('jbprice')->addPath(
            $this->app->path->path('component.site:'),
            $this->app->path->path('jbtmpl:catalog')
        );

        $elements = $renderer->render($this->_layout, array('price' => $this));

        if ($layout = $this->getLayout('render.php')) {
            return self::renderLayout($layout, array(
                'elements'          => $elements,
                'prices'            => $prices,
                'isInCart'          => (int)$this->app->jbcart->isExists($item),
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
     * Render default (complex) layout
     * @param $params
     * @return string
     */
    protected function _renderTmplDefault($params)
    {
        $layout = $this->getLayout('tmpl_default.php');
        $prices = $this->_getTmplPrices($params);
        $item   = $this->getItem();

        $mainPrices = $this->_getPrices($params, $this->_getHash());

        $relatedImgParams = $this->_getRelatedImageParams($params);

        return self::renderLayout($layout, array(
            'skuTmpl'           => $this->_renderSku($params),
            'balanceTmpl'       => $this->_renderBalance($params),
            'countTmpl'         => $this->_renderCount($params),
            'pricesTmpl'        => $this->_renderPrices($params, $prices),
            'buttonsTmpl'       => $this->_renderButtons($params),
            'relatedImage'      => $relatedImgParams->get('unique'),
            'popup'             => $relatedImgParams->get('popup', 0),
            'prices'            => $mainPrices,
            'isInCart'          => (int)$this->app->jbcart->isExists($item),
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
                'currencyDefault' => $params->get('currency_default', 'EUR'),
                'startValue'      => (int)$params->get('count_default', 1),
                'multipleValue'   => (int)$params->get('count_multiple', 1),
                'advFieldText'    => (int)$this->config->get('adv_field_text', 0),
                'advAllExistShow' => (int)$this->config->get('adv_all_exists_show', 1),
            ),
        ));
    }

    /**
     * Render "only SKU" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlySku($params)
    {
        $layout = $this->getLayout('tmpl_only_sku.php');
        return self::renderLayout($layout, array(
            'basic' => $this->getBasicData(),
        ));
    }

    /**
     * Render "only buttons" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlyButtons($params)
    {
        $layout = $this->getLayout('tmpl_only_buttons.php');
        $item   = $this->getItem();
        $params = $this->_overloadParamsByPrefix($params, 'only_');

        if ($params->get('button_mode', 'normal') == 'modal') {
            $this->app->jbassets->fancybox();
        }

        return self::renderLayout($layout, array(
            'buttonsTmpl'       => $this->_renderButtons($params),
            'isInCart'          => (int)$this->app->jbcart->isExists($item),
            'basketUrl'         => $this->_getBasketUrl(),
            'addToCartUrl'      => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxAddToCart'),
            'removeFromCartUrl' => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxRemoveFromCart'),
            'modalUrl'          => $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxModalWindow', array(
                    'elem_layout'   => $params->get('_layout'),
                    'elem_position' => $params->get('_position'),
                    'elem_index'    => $params->get('_index'),
                )),
        ));
    }

    /**
     * Redner "only SKU" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlyBalance($params)
    {
        $layout = $this->getLayout('tmpl_only_balance.php');

        return self::renderLayout($layout, array(
            'basic'  => $this->getBasicData(),
            'params' => $params,
        ));
    }

    /**
     * Render "only SKU" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlySale($params)
    {
        $layout = $this->getLayout('tmpl_only_sale.php');

        $params->set('sale_show', $params->get('only_sale_mode', self::SALE_VIEW_ICON_VALUE));
        return self::renderLayout($layout, array(
            'saleTmpl' => $this->_renderSale($params),
        ));
    }

    /**
     * Render "only SKU" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlyNew($params)
    {
        $layout = $this->getLayout('tmpl_only_new.php');

        $params->set('new_show', 1);
        return self::renderLayout($layout, array(
            'newTmpl' => $this->_renderNew($params),
        ));
    }

    /**
     * Render "only Hit" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlyHit($params)
    {
        $layout = $this->getLayout('tmpl_only_hit.php');

        $params->set('hit_show', 1);
        return self::renderLayout($layout, array(
            'hitTmpl' => $this->_renderHit($params),
        ));
    }

    /**
     * Render "Modal window" template
     * @param $params
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
     * Render "only price" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlyPrice($params)
    {
        $layout          = $this->getLayout('tmpl_only_price.php');
        $prices          = $this->_getTmplPrices($params);
        $currencyDefault = $params->get('currency_default', 'EUR');

        $mainHash = $this->_getHash();
        $prices   = array($mainHash => $prices[$mainHash]);
        $basic    = $this->getBasicData();

        return self::renderLayout($layout, array(
            'params'   => $params,
            'basic'    => $basic,
            'base'     => array(
                'price' => $prices[$mainHash]['prices'][$currencyDefault]['price'],
                'total' => $prices[$mainHash]['prices'][$currencyDefault]['total'],
                'save'  => $prices[$mainHash]['prices'][$currencyDefault]['save'],
            ),
            'discount' => array(
                'value'  => (float)$basic['discount'],
                'format' => $this->_jbmoney->toFormat($basic['discount'], $basic['discount_currency']),
            )
        ));
    }

    /**
     * Render balance partial
     * @param array $params
     * @return null|string
     */
    protected function _renderBalance($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('balance_show', 1) && $layout = $this->getLayout('_balance.php')) {
            return self::renderLayout($layout, array(
                'basic'      => $this->getBasicData(),
                'variations' => $this->_getVariations(),
                'config'     => $this->config,
                'params'     => $params,
            ));
        }

        return null;
    }

    /**
     * Render count partial
     * @param array $params
     * @return null|string
     */
    protected function _renderCount($params)
    {
        $params = $this->app->data->create($params);

        if ($layout = $this->getLayout('_count.php')) {
            return self::renderLayout($layout, array(
                'isEnabled' => (int)$params->get('count_show', 1),
            ));
        }

        return null;
    }

    /**
     * Render buttons partial
     * @param array $params
     * @return null|string
     */
    protected function _renderButtons($params)
    {
        $params = $this->app->data->create($params);

        if ($layout = $this->getLayout('_buttons.php')) {
            $item = $this->getItem();

            return self::renderLayout($layout, array(
                'mode' => $params->get('button_mode', 'normal'),
            ));
        }

        return null;
    }

    /**
     * Get prices for template
     * @param $params
     * @return array
     */
    protected function _getTmplPrices($params)
    {
        static $prices;

        if (!isset($result)) {
            $params = $this->app->data->create($params);

            $indexData    = $this->getIndexData();
            $currencyList = $this->getCurrencyList();

            $prices = array();
            foreach ($indexData as $key => $data) {
                $hash = $data['hash'];

                $prices[$hash] = array(
                    'balance'     => $data['balance'],
                    'description' => $data['description'],
                    'image'       => isset($data['image']) ? $data['image'] : '',
                    'prices'      => array()
                );

                foreach ($currencyList as $currency) {

                    $priceNoFormat = $this->_jbmoney->convert($data['currency'], $currency, $data['price']);
                    $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

                    $totalNoFormat = $this->_jbmoney->convert($data['currency'], $currency, $data['total']);
                    $total         = $this->_jbmoney->toFormat($totalNoFormat, $currency);

                    $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
                    $save         = $this->_jbmoney->toFormat($saveNoFormat, $currency);

                    $prices[$hash]['prices'][$currency] = array(
                        'totalNoFormat' => $totalNoFormat,
                        'priceNoFormat' => $priceNoFormat,
                        'saveNoFormat'  => $saveNoFormat,
                        'total'         => $total,
                        'price'         => $price,
                        'save'          => $save,
                    );
                }
            }
        }

        return $this->_prepareData($prices, $params);
    }

    /**
     * Render prices partial
     * @param array $params
     * @param array $prices
     * @return null|string
     */
    protected function _renderPrices($params, $prices)
    {
        $params          = $this->app->data->create($params);
        $basic           = $this->getBasicData();
        $currencyDefault = $params->get('currency_default', 'EUR');
        $currencyList    = $this->getCurrencyList($params);

        if ($layout = $this->getLayout('_prices.php')) {
            return self::renderLayout($layout, array(
                'saleTmpl'     => $this->_renderSale($params),
                'newTmpl'      => $this->_renderNew($params),
                'hitTmpl'      => $this->_renderHit($params),
                'config'       => $this->config,
                'params'       => $params,
                'currencyList' => $currencyList,
                //'selects'      => $this->_renderParamsControl($params),
                'prices'       => $prices,
                'base'         => array(
                    'price' => $prices[$this->_getHash()]['prices'][$currencyDefault]['price'],
                    'total' => $prices[$this->_getHash()]['prices'][$currencyDefault]['total'],
                    'save'  => $prices[$this->_getHash()]['prices'][$currencyDefault]['save'],
                ),
                'discount'     => array(
                    'value'  => (float)$basic['discount'],
                    'format' => $this->_jbmoney->toFormat($basic['discount'], $basic['discount_currency']),
                )
            ));
        }

        return null;
    }

    /**
     * Overload params by prefix in key
     * TODO Remove this hack
     * @param JSONData|array $params
     * @param string $prefix
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
     * @param $prices
     * @param array $data
     * @param $params
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
     * @param array $params
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
     * Render params controls
     * @param $params
     * @return array
     */
    protected function _renderParamsControl($params)
    {
        $jbhtml = $this->app->jbhtml;

        $paramsTmpl   = (int)$params->get('params_tmpl', 0);
        $advFieldText = (int)$this->config->get('adv_field_text', 0);
        $advShowEmpty = (int)$this->config->get('adv_show_empty', 1);

        $html = array();

        for ($i = 1; $i <= 3; $i++) {

            if ($paramsList = $this->_getParamOptions($i)) {

                $default   = $this->config->get('adv_field_param_' . $i . '_default', '');
                $default   = $this->app->string->sluggify($default);
                $paramName = JString::trim($paramsList[''], '-');
                reset($paramsList);

                if (!$advShowEmpty) {
                    unset($paramsList['']);

                    if (empty($default)) {
                        $default = key($paramsList);
                    }
                }

                // validate default var
                if (!isset($paramsList[$default])) {
                    $default = key($paramsList);
                }

                if (self::PARAMS_TMPL_SELECT == $paramsTmpl) {
                    $html[$i] = $jbhtml->select($paramsList, uniqid('jbprice-'), 'class="jsParam" data-index="p' . $i . '-"', $default);
                    $html[$i] = '<div class="jbprice-param-select jbprice-param-list jbprice-param-list-' . $i . '">' . $html[$i] . '</div>';

                } else if (self::PARAMS_TMPL_RADIO == $paramsTmpl) {

                    $html[$i] = $jbhtml->radio($paramsList, uniqid('jbprice-'), '', $default);
                    $html[$i] = '<fieldset class="jbprice-param-radio jbprice-param-list jbprice-param-' . $i . '" data-index="p' . $i . '-">'
                        . '<legend>' . $paramName . '</legend>'
                        . $html[$i]
                        . '</fieldset>';
                }

            } else {
                $html[$i] = $jbhtml->hidden('', '', 'class="jsParam" data-index="p' . $i . '-"');
            }
        }

        $paramsList = $this->_getParamOptionsDesc();

        if ($advFieldText == self::TEXT_FIELD_AS_PARAM && !empty($paramsList)) {

            if (self::PARAMS_TMPL_SELECT == $paramsTmpl) {
                $html[4] = $jbhtml->select($paramsList, uniqid('jbprice-'), 'class="jsParamDesc" data-index="d-"');
                $html[4] = '<div class="jbprice-param-select jbprice-param-list jbprice-param-list-desc">' . $html[4] . '</div>';

            } else if (self::PARAMS_TMPL_RADIO == $paramsTmpl) {
                $paramName = JString::trim($paramsList[''], '-');
                if (!$advShowEmpty) {
                    unset($paramsList['']);
                }

                reset($paramsList);
                $value = key($paramsList);

                $html[4] = $jbhtml->radio($paramsList, uniqid('jbprice-'), '', $value);
                $html[4] = '<fieldset class="jbprice-param-radio jbprice-param-list jbprice-param-list-desc" data-index="d-">'
                    . '<legend>' . $paramName . '</legend>'
                    . $html[4]
                    . '</fieldset>';
            }

        } else if ($advFieldText == self::TEXT_FIELD_AS_PARAM && empty($paramsList)) {
            $html[4] = $jbhtml->hidden('', '', 'class="jsParamDesc" data-index="d-"');
        }

        return implode("\n ", $html);
    }

    /**
     * @param $params
     * @param  array $values
     * @return array|mixed
     */
    protected function _getPrices($params, $values = array())
    {
        $indexData    = $this->getIndexData();
        $currencyList = $this->getCurrencyList();

        $prices = array();

        $i = 0;
        if (!empty($indexData)) {
            foreach ($indexData as $data) {

                $prices[$i] = array(
                    'balance'     => $data['balance'],
                    'sku'         => isset($data['sku']) ? $data['sku'] : '',
                    'description' => $data['description'],
                    'image'       => isset($data['image']) ? $data['image'] : '',
                );

                foreach ($currencyList as $currency) {

                    $priceNoFormat = $this->_jbmoney->convert($data['currency'], $currency, $data['price']);
                    $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

                    $totalNoFormat = $this->_jbmoney->convert($data['currency'], $currency, $data['total']);
                    $total         = $this->_jbmoney->toFormat($totalNoFormat, $currency);

                    $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
                    $save         = $this->_jbmoney->toFormat($saveNoFormat, $currency);

                    $prices[$i][$currency] = array(
                        'totalNoFormat' => $totalNoFormat,
                        'priceNoFormat' => $priceNoFormat,
                        'saveNoFormat'  => $saveNoFormat,
                        'total'         => $total,
                        'price'         => $price,
                        'save'          => $save,
                    );
                }

                $i++;
            }

        }
        if (!empty($prices)) {
            //$prices = $this->_prepareData($prices, $params);
        }

        return $prices;
    }

    public function getVariantByValues($values = array())
    {
        $data = $this->_getValues();

        if (empty($values) || empty($data)) {
            return $values;
        }

        $variations = $this->_getVariations();

        foreach ($data as $i => $value) {
            foreach ($value as $identifier => $fields) {

                $valError = false;
                $idError  = false;

                if (!isset($values[$identifier])) {
                    $idError = true;
                }

                if ($idError === false) {
                    $diff = array_diff_assoc($fields, $values[$identifier]);

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
        return !empty($variations) ? $variations[key($variations)] : array();
    }

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
                        $result[$i] = $this->_getVariations($i);
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

    public function getPricesByVariant($variant = array())
    {
        if (empty($variant)) {
            return false;
        }

        $currencyList  = $this->getCurrencyList();
        $variant       = $this->app->data->create($variant);
        $variantParams = $this->app->data->create($variant->get('params'));

        $discount     = $variantParams->get('_discount');
        $discountVal  = $discount['value'];
        $discountCurr = $discount['currency'];

        $currencyDefault = $this->_getDefaultCurrency();

        $basic = $this->getBasicData();
        $basic = $this->app->data->create($basic);

        $value      = $this->_jbmoney->calc($basic->get('_value'), $basic->get('_currency'), $variant->get('_value'), $variant->get('_currency'));
        $basicPrice = $this->_jbmoney->convert($basic->get('_currency'), $currencyDefault, $value);
        $basicTotal = $this->_jbmoney->calcDiscount($value, $basic->get('_currency'), $discountVal, $discountCurr);
        $basicTotal = $this->_jbmoney->convert($basic->get('_currency'), $currencyDefault, $basicTotal);

        $result = array(
            'sku'         => $variantParams->get('_sku'),
            'balance'     => $variantParams->get('_balance', -1),
            'image'       => $variantParams->get('_image', ''),
            'description' => $variantParams->get('_description', '')
        );

        foreach ($currencyList as $currency) {

            $priceNoFormat = $this->_jbmoney->convert($currencyDefault, $currency, $basicPrice);
            $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

            $totalNoFormat = $this->_jbmoney->convert($currencyDefault, $currency, $basicTotal);
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

    public function getBasicPrices()
    {
        $basic       = $this->getBasicData();
        $basic       = $this->app->data->create($basic);
        $basicParams = $this->app->data->create($basic->get('params'));

        $currencyList    = $this->getCurrencyList();
        $currencyDefault = $this->_getDefaultCurrency();
        $basicCurrency   = $basic->get('_currency', $currencyDefault);

        $discount = $basicParams->get('_discount');

        $discountCurrency = isset($discount['currency']) ? $discount['currency'] : $currencyDefault;
        $discountValue    = isset($discount['value']) ? $discount['value'] : 0;

        $value = $basic->get('_value');

        $prices = array(
            'sku'         => $basicParams->get('_sku', $this->_item->id),
            'image'       => $basicParams->get('_image', ''),
            'description' => $basicParams->get('_description', ''),
            'balance'     => $basicParams->get('_balance', -1),
        );

        $basicPrice = $this->_jbmoney->convert($basicCurrency, $currencyDefault, $value);
        $basePrice  = $this->_jbmoney->calcDiscount($value, $basicCurrency, $discountValue, $discountCurrency);
        $basicTotal = $this->_jbmoney->convert($basicCurrency, $currencyDefault, $basePrice);

        foreach ($currencyList as $currency) {

            $priceNoFormat = $this->_jbmoney->convert($currencyDefault, $currency, $basicPrice);
            $price         = $this->_jbmoney->toFormat($priceNoFormat, $currency);

            $totalNoFormat = $this->_jbmoney->convert($currencyDefault, $currency, $basicTotal);
            $total         = $this->_jbmoney->toFormat($totalNoFormat, $currency);

            $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
            $save         = $this->_jbmoney->toFormat($saveNoFormat, $currency);

            $prices[$currency] = array(
                'totalNoFormat' => $totalNoFormat,
                'priceNoFormat' => $priceNoFormat,
                'saveNoFormat'  => $saveNoFormat,
                'total'         => $total,
                'price'         => $price,
                'save'          => $save
            );
        }

        return $prices;
    }

    protected function _getValues()
    {
        $data = $this->data();

        return isset($data['values']) ? $data['values'] : array();
    }

    /**
     * @param $name
     * @param $value
     * @param  array $attrs
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
     * @param int $basic
     * @param $selected
     * @return string
     */
    protected function _renderFields($basic = 0, $selected = array())
    {
        $basic  = (int)$basic;
        $result = array();

        if ($basic) {
            $basicData = $this->getBasicData();

            $basicData['basic'] = $basic;
            return $this->_variations($basicData);
        }
        $selected['basic'] = $basic;

        $result[] = $this->_variations($selected);


        return implode($result);
    }

    /**
     * @param  array $data
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

    protected function _getElementConfig($identifier)
    {
        if (isset($this->elementsConfig[$identifier])) {
            return $this->app->data->create($this->elementsConfig[$identifier]);
        }

        return null;
    }

    protected function _setElementConfig($identifier)
    {
    }

    protected function _getFields($selected, $function = 'edit')
    {
        $fields = $this->_position->loadForPrice($this);
        $result = array();

        foreach ($fields as $field) {
            if (is_object($field)) {
                $field->setJBPrice($this);
                $this->_bindElements($field, $selected);

                $result[] = $field->$function();
            }
        }

        return implode("\n", $result);
    }

    protected function _variations($data = array())
    {
        $result = array();

        $basic  = (int)$data['basic'];
        $fields = $this->app->jbcartposition->loadForPrice($this);

        foreach ($fields as $field) {

            if (is_object($field)) {

                if ($basic && $field->getMetaData('core') != 'true') {
                    continue;
                }

                $this->_bindElements($field, array_merge(
                    array(
                        'config'             => $this->config,
                        'related_identifier' => $this->identifier,
                        'basicData'          => $this->getBasicData(),
                    )), $data);

                $this->_bindElements($field, $data);
                $field->setJBPrice($this);
                $result[] = $field->edit();
            }
        }

        return implode($result);
    }

    /**
     * Render variations for edit()
     * @param  array $selected
     * @param  int $variant - variant index
     * @param  boolean $core
     * @return null|string
     */
    protected function _renderEditFields($selected = array(), $variant, $core = false)
    {
        $result = array();

        $fields = $this->app->jbcartposition->loadForPrice($this);

        if (!empty($fields)) {
            $i = 0;
            foreach ($fields as $field) {

                if ($core && $field->getMetaData('core') != 'true') {
                    continue;
                }

                $select = null;

                if (!empty($selected[$i]['value']) &&
                    $selected[$i]['key'] == $field->identifier
                ) {
                    $select = $selected[$i]['value'];
                }

                $this->bindConfigElements($field, $i, $variant, $select);
                $result[] = $field->edit();

                $i++;
            }

            return implode($result);
        }

        return null;
    }

    protected function _bindElements($element, $data = array())
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        foreach ($data as $key => $value) {
            $element->config->set($key, $value);
        }

        return $this;
    }

    protected function bindConfigElements($element, $key = 0, $variant = 0, $selected = array(), $basic = false, $basicData = array())
    {
        $element->config->set('related_identifier', $this->identifier);
        $element->config->set('key', $key);
        $element->config->set('variant', $variant);
        $element->config->set('selected', $selected);
        $element->config->set('config', $this->config);
        $element->config->set('basic', (int)$basic);
        $element->config->set('basicData', $basicData);
    }

    /**
     * Get variation list
     * @param  int $variant
     * @return array
     */
    protected function _getVariations($variant = null)
    {
        $result = array();

        if (!(int)$this->config->get('mode', 0)) {
            return $result;
        }

        $data        = $this->data();
        $defaultData = $this->_getDefaultData();

        if (isset($variant) && isset($data['variations'][$variant])) {
            return array_merge($defaultData, $data['variations'][$variant]);
        }

        if (isset($data['variations'])) {

            foreach ($data['variations'] as $variant) {
                $result[] = array_merge($defaultData, $variant);
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
     * Get param option list
     * @param int $index
     * @param bool $edit
     * @return array
     */
    protected function _getParamOptions($index, $edit = false)
    {
        $result     = array();
        $variations = $this->_getVariations();
        if ($elementId = $this->config->get('adv_field_param_' . $index)) {

            $file = $this->app->path->path('jbtypes:' . $this->getItem()->type . '.config');
            if ($file && $json = $this->app->jbfile->read($file)) {
                $data = json_decode($json, true);
            }

            if (isset($data['elements'][$elementId]['option'])) {
                $result[''] = '- ' . JText::_($data['elements'][$elementId]['name']) . ' -';
                foreach ($data['elements'][$elementId]['option'] as $option) {
                    if ((int)$this->config->get('adv_all_exists_show', 1) || $edit) {
                        $result[$option['value']] = $option['name'];
                    } else {
                        foreach ($variations as $variation) {
                            if (in_array($option['value'], $variation, true)) {
                                $result[$option['value']] = $option['name'];
                            }
                        }
                    }
                }
            }
        }

        $result = count($result) > 1 ? $result : array();
        return $result;
    }

    /**
     * Get option list from all descriptions
     * @return array
     */
    protected function _getParamOptionsDesc()
    {
        $variants = $this->_getVariations();

        $result = array();

        if (!empty($variants)) {
            foreach ($variants as $variant) {
                $key = $this->app->string->sluggify($variant['description']);
                if ($key && $variant['description']) {
                    $result[$key] = $variant['description'];
                }
            }
        }

        if (!empty($result)) {
            $basic  = $this->getBasicData();
            $result = $this->app->jbarray->unshiftAssoc($result, '', $basic['description']);
        }

        return $result;
    }

    /**
     * Render SKU partial
     * @param $params
     * @return null|string
     */
    protected function _renderSKU($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('sku_show', 1) && $layout = $this->getLayout('_sku.php')) {
            return self::renderLayout($layout, array(
                'basic'      => $this->getBasicData(),
                'variations' => $this->_getVariations(),
            ));
        }

        return null;
    }

    /**
     * Render Sale partial
     * @param $params
     * @return string
     */
    protected function _renderSale($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('sale_show', 1) && $layout = $this->getLayout('_sale.php')) {

            $currencyDefault = $params->get('currency_default', 'EUR');
            $prices          = $this->_getTmplPrices($params);
            $basic           = $this->getBasicData();

            return JString::trim(self::renderLayout($layout, array(
                'mode'     => (int)$params->get('sale_show', self::SALE_VIEW_ICON_VALUE),
                'discount' => array(
                    'value'  => (float)$basic['discount'],
                    'format' => $this->_jbmoney->toFormat($basic['discount'], $basic['discount_currency']),
                ),
                'base'     => array(
                    'price' => $prices[$this->_getHash()]['prices'][$currencyDefault]['price'],
                    'total' => $prices[$this->_getHash()]['prices'][$currencyDefault]['total'],
                    'save'  => $prices[$this->_getHash()]['prices'][$currencyDefault]['save'],
                ),
            )));
        }

        return null;
    }

    /**
     * @return string
     */
    protected function _getDefaultCurrency()
    {
        return $this->config->get('currency_default', 'EUR');
    }

    /**
     * Bind and validate data
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
                                    unset($result['values'][$i][$key]);
                                }
                            }
                        }
                    }

                    $result['variations'][$i]['params'][$key] = $variant;
                }

            }

        }

        parent::bindData($result);

        /*$data = $this->_mergeDefaultData($data);

        $mainBalance = (int)trim($data['basic']['_balance']);
        $mainHash    = $this->_getHash();
        $balanceMode = (int)$this->config->get('balance_mode', 0);

        if (!(int)$this->config->get('balance_mode', 0)) {
            $mainBalance = ($data['basic']['_balance'] > 0 || $data['basic']['_balance'] == -1) ? -1 : 0;
        }

        $data['basic']['sku'] = JString::trim($data['basic']['_sku']);

        $data['basic'] = array(
            'value'    => $this->_jbmoney->clearValue($data['basic']['value']),
            'currency' => $this->_jbmoney->clearCurrency($data['basic']['_currency'], $this->_getDefaultCurrency()),
            'params'   => array(
                'sku'               => !empty($data['basic']['_sku']) ? $data['basic']['_sku'] : $mainHash,
                'balance'           => $mainBalance,
                'discount'          => $this->_jbmoney->clearValue($data['basic']['_discount']),
                'discount_currency' => $this->_jbmoney->clearCurrency($data['basic']['_discount_currency'], $this->_getDefaultCurrency()),
                'description'       => JString::trim($data['basic']['_description']),
                'image'             => isset($data['basic']['_image']) ? $data['basic']['_image'] : ''
            ),
        );

        if (isset($data['variations'])) {
            foreach ($data['variations'] as $key => $variant) {

                if ($balanceMode) {
                    $variant['balance'] = (int)JString::trim($variant['_balance']);
                } else {
                    $variant['balance'] = $mainBalance;
                }

                $variant['sku'] = JString::trim($variant['_sku']);

                $variant['params']['variant'] = $key;

                $data['variations'][$key] = array(
                    'value'    => $this->_jbmoney->clearValue($variant['_value']),
                    'currency' => $this->_jbmoney->clearCurrency($variant['_currency']),
                    'params'   => array(
                        'sku'         => !empty($variant['_sku']) ? $variant['_sku'] : $mainHash,
                        'balance'     => $variant['_balance'],
                        'description' => isset($variant['_description']) ? JString::trim($variant['_description']) : '',
                        'image'       => isset($variant['_image']) ? JString::trim($variant['_image']) : '',
                    ),

                    'params'   => $variant['params']
                );
            }

            $variations = $uniqHashes = array();

            foreach ($data['variations'] as $key => $variant) {

                $variant['hash'] = $this->_getHash($variant);

                if ($variant['hash'] !== $mainHash && !in_array($variant['hash'], $uniqHashes, true)) {
                    $uniqHashes[] = $variant['hash'];

                    $variations[$variant['hash']] = $variant;

                } else {
                    unset($data['variations'][$key]);
                }

            }

            $data['variations'] = $variations;
        }

        //die;
        parent::bindData($data);*/
    }

    /**
     * Get index SKU-data for element
     * @param bool $force
     * @return array
     */
    public function getIndexData($force = false)
    {
        $itemId = $this->_getItemId();
        //$this->app->jbdebug->mark('JBPrice::indexData::start-' . $itemId);

        $isCacheEnabled = (int)$this->config->get('cache', 0) && !$force;

        $result = array();
        if ($isCacheEnabled) {
            $cache     = $this->app->jbcache;
            $cacheHash = array(
                'itemId'  => $itemId,
                'elemid'  => $this->identifier,
                'data'    => (array)$this->data(),
                'config'  => (array)$this->config,
                'curmode' => $this->app->jbmoney->getMode(),
            );
            $cacheKey  = 'price_data';
            $result    = $cache->get($cacheHash, $cacheKey, true);
        }

        if (empty($result)) {

            $basic = $this->getBasicData();

            if (!empty($basic['params'])) {

                $basicParams = $this->app->data->create($basic['params']);

                $discount      = $basicParams->get('_discount');
                $basicParamSku = $basicParams->get('_sku');

                $discountVal  = isset($discount['value']) ? $discount['value'] : 0;
                $discountCurr = isset($discount['currency']) ? $discount['currency'] : $this->_getDefaultCurrency();

                $variations      = $this->_getVariations();
                $currencyDefault = $this->_getDefaultCurrency();

                $price     = $this->_jbmoney->convert($basic['_currency'], $currencyDefault, $basic['_value']);
                $basePrice = $this->_jbmoney->calcDiscount($basic['_value'], $basic['_currency'], $discountVal, $discountCurr);
                $total     = $this->_jbmoney->convert($basic['_currency'], $currencyDefault, $basePrice);

                $mainHash = $this->_getHash();
                $mainSku  = !empty($basicParamSku) ? $basicParamSku : $mainHash;

                $result[] = array(
                    'is_sale'     => (int)($discountVal < 0),
                    'item_id'     => $itemId,
                    'element_id'  => $this->identifier,
                    'sku'         => $mainSku,
                    'type'        => self::TYPE_PRIMARY,
                    'price'       => $price,
                    'total'       => $total,
                    'currency'    => $currencyDefault,
                    'balance'     => $basicParams->get('_balance', -1),
                    'image'       => $basicParams->get('_image'),
                    'description' => $basicParams->get('_description')
                );
            }

            if (!empty($variations)) {
                foreach ($variations as $variant) {

                    $variant       = $this->app->data->create($variant);
                    $variantParams = $this->app->data->create($variant->get('params'));
                    $variantSku    = $variantParams->get('_sku', $basicParamSku);

                    $discount     = $variantParams->get('_discount');
                    $discountVal  = $discount['value'];
                    $discountCurr = $discount['currency'];

                    $basic = $this->app->data->create($basic);

                    $value = $this->_jbmoney->calc($basic->get('_value'), $basic->get('_currency'), $variant->get('_value'), $variant->get('_currency'));
                    $price = $this->_jbmoney->convert($basic->get('_currency'), $currencyDefault, $value);
                    $total = $this->_jbmoney->calcDiscount($value, $basic->get('_currency'), $discountVal, $discountCurr);
                    $total = $this->_jbmoney->convert($basic->get('_currency'), $currencyDefault, $total);

                    $result[] = array(
                        'item_id'     => $itemId,
                        'is_sale'     => (int)($discount['value'] < 0),
                        'element_id'  => $this->identifier,
                        'sku'         => !empty($variantSku) ? $variantSku : $mainSku,
                        'type'        => self::TYPE_SECONDARY,
                        'price'       => $price,
                        'total'       => $total,
                        'currency'    => $currencyDefault,
                        'balance'     => $variantParams->get('_balance', -1),
                        'image'       => $variantParams->get('_image', ''),
                        'description' => $variantParams->get('_description', '')
                    );
                }
            }

            if ($isCacheEnabled) {
                $cache->set($cacheHash, $result, $cacheKey, true);
            }
        }

        //$this->app->jbdebug->mark('JBPrice::indexData::finish-' . $itemId);

        return $result;
    }

    public function getIndexDataParameters()
    {
        $variations = $this->_getVariations();
        $elements   = array();

        if (!empty($variations)) {
            foreach ($variations as $variant) {
                foreach ($variant['params'] as $identifier => $value) {
                    if (strpos($identifier, '_') !== 0) {
                        $elements[] = array(
                            'element_id' => $identifier,
                            'value'      => $value[key($value)],
                            'item_id'    => $this->_item->id
                        );
                    }
                }
            }
        }

        return $elements;
    }

    /**
     * Get hash for variant
     * @param array $variant
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
     * Get balance text for templates
     * @param int $balance
     * @param int $viewMode
     * @param int $type
     * @return string
     */
    protected function _getBalanceText($balance, $viewMode = 0, $type = self::TYPE_PRIMARY)
    {
        $viewMode = (int)$viewMode;

        if ($viewMode == self::BALANCE_VIEW_NO) {
            return '';
        }

        $balanceMode = (int)$this->config->get('balance_mode', 0);
        $textNo      = '<span class="not-available">' . JText::_('JBZOO_JBPRICE_NOT_AVAILABLE') . '</span>';
        $textYes     = '<span class="available">' . JText::_('JBZOO_JBPRICE_AVAILABLE') . '</span>';

        if ($type == self::TYPE_PRIMARY) {

            $basicData = $this->getBasicData();

            if ($viewMode == self::BALANCE_VIEW_SIMPLE) {
                return $basicData['balance'] == 0 ? $textNo : $textYes;

            } else if ($viewMode == self::BALANCE_VIEW_FULL) {

                if ($balanceMode && $basicData['balance'] > 0) {
                    return '<span class="available">' . JText::_('JBZOO_JBPRICE_BALANCE_TEXT') . ': ' . $basicData['balance'] . '</span>';

                } else if (!$balanceMode && $basicData['balance'] > 0) {
                    return $textYes;

                } else if ($basicData['balance'] == -1) {
                    return $textYes;

                } else if ($basicData['balance'] == 0) {
                    return $textNo;
                }
            }

        } else if ($type == self::TYPE_SECONDARY) {

            if ($viewMode == self::BALANCE_VIEW_SIMPLE) {
                return $balance == 0 ? $textNo : $textYes;

            } else if ($viewMode == self::BALANCE_VIEW_FULL) {

                if ($balanceMode && $balance > 0) {
                    return '<span class="available">' . JText::_('JBZOO_JBPRICE_BALANCE_TEXT') . ': ' . $balance . '</span>';

                } else if (!$balanceMode && ($balance > 0 || $balance == -1)) {
                    return $textYes;

                } else if ($balance == -1) {
                    return $textYes;

                } else if ($balance == 0) {
                    return $textNo;
                }
            }
        }

        return null;
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
     */
    public function ajaxAddToCart($quantity = 1, $params = array(), $sendAjax = true)
    {
        $jbajax = $this->app->jbajax;

        $hash = $this->_getHash(array(
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

        $sendAjax && $jbajax->send(array('added' => 0, 'message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')));
    }

    /**
     * Ajax remove from cart method
     */
    public function ajaxRemoveFromCart()
    {
        $result = $this->app->jbcart->removeItem($this->getItem(), true);
        $this->app->jbajax->send(array('removed' => $result));
    }

    /**
     * @param string $layout
     * @param string $position
     * @param array $values
     * @param int $index
     */
    public function ajaxChangeVariant($layout = 'full', $position = '', $index = 1, $values = array())
    {
        if ($params = $this->_getRenderParams($layout, $position, $index)) {

            $params    = $this->app->data->create($params);
            $priceMode = (int)$this->config->get('price_mode', 1);

            if ($priceMode == self::PRICE_MODE_OVERLAY) {
                $variant = $this->getVariantByValuesOverlay($values);
            } else {
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
     * Check is good in stock
     * @param $hash
     * @param $quantity
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
     * Get render params
     * @param $layout
     * @param $position
     * @param $index
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
     * Get total price by hash
     * @param $hash
     * @return string
     */
    protected function _getPriceByHash($hash)
    {
        $data = $this->getIndexData();

        if (isset($data[$hash])) {
            return $data[$hash];
        }

        return $data[$this->_getHash()];
    }

    /**
     * Get formated params
     * @param $params
     * @return array
     */
    protected function _getFormatedParams($params)
    {
        $result = array();
        foreach ($params as $key => $value) {
            if ($value) {
                $options = $this->_getParamOptions((int)$key);
                reset($options);
                $name = current($options);

                preg_match('#-(.*?)-#u', $name, $matches); // TODO Remove this hack
                if (isset($matches[1]) && $name = JString::trim($matches[1])) {
                    $result[$name] = isset($options[$value]) ? $options[$value] : '';
                }
            }
        }

        return $result;
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
     * @param $data
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
     * @param $hash
     * @param $quantity
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
