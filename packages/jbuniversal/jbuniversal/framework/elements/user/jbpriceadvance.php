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
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();

        $result[] = $this->_packToLine($this->_value['basic']);
        if (!empty($this->_value['variations'])) {
            foreach ($this->_value['variations'] as $variant) {
                unset($variant['hash']);
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
        if (JString::strpos($value, ':') !== false
            && JString::strpos($value, JBCSVItem::SEP_CELL) === false
        ) {
            $values = $this->_unpackFromLine($value);

        } else {
            // converting from old JBPrice version
            $valuesTmp = $this->_getArray($value, JBCSVItem::SEP_CELL);

            if (count($valuesTmp) == 4) {
                $values = array(
                    'sku'         => $this->_getString($valuesTmp[0]),
                    'balance'     => $this->_getBool($valuesTmp[1]) ? -1 : 0,
                    'value'       => $this->_getFloat($valuesTmp[2]),
                    'description' => $this->_getString($valuesTmp[3]),
                );

            } else if (count($valuesTmp) == 3) {
                $values = array(
                    'sku'         => $this->_getString($valuesTmp[0]),
                    'value'       => $this->_getFloat($valuesTmp[1]),
                    'description' => $this->_getString($valuesTmp[2]),
                    'balance'     => -1,
                );

            } else if (count($valuesTmp) == 2) {
                $values = array(
                    'sku'         => $this->_item->id,
                    'value'       => $this->_getFloat($valuesTmp[0]),
                    'description' => $this->_getString($valuesTmp[1]),
                    'balance'     => -1,
                );

            } else {
                $values = array(
                    'sku'         => $this->_item->id,
                    'value'       => $this->_getFloat($valuesTmp[0]),
                    'description' => '',
                    'balance'     => -1,
                );
            }

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

                if (isset($values['param1']) && $fieldParam1) {
                    $this->app->jbtype->checkOption($values['param1'], $fieldParam1, $itemTypeId, $itemAppId);
                }
                if (isset($values['param2']) && $fieldParam2) {
                    $this->app->jbtype->checkOption($values['param2'], $fieldParam2, $itemTypeId, $itemAppId);
                }
                if (isset($values['param3']) && $fieldParam3) {
                    $this->app->jbtype->checkOption($values['param3'], $fieldParam3, $itemTypeId, $itemAppId);
                }
            }
        }

        // save data
        if ($position == 1) {
            $data = array('basic' => $values);

        } else {
            $data = $this->_element->data();

            if (!isset($data['variations'])) {
                $data['variations'] = array();
            }

            if (isset($values['param1'])) {
                $values['param1'] = $this->app->string->sluggify($values['param1']);
            }

            if (isset($values['param2'])) {
                $values['param2'] = $this->app->string->sluggify($values['param2']);
            }

            if (isset($values['param3'])) {
                $values['param3'] = $this->app->string->sluggify($values['param3']);
            }

            $data['variations'][] = $values;
        }

        $this->_element->bindData($data);

        return $this->_item;
    }
}
