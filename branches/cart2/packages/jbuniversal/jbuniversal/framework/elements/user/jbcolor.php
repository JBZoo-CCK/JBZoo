<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JBCSVItemUserJBColor extends JBCSVItem
{
    /**
     * @var Object JBColorHelper
     */
    private $_jbcolor;

    /**
     * @param Element|String $element
     * @param Item           $item
     * @param array          $options
     */
    public function __construct($element, Item $item = null, $options = array())
    {
        parent::__construct($element, $item, $options);

        $this->_jbcolor = $this->app->jbcolor;
    }

    /**
     * @return string|void
     */
    public function toCSV()
    {
        $result = array();

        if (!empty($this->_value)) {

            $result = $this->_value['option'];
        }

        if ((int)$this->_exportParams->merge_repeatable) {
            return implode(JBCSVItem::SEP_ROWS, $result);
        }

        return $result;
    }

    /**
     * @param      $value
     * @param null $position
     * @return Item
     */
    public function fromCSV($value, $position = null)
    {
        if (strpos($value, JBCSVItem::SEP_ROWS)) {

            foreach ($this->_getArray($value) as $value) {
                $data['option'][] = $this->_getValuesData($value);
            }

        } else {
            $data             = ($position == 1) ? array() : $this->_element->data();
            $data['option'][] = $this->_getValuesData($value);
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
        $elementData = $this->_jbcolor->clean($elementData);
        $options     = $this->_getColors();

        if (strpos($elementData, '#') === 0) {

            list($label, $elementData) = explode('#', $elementData);
            $elementData = $this->_jbcolor->clean($elementData);

            foreach ($options as $label => $color) {

                if ($color == $this->_jbcolor->clean($elementData) ||
                    $label == $this->_jbcolor->clean($elementData)
                ) {
                    $value = $label;
                }

            }
        } elseif (strpos($elementData, '#')) {
            $this->_setNewOption($elementData);
            list($label, $elementData) = explode('#', $elementData);
            $value = $this->_jbcolor->clean($label);
        } else {
            $this->_setNewOption($elementData);
            $value = $this->_jbcolor->clean($elementData);
        }

        return !empty($value) ? $value : null;
    }

    /**
     * Add new option if not exists
     * @param string $elementData
     * @return bool
     */
    private function _setNewOption($elementData)
    {
        $importData = $this->app->jbsession->getGroup('import');
        if (isset($importData['checkOptions']) && (int)$importData['checkOptions'] == JBImportHelper::OPTIONS_YES) {
            return $this->app->jbtype->checkOptionColor($elementData, $this->_identifier, $this->_item->getType()->id, $this->_item->application_id);
        }
        return false;
    }

    /**
     * Get colors from element
     * @return array
     */
    private function _getColors()
    {
        $colors = $this->_element->config->get('colors');
        return $this->_jbcolor->parse($colors);
    }

}