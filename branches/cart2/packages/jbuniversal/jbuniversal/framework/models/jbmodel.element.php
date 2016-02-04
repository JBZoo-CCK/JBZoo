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
 * Class JBModelElement
 */
class JBModelElement extends JBModel
{
    /**
     * @var string
     */
    protected $_identifier = null;

    /**
     * @var JSONData
     */
    protected $_config = null;

    /**
     * @var Element
     */
    protected $_element = null;

    /**
     * @var string
     */
    protected $_itemType = null;

    /**
     * @param Element $element
     * @param int     $applicationId
     * @param string  $itemType
     */
    function __construct(Element $element, $applicationId, $itemType)
    {
        parent::__construct();

        $this->_element       = $element;
        $this->_itemType      = $itemType;
        $this->_config        = $element->config;
        $this->_identifier    = $element->identifier;
        $this->_applicationId = $applicationId;
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        return strtolower($this->_element->getElementType());
    }

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param string|array    $value
     * @param int             $i
     * @param bool            $exact
     * @return array
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        if ($exact) {
            return $this->_getWhereExact($select, $elementId, $value, $exact);
        } else {
            return $this->_getWhereLike($select, $elementId, $value, $exact);
        }
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
        if ($exact) {
            return $this->_getWhereExact($select, $elementId, $value, $exact);
        } else {
            return $this->_getWhereLike($select, $elementId, $value, $exact);
        }
    }

    /**
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param mixed           $value
     * @param bool            $exact
     * @return array
     */
    public function _getWhereExact(JBDatabaseQuery $select, $elementId, $value, $exact)
    {
        $values    = $this->_prepareValue($value, $exact);
        $fieldName = $this->_jbtables->getFieldName($elementId, 's');

        if (empty($values) && is_array($values)) {
            return array();
        } elseif ($values === null) {
            return array('tItem.id IN (0)');
        }

        $conditions = array();

        // $conditions[] = 'tIndex.' . $this->_jbtables->getFieldName($elementId, 's') . ' IS NOT NULL';

        foreach ($values as $valueOne) {
            if ($this->app->jbdate->isDate($valueOne)) {
                $conditions[] = 'tIndex.' . $fieldName . ' LIKE ' . $this->_quote('%' . $valueOne . '%');
            } else {
                $conditions[] = 'tIndex.' . $fieldName . ' = ' . $this->_quote($valueOne);
            }
        }

        $where = ' (' . implode(' OR ', $conditions) . ') ';

        $subSelect = $this->_getSelect()
            ->select('item_id')
            ->from($this->_jbtables->getIndexTable($this->_itemType) . ' AS tIndex')
            ->where('tIndex.' . $fieldName . ' IS NOT NULL')
            ->where($where);

        $rows = $this->fetchList($subSelect);

        if (!empty($rows)) {
            return array('tItem.id IN (' . implode(',', $rows) . ')');
        }

        return array('tItem.id IN (0)');
    }

    /**
     * @param JBDatabaseQuery $select
     * @param string          $elementId
     * @param mixed           $value
     * @param bool            $exact
     * @return array
     */
    public function _getWhereLike(JBDatabaseQuery $select, $elementId, $value, $exact)
    {
        $values    = $this->_prepareValue($value, $exact);
        $fieldName = $this->_jbtables->getFieldName($elementId, 's');

        if (empty($values) && is_array($values)) {
            return array();
        } elseif ($values === null) {
            return array('tItem.id IN (0)');
        }

        $conditions = array();

        // $conditions[] = 'tIndex.' . $this->_jbtables->getFieldName($elementId, 's') . ' IS NOT NULL';

        foreach ($values as $valueOne) {
            $conditions[] = $this->_buildLikeBySpaces($valueOne, 'tIndex.' . $fieldName) . PHP_EOL;
        }

        $where = ' (' . implode(' OR ', $conditions) . ') ';

        $subSelect = $this->_getSelect()
            ->select('item_id')
            ->from($this->_jbtables->getIndexTable($this->_itemType) . ' AS tIndex')
            ->where($where);

        $rows = $this->fetchList($subSelect);

        if (!empty($rows)) {
            return array('tItem.id IN (' . implode(',', $rows) . ')');
        }

        return array('tItem.id IN (0)');
    }

    /**
     * Prepare value
     * @param string|array $value
     * @param boolean      $exact
     * @return mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $value = array_filter($value);

        return $value;
    }

}
