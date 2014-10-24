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

/**
 * Class JBCartElementPrice
 */
abstract class JBCartElementPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_PRICE;

    /**
     * @var JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @var ElementJBPriceAdvance
     */
    protected $_jbprice;

    /**
     * Constructor
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml   = $app->jbhtml;
        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * Check if element has value
     *
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param  array $params
     *
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return true;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getValue($key, $default = null)
    {
        $data = $this->data();

        return $data->get($key, $default);
    }

    /**
     * @return mixed
     */
    abstract function edit();

    /**
     * @param  array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return false;
    }

    /**
     * Load data from ElementJBPriceAdvance
     *
     * @param bool $bind - Bind data into element
     *
     * @return array
     */
    public function loadData($bind = true)
    {
        $data = array();
        if ($this->_jbprice instanceof ElementJBPriceAdvance) {

            $data = $this->_jbprice->getParamData($this);

            if ($bind === true) {
                $this->bindData($data);
            }
        }

        return $data;
    }

    /**
     * @param null $identifier
     *
     * @return array
     */
    public function getAllData($identifier = null)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return $this->_jbprice->getAllParamData($identifier);
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $allData = $this->getAllData();
        $options = $this->_renderOptions();
        $data    = array();

        $isOverlay = $this->_jbprice->isOverlay();
        $jbprice   = $this->_jbprice;

        if ($isOverlay) {
            $basicTotal = 0;
            $basic      = $jbprice->getBasicPrices();
            if (isset($basic['eur']['totalNoFormat'])) {
                $basicTotal = $basic['eur']['totalNoFormat'];
            }
        }

        if (!empty($allData)) {

            foreach ($allData as $name) {


                if (empty($name['value'])) {
                    continue;
                }

                $value = $name['value'];
                $name  = isset($options[$value]) ? $options[$value] : $value;

                if ($isOverlay) {
                    // Тушенка из котиков...
                    $calc = $jbprice->calcVariant($jbprice->getVariantByValuesOverlay(array(
                        $this->identifier => array(
                            'value' => $value
                        ),
                    )));

                    $diff = $calc['total'] - $basicTotal;
                    $cost = $this->_jbmoney->toFormat($diff, 'eur');
                    if ($diff > 0) {
                        $cost = '+' . $cost;
                    }

                    $data[$value] = $name . ' <em>' . $cost . '</em>';
                } else {
                    $data[$value] = $name;
                }

            }

            if (!empty($data)) {
                $data = array_merge(array(
                    '' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - '
                ), $data);
            }
        }

        return $data;
    }

    /**
     * @param ElementJBPriceAdvance $object
     */
    public function setJBPrice(ElementJBPriceAdvance $object)
    {
        $this->_jbprice = $object;
    }

    /**
     * @return ElementJBPriceAdvance
     */
    public function getJBPrice()
    {
        return $this->_jbprice;
    }

    /**
     * @return array
     */
    public function getPrices()
    {
        $jbPrice = $this->getJBPrice();
        $variant = $this->config->get('_variant');

        $currency = $this->getElementData('_currency', $variant);
        $currency = $currency->get('value', 'EUR');

        if (strpos($variant, '-') !== false) {

            $variants = explode('-', $variant);

            $basicData = $jbPrice->getBasicData();
            $basic     = $jbPrice->getReadableData($basicData);
            $value     = $basic->find('_value.value', 0);

            foreach ($variants as $v) {
                $v = $jbPrice->getVariations($v);
                $v = $jbPrice->getReadableData($v);

                $variantValue    = $v->find('_value.value', 0);
                $variantCurrency = $v->find('_currency.value', 'EUR');

                $value = $this->_jbmoney->calcDiscount($value, $currency, $variantValue, $variantCurrency);
            }

            $prices['total'] = $value;
            $prices['price'] = $value;

        } else if (JString::strlen($variant) >= 1) {

            $variant = $jbPrice->getVariations($variant);
            $prices  = $jbPrice->calcVariant($variant);

            //$prices = array_merge($data, $jbPrice->getPrices($data));
        } else {

            $prices = $jbPrice->calcBasic($currency);
            //$prices = array_merge($data, $jbPrice->getPrices($data));
        }

        $saveNoFormat = abs($prices['total'] - $prices['price']);

        $prices['total'] = $this->_jbmoney->toFormat($prices['total'], $currency);
        $prices['price'] = $this->_jbmoney->toFormat($prices['price'], $currency);
        $prices['save']  = $this->_jbmoney->toFormat($saveNoFormat, $currency);

        return $prices;
    }

    /**
     * @param string   $identifier
     * @param null|int $variant
     *
     * @return bool|JBCartElement|null
     */
    public function getElement($identifier, $variant = null)
    {
        $param = $this->getJBPrice()->getParam($identifier, $variant);

        return $param;
    }

    /**
     * @param string   $identifier
     * @param null|int $variant
     *
     * @return array|bool|JBCartElement|null
     */
    public function getElementData($identifier, $variant = null)
    {
        $param = $this->getElement($identifier, $variant);
        $param = !empty($param) ? $param->data() : $this->app->data->create($param);

        return $param;
    }

    /**
     * Get render parameters of any price element
     *
     * @param $identifier
     *
     * @return array|mixed
     */
    public function getRenderParams($identifier)
    {
        $data   = array();
        $params = $this->getJBPrice()->getCoreParamsConfig();

        if (isset($params[$identifier])) {
            $data = $params[$identifier];
        }

        return $this->app->data->create($data);
    }

    /**
     * Get basic variant data
     *
     * @return mixed
     */
    public function getBasicData()
    {
        return $this->getJBPrice()->getBasicReadableData();
    }

    /**
     * @param  string  $key
     * @param  boolean $array
     *
     * @return string
     */
    public function getControlName($key, $array = false)
    {
        $name = $this->getParamName($key);
        if (is_null($this->config->get('_variant'))) {
            $name = $this->getBasicName($key);
        }

        return $name;
    }

    /**
     * @param  string $name
     * @param bool    $array
     *
     * @return string
     */
    public function getBasicName($name, $array = false)
    {
        $priceId = $this->getJBPrice()->identifier;

        return "elements[{$priceId}][basic][params][{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param string $name
     * @param int    $index
     * @param bool   $array
     *
     * @return string
     */
    public function getParamName($name, $index = null, $array = false)
    {
        $priceId = $this->getJBPrice()->identifier;

        if (is_null($index)) {
            $index = $this->config->get('_variant');
        }

        return
            "elements[{$priceId}][variations][{$index}][params][{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function getRenderName($name)
    {
        $itemId = $this->getJBprice()->getItem()->id;

        return "params[{$itemId}][{$this->identifier}][{$name}]";
    }

    /**
     * @return mixed
     */
    protected function _getOptions()
    {
        $data = $this->config->get('options', array());

        return $data;
    }

    /**
     * @return array|null
     */
    protected function _renderOptions()
    {
        $options = $this->_getOptions();

        if (!empty($options)) {

            $options = $this->app->jbstring->parseLines($options);

            return array_merge(array(
                '' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - '
            ), array_combine($options, $options));

        }

        return null;
    }

    /**
     * @return string
     */
    public function renderOrderEdit()
    {
        return $this->data()->get('value');
    }

    /**
     * Clone data
     */
    public function __clone()
    {
        $this->_data = clone($this->_data);
    }

}

/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
