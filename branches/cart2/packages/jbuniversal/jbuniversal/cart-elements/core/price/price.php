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

        return false;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = array();
        $config = $this->config;

        $data                 = $this->app->data->create($config->get('data'));
        $params['identifier'] = $this->_jbprice->identifier;
        $params['index']      = $config->get('index', 0);
        $params['basic']      = (int)$data->get('basic', 0);
        $params['data']       = $data->get('params');

        return $this->app->data->create($params);
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
        $data    = array();

        if (!empty($allData)) {
            $data[''] = 'Chose your variant';

            foreach ($allData as $name) {
                if (empty($name['value'])) {
                    continue;
                }

                $data[$name['value']] = $name['value'];
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
    public function getBasic($key, $default = null)
    {
        if ($defaultVariant = $this->_jbprice->getDefaultVariant()) {
            $defaultVariant = $this->app->data->create($defaultVariant);
            $defaultParams  = $this->app->data->create($defaultVariant->get('params'));
            $value          = $defaultVariant->get($key);

            if (empty($value)) {
                $value = $defaultParams->get($key, $default);
            }
        } else {

            $basic  = $this->app->data->create($this->_jbprice->getBasicData());
            $params = $this->app->data->create($basic->get('params'));
            $value  = $basic->get($key);

            if (empty($value)) {
                $value = $params->get($key, $default);
            }
        }

        return $value;
    }

    /**
     * @param ElementJBPriceAdvance $object
     */
    public function setJBPrice(ElementJBPriceAdvance $object)
    {
        static $add = false;

        if (!$add) {
            $this->_jbprice = $object;
        }

    }

    /**
     * @return ElementJBPriceAdvance
     */
    public function getJBPrice()
    {
        return $this->_jbprice;
    }

    /**
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
        $params = $this->getParams();
        $param  = $this->app->data->create($params->get('data'));

        return $param->get($key, $default);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        $priceparams = $this->_jbconfig->getGroup('cart.' . JBCart::ELEMENT_TYPE_PRICE);

        $list = $priceparams->get('list');

        return $this->app->data->create($list);
    }

    /**
     * @param  string  $key
     * @param  boolean $array
     *
     * @return string
     */
    public function getControlName($key = 'value', $array = false)
    {
        $params = $this->getParams();

        $name = $this->getParamName($params->get('identifier'), $key, $params->get('index', 0));
        if ((int)$params->get('basic', 0)) {
            $name = $this->getBasicName($params->get('identifier'), $key);
        }

        return $name;
    }

    /**
     * @param null $identifier
     * @param      $name
     *
     * @return string
     */
    public function getBasicName($identifier = null, $name)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][basic][params][{$name}]";
    }

    /**
     * @param null $identifier
     * @param      $name
     * @param  int $index
     *
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][params][{$name}]";
    }

    /**
     * Name for render
     *
     * @param  string $name
     *
     * @return string
     */
    public function getRenderName($name = 'value')
    {
        return "params[{$this->identifier}][{$name}]";
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

        return null;
    }

    /**
     * @param bool $params
     *
     * @return mixed
     */
    public function getDefaultVariantData($params = false)
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
        $currencyDefault = $this->_jbprice->config->get('currency_default', 'EUR');
        $basicCurrency   = $this->getBasic('_currency', $currencyDefault);

        $jbmoney = $this->app->jbmoney;
        $data    = $this->getBasic('_discount');

        $discountCurrency = $data['currency'];
        $discountValue    = $data['value'];

        $value = $this->getBasic('_value');

        $priceNoFormat = $jbmoney->convert($basicCurrency, $currencyDefault, $value);
        $price         = $jbmoney->toFormat($priceNoFormat, $basicCurrency);

        $totalNoFormat = $jbmoney->calcDiscount($value, $basicCurrency, $discountValue, $discountCurrency);
        $total         = $jbmoney->toFormat($totalNoFormat, $basicCurrency);

        $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
        $save         = $jbmoney->toFormat($saveNoFormat, $basicCurrency);

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

            return $this->_jbhtml->$type($option, $this->getControlName(), $attr, $attr);
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

}

/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
