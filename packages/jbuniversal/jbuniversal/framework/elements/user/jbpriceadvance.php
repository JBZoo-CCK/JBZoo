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
 * Class JBCSVItemUserJBPriceAdvance
 */
class JBCSVItemUserJBPriceAdvance extends JBCSVItem
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var JBPriceParamsHelper
     */
    protected $_params;

    /**
     * @var JBCSVCellHelper
     */
    protected $_cell;

    /**
     * @param Element|String $element
     * @param Item $item
     * @param array $options
     */
    public function __construct($element, Item $item = null, $options = array())
    {
        parent::__construct($element, $item, $options);

        $this->_options = $options;
        $this->_params  = $this->app->jbpriceparams;
        $this->_cell    = $this->app->jbcsvcell;
    }

    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();

        $result[] = $this->_packToLine($this->_value['basic']);
        if (!empty($this->_value['variations'])) {
            foreach ($this->_value['variations'] as $variant) {
                $result[] = $this->_packToLine($variant);
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $options = $this->_options;

        if (JString::strpos($value, ':') !== false
            && JString::strpos($value, JBCSVItem::SEP_CELL) === false
        ) {
            $value = $this->_unpackFromLine($value);
        }

        $values  = $value;
        $variant = $position - 2;
        if (is_string($values)) {
            $values = array(
                $options['identifier'] => $values
            );

            $variant = null;
        }

        // check option configs
        if ($position != 1) {

            $importData = $this->_lastImportParams->get('previousparams');
            if (isset($importData['checkOptions']) && (int)$importData['checkOptions'] == JBImportHelper::OPTIONS_YES) {

                $config      = $this->_element->config;
                $itemTypeId  = $this->_item->getType()->id;
                $itemAppId   = $this->_item->application_id;
                $fieldParam1 = $config->get('adv_field_param_1');
                $fieldParam2 = $config->get('adv_field_param_2');
                $fieldParam3 = $config->get('adv_field_param_3');

                if (isset($value['param1']) && $fieldParam1) {
                    $this->app->jbtype->checkOption($value['param1'], $fieldParam1, $itemTypeId, $itemAppId);
                }
                if (isset($value['param2']) && $fieldParam2) {
                    $this->app->jbtype->checkOption($value['param2'], $fieldParam2, $itemTypeId, $itemAppId);
                }
                if (isset($value['param3']) && $fieldParam3) {
                    $this->app->jbtype->checkOption($value['param3'], $fieldParam3, $itemTypeId, $itemAppId);
                }
            }
        }

        if ($position == 1) {
            $variant = null;
        }

        $params = $this->_bindJBPriceByParams($values, $variant);
        $data   = $this->_bindJBPrice($params, $variant);

        /*if (!isset($data['variations'])) {
            $data['variations'] = array();
        }

        if (isset($value['param1'])) {
            $value['param1'] = $this->app->string->sluggify($value['param1']);
        }

        if (isset($value['param2'])) {
            $value['param2'] = $this->app->string->sluggify($value['param2']);
        }

        if (isset($value['param3'])) {
            $value['param3'] = $this->app->string->sluggify($value['param3']);
        }

        $data['variations'][] = $value;*/
        $this->_element->bindData($data);

        return $this->_item;
    }

    /**
     * @param  $params
     * @param  $variant
     * @return array
     */
    protected function _bindJBPriceByParams($params = array(), $variant = null)
    {
        if (empty($params)) {
            return $params;
        }

        $error = array();

        foreach ($params as $id => $value) {

            if (strpos($id, '_') === 0) {
                $id = JString::str_ireplace('_', '', $id, 1);
            }

            $name  = 'price_' . $id;
            $class = $this->_cell->createItem($name, $this->_item, 'price', array('elementId' => $this->_element->identifier));

            if (get_class($class) != 'JBCSVItem') {
                $class->fromCSV($value, $variant);
            } else {
                $error[$id] = $value;
            }
        }

        return $error;
    }

    /**
     * @param  array $params
     * @param  int $variant
     * @return array
     */
    protected function _bindJBPrice($params = array(), $variant = null)
    {
        if (isset($variant) && (isset($params['param1']) ||
                isset($params['param2']) ||
                isset($params['param3']))
        ) {
            $this->_bindOldParams($params, $variant);
        }

        $data = $this->_element->data();

        unset($params['param1']);
        unset($params['param2']);
        unset($params['param3']);

        if (empty($params)) {
            return $data;
        }
        $importData = $this->_lastImportParams->get('previousparams');

        foreach ($params as $id => $value) {

            if (isset($importData['checkOptions']) && (int)$importData['checkOptions'] == JBImportHelper::OPTIONS_YES) {
                $this->_params->addValueToParam($this->_identifier, $id, $value);
            }

            $value = $this->_params->getNestingValue($id, $value);

            if (!isset($variant)) {

                if ($this->_params->isMain($id)) {
                    $data['basic'][$id] = $value;
                } else {
                    $data['basic']['params'][$id] = $value;
                }

            } else if ($variant >= 0) {
                $element = $this->_element->loadElement($id);

                if ($this->_params->isMain($id)) {
                    $data['variations'][$variant][$id] = $value;
                } else {

                    if ($element->config->get('type') == 'color') {
                        $color = $this->app->jbcolor->getColors($value);
                        if (!empty($color)) {
                            $value = key($color);
                        }
                    }
                    $data['variations'][$variant]['params'][$id] = $value;
                }

            }
        }

        return $data;
    }

    /**
     * @param  array $values
     * @param  int $variant
     * @return array|boolean
     */
    protected function _bindOldParams($values = array(), $variant = null)
    {
        $data = $this->_element->data();
        if (empty($values)) {
            return false;
        }

        if (!$dataVariant = $this->_element->_getVariations($variant)) {
            return false;
        }

        $params    = $this->app->jbcartposition->loadForPrice($this->_element);
        $oldParams = array_values($values);

        $i = 0;
        foreach ($params as $param) {

            if ($i >= 3) {
                return false;
            }

            if (JString::strlen($param->identifier) == ElementJBPriceAdvance::SIMPLE_PARAM_LENGTH) {

                $value = $this->_params->getNestingValue($param->identifier, $oldParams[$i]);

                $dataVariant['params'][$param->identifier] = $value;
                $i++;
            }
        }

        $data['variations'][$variant] = $dataVariant;
        $this->_element->bindData($data);
    }

}
