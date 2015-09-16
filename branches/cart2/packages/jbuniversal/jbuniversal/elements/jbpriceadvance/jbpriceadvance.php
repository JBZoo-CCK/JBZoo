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

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

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

        // link to money helper
        $this->_jbmoney = $this->app->jbmoney;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $params = $this->app->data->create($params);

        if (!(int)$params->get('show_empty_price', 1)) {

            $basic = $this->_getBasicData();

            if (empty($basic['value']) || $basic['value'] == 0) {
                return false;
            }
        }

        if (!(int)$params->get('show_empty_balance', 1)) {
            $basic = $this->_getBasicData();

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
        $data = $this->_getBasicData();

        return $data['sku'];
    }

    /**
     * @return null|string
     */
    public function edit()
    {
        $this->app->jbassets->admin();

        if ($layout = $this->getLayout('edit.php')) {

            $variations = $this->_getVariations();
            if (empty($variations) && (int)$this->config->get('mode', 0)) {
                $basic                = $this->_getBasicData();
                $basic['description'] = '';
                $variations           = array($basic);
            }

            return self::renderLayout($layout, array(
                'config'       => $this->config,
                'currencyList' => $this->_getCurrencyList($this->config),
                'variations'   => $variations,
                'basicData'    => $this->_getBasicData(),
                'param1'       => $this->_getParamOptions(1, true),
                'param2'       => $this->_getParamOptions(2, true),
                'param3'       => $this->_getParamOptions(3, true),
            ));
        }

        return null;
    }

    /**
     * Get currency list
     * @param JSONData $params
     * @return array
     */
    protected function _getCurrencyList($params)
    {
        $all = $this->app->jbmoney->getCurrencyList(true);

        $default = $params->get('currency_default', 'EUR');
        $list    = $params->get('currency_list', array());
        if (!in_array($default, $list)) {
            $list[] = $default;
        }
        $list = array_unique($list);

        $result = array();

        foreach ($list as $currency) {
            $currency = strtolower($currency);
            $result[$currency] = $all[$currency];
        }

        return $result;
    }

    /**
     * Get variation list
     * @return array
     */
    protected function _getVariations()
    {
        $result = array();

        if (!(int)$this->config->get('mode', 0)) {
            return $result;
        }

        $data        = $this->data();
        $defaultData = $this->_getDefaultData();
        $mainHash    = $this->_getHash();

        if (isset($data['variations'])) {
            foreach ($data['variations'] as $variant) {
                $hash = $this->_getHash($variant);
                if ($mainHash !== $hash) {
                    $variant['hash'] = $this->_getHash($variant);
                    $result[]        = array_merge($defaultData, $variant);
                }
            }
        }

        return $result;
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
     * Get general data values
     * @return float
     */
    protected function _getBasicData()
    {
        $data = $this->data();

        $balance = isset($data['basic']['balance']) ? (int)$data['basic']['balance'] : -1;
        if (!(int)$this->config->get('balance_mode')) {
            if ($balance != 0) {
                $balance = -1;
            }
        }

        return array(
            'new'               => isset($data['basic']['new']) ? (int)$data['basic']['new'] : 0,
            'hit'               => isset($data['basic']['hit']) ? (int)$data['basic']['hit'] : 0,
            'sku'               => isset($data['basic']['sku']) ? $data['basic']['sku'] : $this->_getItemId(),
            'value'             => isset($data['basic']['value']) ? (float)$data['basic']['value'] : 0,
            'currency'          => isset($data['basic']['currency']) ? $data['basic']['currency'] : $this->_getDefaultCurrency(),
            'discount'          => isset($data['basic']['discount']) ? $data['basic']['discount'] : 0,
            'discount_currency' => isset($data['basic']['discount_currency']) ? $data['basic']['discount_currency'] : $this->_getDefaultCurrency(),
            'description'       => isset($data['basic']['description']) ? $data['basic']['description'] : '',
            'balance'           => $balance,
        );
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
            $basic = $this->_getBasicData();
            $result = $this->app->jbarray->unshiftAssoc($result, '', $basic['description']);
        }

        return $result;
    }

    /**
     * Load static assets
     * @return $this
     */
    public function loadAssets()
    {
        //$this->app->jbassets->initJBpriceAdvance();
        return parent::loadAssets();
    }

    /**
     * Get name for prices variantss
     * @param $name
     * @param int $index
     * @return string
     */
    public function getRowControlName($name, $index = 0)
    {
        return "elements[{$this->identifier}][variations][{$index}][{$name}]";
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
     * Render for front-end
     * @param array $params
     * @return string|void
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $template = $params->get('template', 'default');

        if ($template == 'modal') {
            return $this->_renderTmplModal($params);

        } elseif ($template == 'default') {
            return $this->_renderTmplDefault($params);

        } elseif ($template == 'only_price') {
            return $this->_renderTmplOnlyPrice($params);

        } elseif ($template == 'only_sku') {
            return $this->_renderTmplOnlySku($params);

        } elseif ($template == 'only_balance') {
            return $this->_renderTmplOnlyBalance($params);

        } elseif ($template == 'only_sale') {
            return $this->_renderTmplOnlySale($params);

        } elseif ($template == 'only_new') {
            return $this->_renderTmplOnlyNew($params);

        } elseif ($template == 'only_hit') {
            return $this->_renderTmplOnlyHit($params);

        } elseif ($template == 'only_buttons') {
            return $this->_renderTmplOnlyButtons($params);
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
     * Render default (complex) layout
     * @param $params
     * @return string
     */
    protected function _renderTmplDefault($params)
    {
        $layout = $this->getLayout('tmpl_default.php');
        $prices = $this->_getTmplPrices($params);
        $item   = $this->getItem();

        return self::renderLayout($layout, array(
            'skuTmpl'           => $this->_renderSku($params),
            'balanceTmpl'       => $this->_renderBalance($params),
            'countTmpl'         => $this->_renderCount($params),
            'pricesTmpl'        => $this->_renderPrices($params, $prices),
            'buttonsTmpl'       => $this->_renderButtons($params),
            'prices'            => $prices,
            'isInCart'          => (int)$this->app->jbcart->isExists($item),
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
     * Render "only SKU" template
     * @param $params
     * @return string
     */
    protected function _renderTmplOnlySku($params)
    {
        $layout = $this->getLayout('tmpl_only_sku.php');
        return self::renderLayout($layout, array(
            'basic' => $this->_getBasicData(),
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
            'basic'  => $this->_getBasicData(),
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
        $basic    = $this->_getBasicData();

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
                'basic'      => $this->_getBasicData(),
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
     * Render prices partial
     * @param array $params
     * @param array $prices
     * @return null|string
     */
    protected function _renderPrices($params, $prices)
    {
        $params          = $this->app->data->create($params);
        $basic           = $this->_getBasicData();
        $currencyDefault = $params->get('currency_default', 'EUR');
        $currencyList    = $this->_getCurrencyList($params);

        if ($layout = $this->getLayout('_prices.php')) {
            return self::renderLayout($layout, array(
                'saleTmpl'     => $this->_renderSale($params),
                'newTmpl'      => $this->_renderNew($params),
                'hitTmpl'      => $this->_renderHit($params),
                'config'       => $this->config,
                'params'       => $params,
                'currencyList' => $currencyList,
                'selects'      => $this->_renderParamsControl($params),
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
            $currencyList = $this->_getCurrencyList($params);

            $prices = array();
            foreach ($indexData as $key => $data) {
                $hash = $data['hash'];

                $prices[$hash] = array(
                    'balance'     => $data['balance'],
                    'description' => $data['params']['description'],
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

        return $prices;
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
                'basic'      => $this->_getBasicData(),
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
            $basic           = $this->_getBasicData();

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
     * Render New partial
     * @param $params
     * @return null|string
     */
    public function _renderNew($params)
    {
        $params = $this->app->data->create($params);

        if ((int)$params->get('new_show', 1) && $layout = $this->getLayout('_new.php')) {

            $basic = $this->_getBasicData();

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

            $basic = $this->_getBasicData();

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
        $data = $this->_mergeDefaultData($data);

        $mainBalance = (int)trim($data['basic']['balance']);
        $mainHash    = $this->_getHash();
        $balanceMode = (int)$this->config->get('balance_mode', 0);

        if (!(int)$this->config->get('balance_mode', 0)) {
            $mainBalance = ($data['basic']['balance'] > 0 || $data['basic']['balance'] == -1) ? -1 : 0;
        }

        $data['basic']['sku'] = JString::trim($data['basic']['sku']);

        $data['basic'] = array(
            'balance'           => $mainBalance,
            'new'               => isset($data['basic']['new']) ? (int)$data['basic']['new'] : 0,
            'hit'               => isset($data['basic']['hit']) ? (int)$data['basic']['hit'] : 0,
            'sku'               => !empty($data['basic']['sku']) ? $data['basic']['sku'] : $mainHash,
            'value'             => $this->_jbmoney->clearValue($data['basic']['value']),
            'currency'          => $this->_jbmoney->clearCurrency($data['basic']['currency'], $this->_getDefaultCurrency()),
            'discount'          => $this->_jbmoney->clearValue($data['basic']['discount']),
            'discount_currency' => $this->_jbmoney->clearCurrency($data['basic']['discount_currency'], $this->_getDefaultCurrency()),
            'description'       => JString::trim($data['basic']['description']),
        );

        if (isset($data['variations'])) {
            foreach ($data['variations'] as $key => $variant) {

                if ($balanceMode) {
                    $variant['balance'] = (int)JString::trim($variant['balance']);
                } else {
                    $variant['balance'] = $mainBalance;
                }

                $variant['sku'] = JString::trim($variant['sku']);

                $data['variations'][$key] = array(
                    'balance'     => $variant['balance'],
                    'value'       => $this->_jbmoney->clearValue($variant['value']),
                    'currency'    => $this->_jbmoney->clearCurrency($variant['currency']),
                    'sku'         => !empty($variant['sku']) ? $variant['sku'] : $mainHash,
                    'param1'      => isset($variant['param1']) ? JString::trim($variant['param1']) : '',
                    'param2'      => isset($variant['param2']) ? JString::trim($variant['param2']) : '',
                    'param3'      => isset($variant['param3']) ? JString::trim($variant['param3']) : '',
                    'description' => isset($variant['description']) ? JString::trim($variant['description']) : '',
                );
            }

            $variations = $uniqHashes = array();

            foreach ($data['variations'] as $key => $variant) {

                $variant['hash'] = $this->_getHash($variant);

                if ($variant['hash'] !== $mainHash && !in_array($variant['hash'], $uniqHashes, true)) {
                    $uniqHashes[]                 = $variant['hash'];
                    $variations[$variant['hash']] = $variant;

                } else {
                    unset($data['variations'][$key]);
                }

            }
            $data['variations'] = $variations;

        }

        parent::bindData($data);
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

        $result = null;
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

            $basic           = $this->_getBasicData();
            $variations      = $this->_getVariations();
            $currencyDefault = $this->_getDefaultCurrency();

            $price     = $this->_jbmoney->convert($basic['currency'], $currencyDefault, $basic['value']);
            $basePrice = $this->_jbmoney->calcDiscount($basic['value'], $basic['currency'], $basic['discount'], $basic['discount_currency']);
            $total     = $this->_jbmoney->convert($basic['currency'], $currencyDefault, $basePrice);

            $mainHash = $this->_getHash();

            $mainSku = !empty($basic['sku']) ? $basic['sku'] : $mainHash;

            $result = array($mainHash => array(
                'hash'       => $mainHash,
                'is_new'     => (int)$basic['new'],
                'is_hit'     => (int)$basic['hit'],
                'is_sale'    => (int)($basic['discount'] < 0),
                'item_id'    => $itemId,
                'element_id' => $this->identifier,
                'sku'        => $mainSku,
                'type'       => self::TYPE_PRIMARY,
                'price'      => $price,
                'total'      => $total,
                'currency'   => $currencyDefault,
                'balance'    => $basic['balance'],
                'params'     => array(
                    'param1'      => '',
                    'param2'      => '',
                    'param3'      => '',
                    'description' => $basic['description'],
                ),
            ));

            if (!empty($variations)) {
                foreach ($variations as $key => $variant) {

                    $hash = $this->_getHash($variant);
                    if ($hash === $mainHash) {
                        continue;
                    }

                    $value = $this->_jbmoney->calc($basic['value'], $basic['currency'], $variant['value'], $variant['currency']);
                    $price = $this->_jbmoney->convert($basic['currency'], $currencyDefault, $value);
                    $total = $this->_jbmoney->calcDiscount($value, $basic['currency'], $basic['discount'], $basic['discount_currency']);
                    $total = $this->_jbmoney->convert($basic['currency'], $currencyDefault, $total);

                    $variant['sku'] = JString::trim($variant['sku']);

                    $result[$hash] = array(
                        'hash'       => $hash,
                        'item_id'    => $itemId,
                        'is_new'     => (int)$basic['new'],
                        'is_sale'    => (int)($basic['discount'] < 0),
                        'element_id' => $this->identifier,
                        'sku'        => !empty($variant['sku']) ? $variant['sku'] : $basic['sku'],
                        'type'       => self::TYPE_SECONDARY,
                        'price'      => $price,
                        'total'      => $total,
                        'currency'   => $currencyDefault,
                        'balance'    => $variant['balance'],
                        'params'     => array(
                            'param1'      => $variant['param1'],
                            'param2'      => $variant['param2'],
                            'param3'      => $variant['param3'],
                            'description' => $variant['description'],
                        ),

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

    /**
     * Get hash for variant
     * @param array $variant
     * @return string
     */
    protected function _getHash(array $variant = array())
    {
        $itemId = $this->_getItemId();

        if (empty($variant)) {
            return (string)$itemId;
        }

        $paramsArr = array(
            'p1-' . (isset($variant['param1']) ? $variant['param1'] : ''),
            'p2-' . (isset($variant['param2']) ? $variant['param2'] : ''),
            'p3-' . (isset($variant['param3']) ? $variant['param3'] : '')
        );

        if ((int)$this->config->get('adv_field_text', 0) == self::TEXT_FIELD_AS_PARAM) {
            if (isset($variant['description'])) {
                $paramsArr[] = 'd-' . $this->app->string->sluggify($variant['description']);
            } else {
                $paramsArr[] = 'd-';
            }
        }

        $result = implode('_', $paramsArr);

        if ($result === implode('_', array('p1-', 'p2-', 'p3-', 'd-')) ||
            $result === implode('_', array('p1-', 'p2-', 'p3-'))
        ) {
            return (string)$itemId;
        }

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

            $basicData = $this->_getBasicData();

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
            JPATH_BASE . '/modules/mod_jbzoo_item/renderer/item/positions.config',
            JPATH_BASE . '/modules/mod_zooitem/renderer/item/positions.config',
            JPATH_BASE . '/plugins/system/widgetkit_zoo/widgets/slideset/renderer/item/positions.config'
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
        $basicData = $this->_getBasicData();
        return $basicData['sku'];
    }

    /**
     * Get default data
     * @return array
     */
    protected function _getDefaultData()
    {
        return array(
            'hash'              => $this->_getHash(),
            'sku'               => $this->_getDefaultSku(),
            'new'               => 0,
            'balance'           => -1,
            'value'             => 0,
            'currency'          => $this->_getDefaultCurrency(),
            'discount'          => 0,
            'discount_currency' => $this->_getDefaultCurrency(),
            'description'       => '',
            'param1'            => '',
            'param2'            => '',
            'param3'            => '',
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

                if (!isset($variant['hash'])) {
                    $variant['hash'] = $this->_getHash($variant);
                }

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
