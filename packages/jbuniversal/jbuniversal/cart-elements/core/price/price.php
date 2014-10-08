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
        return TRUE;
    }

    /**
     * @param  array $params
     *
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return TRUE;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getValue($key, $default = NULL)
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
                'field'  => $this->renderFieldByType()
            ));
        }

        return FALSE;
    }

    /**
     * Load data from ElementJBPriceAdvance
     *
     * @param bool $bind - Bind data into element
     *
     * @return array
     */
    public function loadData($bind = TRUE)
    {
        $data = array();
        if ($this->_jbprice instanceof ElementJBPriceAdvance) {

            $data = $this->_jbprice->getParamData($this);

            if ($bind === TRUE) {
                $this->bindData($data);
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = array();


        return $this->app->data->create($params);
    }

    /**
     * @param null $identifier
     *
     * @return array
     */
    public function getAllData($identifier = NULL)
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

        if (!empty($allData)) {
            $data[''] = 'Chose your variant';

            foreach ($allData as $name) {
                if (empty($name['value'])) {
                    continue;
                }

                $value = $name['value'];
                $name  = isset($options[$value]) ? $options[$value] : $value;

                $data[$value] = $name;
            }
        }

        return $data;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getBasic($key, $default = NULL)
    {
        $value = 0;

        return $value;
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
     * @param bool $params
     *
     * @return mixed
     */
    public function getDefaultVariantData($params = FALSE)
    {
        $defaultVariant = $this->_jbprice->config->get('default');
        $defaultVariant = $this->app->data->create($defaultVariant);

        if ($params) {
            $variantParmas = $this->app->data->create($defaultVariant->get('params'));

            return $variantParmas;
        }

        return $defaultVariant;
    }

    /**
     * @return array
     */
    public function getPrices()
    {
        $jbPrice = $this->getJBPrice();
        $jbMoney = $this->app->jbmoney;

        $currencyDefault = $jbPrice->config->get('currency_default', 'EUR');
        $basicCurrency   = $jbPrice->getParam('_currency')->data()['value'];

        $data = array(
            'currency' => $currencyDefault,
            'value'    => 0
        );
        if ($discount = $jbPrice->getParam('_discount')) {
            $data = $discount->data();
        }

        $discountCurrency = $data['currency'];
        $discountValue    = $data['value'];

        $value = $jbPrice->getParam('_value')->data()['value'];

        $priceNoFormat = $jbMoney->convert($basicCurrency, $currencyDefault, $value);
        $price         = $jbMoney->toFormat($priceNoFormat, $basicCurrency);

        $totalNoFormat = $jbMoney->calcDiscount($value, $basicCurrency, $discountValue, $discountCurrency);
        $total         = $jbMoney->toFormat($totalNoFormat, $basicCurrency);

        $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
        $save         = $jbMoney->toFormat($saveNoFormat, $basicCurrency);

        $prices = array(
            'totalNoFormat' => $totalNoFormat,
            'priceNoFormat' => $priceNoFormat,
            'saveNoFormat'  => $saveNoFormat,
            'total'         => $total,
            'price'         => $price,
            'save'          => $save
        );

        return $prices;
    }

    /**
     * @param $identifier
     *
     * @return bool|JBCartElement|null
     */
    public function getPriceParam($identifier)
    {
        $param = $this->getJBPrice()->getParam($identifier);

        return $param;
    }

    /**
     * @param $identifier
     *
     * @return array|bool|JBCartElement|null
     */
    public function getParamData($identifier)
    {
        $param = $this->getPriceParam($identifier);

        return !empty($param) ? $param->data() : $param;
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
     * @return null
     */
    protected function renderFieldByType()
    {
        $values = $this->_renderOptions();
        $option = array();

        if (!empty($values)) {
            $type = $this->getElementType();
            $attr = $this->_jbhtml->buildAttrs(array(
                'class' => 'jsParam'
            ));

            foreach ($values as $options) {
                $option[] = $this->app->html->_('select.option', $options['value'], $options['name']);
            }

            return $this->_jbhtml->$type($option, $this->getControlName('value'), $attr, $attr);
        }

        return NULL;
    }

    /**
     * @param  string  $key
     * @param  boolean $array
     *
     * @return string
     */
    public function getControlName($key, $array = FALSE)
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
    public function getBasicName($name, $array = FALSE)
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
    public function getParamName($name, $index = NULL, $array = FALSE)
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
        $result  = array();

        if (!empty($options)) {
            $options = explode("\n", $options);

            $result[''] = JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT');

            foreach ($options as $value) {

                $value = JString::trim($value);
                if (empty($value)) {
                    continue;
                }

                list($name, $value) = explode('||', $value);
                $value = JString::strtolower(JString::trim($value));
                $value = $this->app->string->sluggify($value);

                $result[$value] = JString::trim($name);
            }

            return $result;
        }

        return NULL;
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
