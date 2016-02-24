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
    /**
     * @type string
     */
    protected $_param_id;

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
     * @param ElementJBPrice|String $element
     * @param Item                  $item
     * @param array                 $options
     */
    public function __construct($element, Item $item = null, $options = array())
    {
        parent::__construct($element, $item, $options);

        $this->_param_id = array_key_exists('paramId', $options) ? $options['paramId'] : null;;
        $this->_helper = $this->app->jbprice;
        $this->_cell   = $this->app->jbcsvcell;
    }

    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result  = $variations = array();
        $default = '';
        $data    = $this->_value;

        // this data not need for user.
        unset($data['values'], $data['selected']);
        // Get variations
        if(isset($data['variations'])) {
            $variations = (array)$data['variations'];
            unset($data['variations']);
        }

        if(isset($data['default_variant'])) {
            $default = (int)$data['default_variant'];
        }

        if (!empty($variations)) {
            foreach ($variations as $key => $elements) {

                if($default === (int) $key) {
                    $result[$key][] = 'default_variant:' . $default;
                }
                foreach ($elements as $id => $data) {
                    if ($element = $this->_element->getElement($id, $key)) {
                        $element->bindData($data);

                        $csv   = $this->create($element);
                        $toCSV = $csv->toCSV();
                        if ($toCSV !== null && $toCSV !== '') {
                            $element_name = JString::strtolower($element->getName());
                            if ($element->isCore()) {
                                $element_name = JString::strtolower($element->identifier);
                            }

                            $result[$key][] = $element_name . ':' . $toCSV;
                        }

                        unset($element);
                    }
                }

                if (isset($result[$key]) && is_array($result[$key])) {
                    $result[$key] = implode(';', $result[$key]);
                }
            }
        }

        return $result;
    }

    /**
     * @param  array|string $values
     * @param  int|string   $position
     * @return Item|void
     */
    public function fromCSV($values, $position = ElementJBPrice::BASIC_VARIANT)
    {
        $importData = $this->_lastImportParams->get('previousparams');
        $cleanPrice = isset($importData['cleanPrice']) && (int)$importData['cleanPrice'];

        if ($position == 1 && $cleanPrice) {
            $this->_element->cleanVariations();
        }

        if ($values === null || JString::strlen($values) === 0) {
            return $this->_item;
        }
        $configs = $bindData = array();
        $params  = $this->_lastImportParams->get('previousparams');

        $values = $this->_getAutoClean($values);
        if (JString::strpos($values, ':') !== false) {
            --$position;
            $variant = (array)$this->_element->getData($position, array());

            $values  = $this->_unpackFromLine($values);
            if(isset($values['default_variant'])) {
                $bindData['default_variant'] = $values['default_variant'];
                unset($values['default_variant']);
            }
            $values  = $this->isOldFormat($values);
            if (count($values)) {
                foreach ($values as $id => $value) {
                    $value = JString::trim($value);

                    if (strpos($id, '_') !== 0) {
                        $_id = $this->getIdByName($id);

                        $values[$_id] = $value;
                        unset($values[$id]);
                        $id = $_id;
                    }

                    $configs[$id] = (array)$this->getConfig($id);
                    if (!empty($configs[$id])) {
                        $instance = $this->create($configs[$id]['type']);

                        $value        = $instance->fromCSV($value, $position);
                        $variant[$id] = $value;
                    }
                }
            }
        } else {
            $values = JString::trim($values);

            if (JSTring::strlen($values) === 0) {
                return $this->_item;
            }
            $id = $this->_param_id;

            $position     = ElementJBPrice::BASIC_VARIANT;
            $variant      = (array)$this->_element->getData($position, array());

            $configs[$id] = (array)$this->getConfig($id);

            if (!empty($configs[$id])) {
                $instance     = $this->create($configs[$id]['type']);
                $variant[$id] = $instance->fromCSV($values, $position);
            }
        }
        if ($values !== null && $values !== '' && isset($params['checkOptions']) && (int)$params['checkOptions'] === JBImportHelper::OPTIONS_YES) {
            if (is_string($values)) {
                $values = array(
                    $this->_param_id => $values
                );
            }

            foreach ($values as $key => $val) {
                $this->_helper->addOption($this->_element, $key, $val);
            }
        }

        $bindData['variations'] = array($position => $variant);
        $this->_element->bindData($bindData);

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
        $files  = JFolder::files($this->app->path->path('jbelements:price'));
        foreach ($values as $key => $value) {
            if (in_array('price_' . $key . '.php', $files, true)) {
                $format['_' . $key] = $value;

                $old = true;
            }
        }

        unset($files);
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
            foreach ($configs as $id => $value) {
                if (JString::strlen($id) !== ElementJBPrice::SIMPLE_PARAM_LENGTH ||
                    $i > 3 || !isset($values['param' . $i])
                ) {
                    continue;
                }
                $key = JString::strtolower(JString::trim($value['name']));

                $format[$key] = $values['param' . $i];
                unset($values['param' . $i]);
                $i++;
            }
        }

        return $old === true ? $format : $values;
    }

    /**
     * Get core price element
     * @param $element
     * @return bool|JBCSVItemPrice
     */
    public function create($element)
    {
        return $this->_helper->csvItem($element, $this->_element);
    }

    /**
     * @param $id
     * @return array
     */
    public function getConfig($id)
    {
        $configs = (array)$this->_config
            ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->_element->identifier)
            ->get(JBCart::DEFAULT_POSITION, array());

        $config = isset($configs[$id]) ? $configs[$id] : array();

        return $config;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getIdByName($name)
    {
        $configs = (array)$this->_config
            ->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->_element->identifier)
            ->get(JBCart::DEFAULT_POSITION, array());

        $app = $this->app;
        $config = array_filter(array_map(function ($data) use ($name, $app) {
            if ($app->jbvars->lower($name) == $app->jbvars->lower($data['name'])) {
                return $data;
            }

            return null;

        }, $configs));
        $config = array_filter($config);
        $key    = key($config);

        return isset($key) ? $key : null;
    }
}
