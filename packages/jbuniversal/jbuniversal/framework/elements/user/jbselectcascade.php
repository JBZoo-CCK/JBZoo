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
 * Class JBCSVItemUserJBSelectCascade
 */
class JBCSVItemUserJBSelectCascade extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();

        if (!empty($this->_value)) {
            foreach ($this->_value as $self) {
                $result[] = implode(JBCSVItem::SEP_CELL, $self);
            }
        }

        if ((int)$this->_exportParams->get('merge_repeatable')) {
            return implode(JBCSVItem::SEP_ROWS, $result);
        } else {
            return $result;
        }
    }

    /**
     * @param $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        if(JString::trim($value) === '') {
            return $this->_item;
        }

        if (strpos($value, JBCSVItem::SEP_ROWS)) {

            $tmpData = $this->_getArray($value, JBCSVItem::SEP_ROWS);

            foreach ($tmpData as $elementData) {
                $data[] = $this->_getValuesData($elementData);
            }

        } else {

            $data = ($position == 1) ? array() : $this->_element->data();

            $data[] = $this->_getValuesData($value);
        }

        $this->_element->bindData($data);

        return $this->_item;
    }

    /**
     * @param $elementData
     * @return mixed
     */
    private function _getValuesData($elementData)
    {
        $importData = $this->_lastImportParams->get('previousparams');
        $valuesTmp  = $this->_getArray($elementData, JBCSVItem::SEP_CELL);

        foreach ($valuesTmp as $key => $value) {
            $values['list-' . $key] = $value;
        }

        if (isset($importData['checkOptions']) && (int)$importData['checkOptions'] == JBImportHelper::OPTIONS_YES) {
            $this->app->jbtype->checkOptionCascade($values, $this->_identifier, $this->_item->getType()->id, $this->_item->application_id);
        }

        return $values;
    }
}
