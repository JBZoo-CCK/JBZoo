<?php
use Joomla\String\StringHelper;
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBPriceFilterElement
 */
class JBPriceFilterElement
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @var string
     */
    protected $_identifier = '';

    /**
     * @type JBCartElementPrice|JBCartElementPriceOption
     */
    protected $_element;

    /**
     * @var array|string
     */
    protected $_value = null;

    /**
     * @var ParameterData
     */
    protected $_params = null;

    /**
     * @var array
     */
    protected $_attrs = array();

    /**
     * @var JSONData
     */
    protected $_config = null;

    /**
     * @var boolean
     */
    protected $_isOrigTmpl = true;

    /**
     * @var bool
     */
    protected $_isMultiple = false;

    /**
     * @var bool
     */
    protected $_isCountShow = true;

    /**
     * @var JBHTMLHelper
     */
    protected $_html = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_money = null;

    /**
     * @param       $element
     * @param       $value
     * @param array $params
     * @param array $attrs
     */
    public function __construct($element, $value, array $params, array $attrs)
    {
        $this->app = App::getInstance('zoo');

        $params        = empty($params) ? array() : $params;
        $this->_params = $this->app->parameter->create($params);

        $this->_identifier = $element->identifier;
        $this->_jbprice    = $element->getJBprice();
        $this->_element    = $element;
        $this->_value      = $this->_getElementValue($value);

        $this->_isOrigTmpl  = (int)$this->_params->get('jbzoo_original_type', 1);
        $this->_isMultiple  = (int)$this->_params->get('multiple', 0);
        $this->_isCountShow = (int)$this->_params->get('jbzoo_filter_count', 1);

        $this->_attrs  = $this->_getAttrs($attrs);
        $this->_config = $element->config;

        $this->_html  = $this->app->jbhtml;
        $this->_money = $this->app->jbmoney;
    }

    /**
     * Get element value
     * @param $value
     * @return mixed
     */
    protected function _getElementValue($value)
    {
        if ($this->_isValueEmpty($value) && $value = $this->_params->get('jbzoo_filter_default', null)) {
            $value = StringHelper::trim($value);

            if (strpos($value, '{') !== false && strpos($value, '}') !== false) {
                $value = json_decode($value, true);
            }
        }

        return $value;
    }

    /**
     * Check is variable empty
     * @param $value
     * @return bool
     */
    protected function _isValueEmpty($value)
    {
        return (empty($value) && $value != '0');
    }

    /**
     * Get available values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $result = array();

        if ($type == 'db') {
            $result = $this->_getDbValues();

        } elseif ($type == 'bool') {
            $result = $this->_getBoolValues();

        } elseif ($type == 'config') {
            $result = $this->_getConfigValues();

        } else if ($type == '__default__') {
            $result = true;
        }

        if (!empty($result)) {
            $result = $this->_sortByArray($result);
        }

        return $result;
    }

    /**
     * Get data from db index table by element identifier
     * @return array
     */
    protected function _getDbValues()
    {
        $isCatDepend = (int)$this->_params->moduleParams->get('depend_category');
        $categoryId  = null;
        if ($isCatDepend) {
            $categoryId = $this->app->jbrequest->getSystem('category');
        }

        return JBModelValues::model()->getParamsValues(
            $this->_jbprice->identifier,
            $this->_identifier,
            $this->_params->get('item_type', null),
            $this->_params->get('item_application_id', null),
            $categoryId
        );
    }

    /**
     * Get boolean values
     * @return array
     */
    protected function _getBoolValues()
    {
        $result = array(
            array(
                'text'  => JText::_('JBZOO_YES'),
                'value' => 1,
                'count' => null
            ),
            array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => 0,
                'count' => null
            )
        );

        return $result;
    }

    /**
     * Get config from options
     * @return mixed|null
     */
    protected function _getConfigValues()
    {
        $options = $this->_config->get('spin', array());

        foreach ($options as $key => $option) {
            $options[$key]['count'] = null;
        }

        return $options;
    }

    /**
     * @param $array
     * @return mixed
     */
    protected function _sortByArray($array)
    {
        if (!$this->_element->isCore()) {
            $array = $this->app->jbarray->index($array, 'value');
            $array = $this->app->jbarray->sortByArray($array, $this->_element->parseOptions());
        }

        return $array;
    }

    /**
     * Get html attributs
     * @param $attrs
     * @return array
     */
    protected function _getAttrs(array $attrs)
    {
        if ($this->_isMultiple) {
            $attrs['multiple'] = 'multiple';

            if (!isset($attrs['size'])) {
                $attrs['size'] = '5';
            }
        }

        return $attrs;
    }

    /**
     * @param array $values
     * @param bool  $showAll
     * @return array
     */
    protected function _createOptionsList($values, $showAll = true)
    {
        $options = array();
        if (!$this->_isMultiple && $showAll) {
            $options[] = $this->app->html->_('select.option', '', ' - ' . $this->_getPlaceholderSelect() . ' - ');
        }

        foreach ($values as $value) {
            if ($this->_element->hasOptions() && !$this->_element->hasOption($value['value'])) {
                continue;
            }

            $name = $value['text'];

            if (!empty($value['count']) && $this->_isCountShow) {
                $name = $name . ' (' . $value['count'] . ')';
            }

            $options[] = $this->app->html->_('select.option', $value['value'], $name);
        }

        return $options;
    }

    /**
     * Get element ID attribute
     * @param string $postFix
     * @param bool   $addUniq
     * @return string
     */
    protected function _getId($postFix = null, $addUniq = false)
    {
        static $uniqNumber;

        if (!isset($uniqNumber)) {
            $uniqNumber = 0;
        }

        $id = isset($this->_attrs['id']) ? $this->_attrs['id'] : $this->app->jbstring->getId('jbfilter-jbprice-');

        if ($postFix !== null) {
            $id .= '-' . $postFix;
        }

        $uniqNumber++;

        if ($addUniq) {
            $id = $id . '-' . $uniqNumber;
        }

        return $id;
    }

    /**
     * Get element name
     * @param bool $id Use element identifier or his id from sku table
     * @param bool $array
     * @return string
     */
    protected function _getName($id = false, $array = false)
    {
        $name = 'e[' . $this->_jbprice->identifier . '][' . $this->_identifier . ']';

        if ($id) {
            $name .= '[id]';
        }
        $name .= ($array ? '[]' : null);

        return $name;
    }

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        return $this->_html->text(
            $this->_getName(),
            $this->_value,
            $this->_attrs,
            $this->_getId()
        );
    }

    /**
     * Get placeholder text
     * @return string
     */
    protected function _getPlaceholder()
    {
        $default     = JText::_('JBZOO_FILTER_PLACEHOLDER_DEFAULT');
        $placeholder = StringHelper::trim($this->_params->get('jbzoo_filter_placeholder', $default));
        if (!$placeholder) {
            $placeholder = $default;
        }

        return $placeholder;
    }

    /**
     * Get placeholder text
     * @return string
     */
    protected function _getPlaceholderSelect()
    {
        $default     = JText::_('JBZOO_ALL');
        $placeholder = StringHelper::trim($this->_params->get('jbzoo_filter_placeholder', $default));
        if (!$placeholder) {
            $placeholder = $default;
        }

        return $placeholder;
    }

    /**
     * Init placeholder
     * @param $attrs
     * @return mixed
     */
    protected function _addPlaceholder($attrs)
    {
        $isAutocomplete = (int)$this->_params->get('jbzoo_filter_autocomplete', 0);
        $placeholder    = StringHelper::trim($this->_params->get('jbzoo_filter_placeholder'));

        if (!empty($placeholder)) {
            $attrs['placeholder'] = $placeholder;
        }

        if ($isAutocomplete) {
            $this->app->jbassets->initPriceAutoComplete();

            $attrs['class'][]     = 'jsPriceAutoComplete';
            $attrs['placeholder'] = $this->_getPlaceholder();
        }

        return $attrs;
    }

    /**
     * Check is has value
     */
    public function hasValue()
    {
        $data = $this->_getValues('__default__'); // TODO hack for empty values

        return !empty($data);
    }

}
