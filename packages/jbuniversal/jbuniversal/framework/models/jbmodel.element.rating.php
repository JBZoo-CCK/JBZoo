<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBModelElementRating
 */
class JBModelElementRating extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Prepare and validate value
     * @param array|string $value
     * @param bool         $exact
     * @return array|mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        if (is_array($value)) {
            reset($value);
            $value = trim(current($value));
        }

        $result = $value;
        if (is_string($value) && strpos($value, '/')) {
            $result    = explode('/', $value);
            $result[0] = trim($result[0]);
            $result[1] = trim($result[1]);
        }

        return $result;
    }

    /**
     * Get conditions for search
     * @param $value
     * @param $elementId
     * @return array
     */
    protected function _getWhere($value, $elementId)
    {
        $value = $this->_prepareValue($value);

        $clearElementId = $this->_jbtables->getFieldName($elementId, 'n');

        $where = array();
        if (is_array($value)) {

            if ($value[0] == 0 && $this->_config->get('stars') == $value[1]) {
                return $where;
            }

            if (strlen($value[0]) != 0) {
                $where[] = 'tIndex.' . $clearElementId . ' >= ' . (float)$value[0];
            }

            if (strlen($value[1]) != 0) {
                $where[] = 'tIndex.' . $clearElementId . ' <= ' . (float)$value[1];
            }

        } else {
            $where[] = 'tIndex.' . $clearElementId . ' = ' . (float)$value;
        }

        return $where;
    }

}
