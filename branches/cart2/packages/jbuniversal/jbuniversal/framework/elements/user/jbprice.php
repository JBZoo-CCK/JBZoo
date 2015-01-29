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
 * Class JBCSVItemUserJBPrice
 */
class JBCSVItemUserJBPrice extends JBCSVItem
{
    /** @type ElementJBPrice $_element Instance of price */
    protected $_element = null;

    /**
     * Link to the same protected property in ElementJBPrice
     * @type JBCartVariantList
     */
    protected $_list;

    /**
     * @type JBPriceHelper
     */
    protected $_helper;

    /**
     * @type JBCSVCellHelper
     */
    protected $_cell;

    /**
     * @type array
     */
    protected $_options = array();

    /**
     * @param ElementJBPrice|String $element
     * @param Item                  $item
     * @param array                 $options
     */
    public function __construct($element, Item $item = null, $options = array())
    {
        parent::__construct($element, $item, $options);

        $this->_options = $options;
        $this->_helper  = $this->app->jbprice;
        $this->_cell    = $this->app->jbcsvcell;

        $this->app->loader->register('JBCSVItemPrice', 'jbelements:price/price.php');
    }

    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();
        if (!empty($this->_value['variations'])) {
            $list = $this->_element->getVariantList($this->_value['variations'], array(), true);
            foreach ($list->all() as $key => $variant) {
                $line = $this->_packToLine($variant);

                if (!empty($line)) {
                    $result[$key] = $line;
                }
            }
        }

        return $result;
    }

    /**
     * @param  JBCartVariant $variant
     * @param  bool          $nullElement
     * @return string
     */
    protected function _packToLine($variant, $nullElement = false)
    {
        $from = array(':', ';');
        $to   = array('%col%', '%sem%');
        $line = array();

        foreach ($variant->getElements() as $id => $element) {
            $value = $element->getValue();

            if ($element->isCore()) {
                $instance = $this->create($element);
                $value    = $instance->toCSV();
            }

            if ($value = $this->_helper->getValue($value)) {
                $line[] = $id . ':' . JString::str_ireplace($from, $to, $value);
            }
        }

        return implode(';', $line);
    }

    /**
     * @param  array|string $values
     * @param  int|string   $position
     * @return Item|void
     */
    public function fromCSV($values, $position = ElementJBPrice::BASIC_VARIANT)
    {
        if (is_null($values) || JString::strlen($values) === 0) {
            return $this->_item;
        }

        $options = $this->_options;
        $configs = array();

        $params = $this->_lastImportParams->get('previousparams');
        $data   = $this->_element->get('variations');

        if (JString::strpos($values, ':') !== false) {
            --$position;

            $values = $this->_unpackFromLine($values);
            $values = $this->isOldFormat($values);

            if (!empty($values)) {
                foreach ($values as $id => $value) {

                    $configs[$id] = (array)$this->getElementConfig($id);

                    if (!empty($configs[$id])) {
                        $instance = $this->create($configs[$id]['type']);

                        $data[$position][$id] = $instance->fromCSV($value, $position);
                    }
                }
            }

        } else {

            $values = JString::trim($values);
            if (JSTring::strlen($values) === 0) {
                return $this->_item;
            }

            $id = $options['paramId'];

            $position     = ElementJBPrice::BASIC_VARIANT;
            $configs[$id] = (array)$this->getElementConfig($id);
            $instance     = $this->create($configs[$id]['type']);

            $data[$position][$id] = $instance->fromCSV($values, $position);
        }

        if (isset($params['checkOptions']) && (int)$params['checkOptions'] == JBImportHelper::OPTIONS_YES) {
            if (!empty($values)) {
                if (is_string($values)) {
                    $values = array(
                        $options['paramId'] => $values
                    );
                }

                foreach ($values as $key => $val) {
                    $this->_helper->addOption($this->_element, $key, $val);
                }
            }
        }

        $this->_element->bindData(array('variations' => $data));

        return $this->_item;
    }

    /**
     * @param  array $values
     * @return array|boolean
     */
    protected function isOldFormat($values = array())
    {
        if (empty($values)) {
            return $values;
        }

        $old    = false;
        $format = array();
        foreach ($values as $key => $value) {
            if (JString::strlen($key) !== ElementJBPrice::SIMPLE_PARAM_LENGTH &&
                strpos($key, '_') === false
            ) {
                $format['_' . $key] = $value;

                $old = true;
            }
        }

        if (isset($format['_currency'])) {
            $format['_value'] .= $format['_currency'];
            unset($format['_currency']);
        }

        if (isset($values['discount_currency'])) {
            $format['_discount'] .= $values['discount_currency'];
        }

        //if isset param1, param2, param3 from old version get array of price elements configs.
        if (isset($values['param1']) ||
            isset($values['param2']) ||
            isset($values['param3'])
        ) {
            $old     = true;
            $configs = $this->_element->params;

            $i = 1;
            foreach ($configs as $key => $value) {

                if (JString::strlen($key) !== ElementJBPrice::SIMPLE_PARAM_LENGTH ||
                    $i > 3 || !isset($values['param' . $i])
                ) {
                    continue;
                }

                $format[$key] = $format['_param' . $i];
                unset($format['_param' . $i]);
                $i++;
            }
        }

        return $old === true ? $format : $values;
    }

    /**
     * Get core price element
     * @param $element
     * @return bool|\JBCSVItemPrice
     */
    public function create($element)
    {
        return $this->_helper->csvItem($element, $this->_element);
    }

    /**
     * @param $id
     * @return array
     */
    public function getElementConfig($id)
    {
        $config = (array)$this->_config->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->_element->identifier)
                                       ->get('list', array());

        return isset($config[$id]) ? $config[$id] : array();
    }
}
