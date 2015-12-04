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
 * Class JBOrderHelper
 */
class JBOrderHelper extends AppHelper
{

    /**
     * Order list
     * @var array
     */
    private $_orderings = array(
        'priority'      => 'priority ASC',
        'rpriority'     => 'priority DESC',
        'id'            => 'id ASC',
        'rid'           => 'id DESC',
        'alpha'         => 'name ASC',
        'ralpha'        => 'name DESC',
        'alias'         => 'alias ASC',
        'ralias'        => 'alias DESC',
        'hits'          => 'hits ASC',
        'rhits'         => 'hits DESC',
        'date'          => 'created ASC',
        'rdate'         => 'created DESC',
        'mdate'         => 'modified ASC',
        'rmdate'        => 'modified DESC',
        'publish_up'    => 'publish_up ASC',
        'rpublish_up'   => 'publish_up DESC',
        'publish_down'  => 'publish_down ASC',
        'rpublish_down' => 'publish_down DESC',
        'author'        => 'author ASC',
        'rauthor'       => 'author DESC',
        'random'        => 'RAND()',
    );

    /**
     * Order list
     * @var array
     */
    private $_categoryOrderings = array(
        'alpha'     => 'name ASC',
        'ralpha'    => 'name DESC',
        'id'        => 'id ASC',
        'rid'       => 'id DESC',
        'alias'     => 'alias ASC',
        'ralias'    => 'alias DESC',
        'ordering'  => 'ordering ASC',
        'rordering' => 'ordering DESC',
        'random'    => 'RAND()',
    );

    /**
     * Order list
     * @var array
     */
    private $_lightOrderings = array(
        'priority'  => 'priority ASC',
        'rpriority' => 'priority DESC',
        'alpha'     => 'name ASC',
        'ralpha'    => 'name DESC',
        'hits'      => 'hits ASC',
        'date'      => 'created ASC',
        'rdate'     => 'created DESC',
        'mdate'     => 'modified ASC',
        'random'    => 'RAND()',
    );

    /**
     * Get order
     * @param string      $order
     * @param null|string $context
     * @param bool        $category
     * @return string
     */
    function get($order, $context = null, $category = false)
    {
        if ($category) {
            $order = isset($this->_categoryOrderings[$order]) ? $this->_categoryOrderings[$order] : $this->_categoryOrderings['rordering'];
        } else {
            $order = isset($this->_orderings[$order]) ? $this->_orderings[$order] : $this->_orderings['rpriority'];
        }


        if ($context && $order != 'RAND()') {
            $order = $context . '.' . $order;
        }

        return $order;
    }

    /**
     * Get order list
     * @return array
     */
    public function getOrderings()
    {
        return $this->_orderings;
    }

    /**
     * Get light ordger list
     */
    public function getLightOrderings()
    {
        return $this->_lightOrderings;
    }

    /**
     * Get order list
     * @param bool $showReverse
     * @return array
     */
    function getList($showReverse = true)
    {
        $result = array();
        foreach ($this->_orderings as $key => $order) {

            if (!$showReverse && $key != 'random' && $key[0] == 'r') {
                continue;
            }

            $result[$key] = JText::_('JBZOO_ORDER_' . trim(preg_replace('#[^a-z]#ius', '_', $order), '_'));
        }

        return $result;
    }


    /**
     * @param bool $showReverse
     * @return array
     */
    public function getCatOrderings($showReverse = true)
    {
        $result = array();
        foreach ($this->_categoryOrderings as $key => $order) {

            if (!$showReverse && $key != 'random' && $key[0] == 'r') {
                continue;
            }

            $result[$key] = JText::_('JBZOO_ORDER_' . trim(preg_replace('#[^a-z]#ius', '_', $order), '_'));

        }

        return $result;
    }

    /**
     * Convert order to usability view
     * @param $order
     * @return array
     */
    public function convert($order)
    {
        $result = array();
        foreach ($order as $orderRow) {
            preg_match('#_jbzoo_([0-9])_(.*?)_(.*)#', $orderRow, $matches);

            if (!empty($matches)) {
                list($all, $key, $innerKey, $value) = $matches;
                $result[$key][$innerKey] = $value;
            }

            if ($orderRow == 'random' || $orderRow == '_random') {
                $result[]['field'] = 'random';
            }

        }

        foreach ($result as $key => $orderRow) {
            if (!isset($orderRow['field']) || empty($orderRow['field'])) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    /**
     * @param $order
     * @param $prevResult
     * @return array
     */
    public function setItemOrder($order, $prevResult)
    {
        $jbtables = $this->app->jbtables;

        $orders = $this->convert($order);

        $joinList = $ol = $columns = array();

        $ran = $this->app->jbarray->recursiveSearch('random', $orders);
        if ($ran !== false) {
            return array('', ' RAND() ');
        }

        foreach ($orders as $orderParams) {

            $order = $this->getOrderDirection($orderParams['order']);

            if ($orderParams['field'] == 'corename') {
                $ol[] = 'a.name ' . $order;

            } elseif ($orderParams['field'] == 'corepriority') {
                $ol[] = 'a.priority ' . $order;

            } elseif ($orderParams['field'] == 'corealias') {
                $ol[] = 'a.alias ' . $order;

            } elseif ($orderParams['field'] == 'corecreated') {
                $ol[] = 'a.created ' . $order;

            } elseif ($orderParams['field'] == 'corehits') {
                $ol[] = 'a.hits ' . $order;

            } elseif ($orderParams['field'] == 'coremodified') {
                $ol[] = 'a.modified ' . $order;

            } elseif ($orderParams['field'] == 'corepublish_down') {
                $ol[] = 'a.publish_down ' . $order;

            } elseif ($orderParams['field'] == 'corepublish_up') {
                $ol[] = 'a.publish_up ' . $order;

            } elseif ($orderParams['field'] == 'coreauthor') {
                $ol[]                     = 'tJoomlaUsers.name ' . $order;
                $joinList['tJoomlaUsers'] = 'LEFT JOIN #__users AS tJoomlaUsers ON a.created_by = tJoomlaUsers.id';

            } elseif (strpos($orderParams['field'], '__')) {
                list ($elementId, $priceField) = explode('__', $orderParams['field']);

                $joinList['tSku'] = 'LEFT JOIN ' . ZOO_TABLE_JBZOO_SKU
                    . ' AS tSku ON tSku.item_id = a.id'
                    . ' AND tSku.element_id = \'' . $elementId . '\''
                    . ' AND tSku.param_id = \'' . $priceField . '\''
                    . ' AND `variant` = \'-1\'';

                $ol[] = 'tSku.value_n ' . $order;

            } else {
                $itemType = $this->app->jbentity->getItemTypeByElementId($orderParams['field']);

                if (!empty($itemType)) {

                    $tableName          = $jbtables->getIndexTable($itemType);
                    $tableSqlName       = 'tIndex' . str_replace('#__', '', $tableName);
                    $columns[$itemType] = $jbtables->getFields($tableName);

                    $elementId = $this->app->jbtables->getFieldName($orderParams['field'], $orderParams['mode']);
                    if (in_array($elementId, $columns[$itemType], true)) {
                        $joinList[$itemType] = ' LEFT JOIN ' . $tableName
                            . ' AS ' . $tableSqlName . ' ON a.id = ' . $tableSqlName . '.item_id'
                            . ' AND ' . $tableSqlName . '.' . $elementId . ' IS NOT NULL';

                        $ol[] = JFactory::getDbo()->quoteName($tableSqlName . '.' . $elementId) . $order;
                    }
                }
            }
        }

        if (!empty($ol)) {
            return array(' ' . implode(' ', $joinList) . ' ', implode(', ', $ol));

        } else if (!empty($prevResult)) {
            return $prevResult;
        }

        return array('', ' a.id ASC ');
    }

    /**
     * @param $dir
     * @return null|string
     */
    public function getOrderDirection($dir)
    {
        if (strtolower($dir) == 'asc') {
            return 'ASC';
        } else if (strtolower($dir) == 'desc') {
            return 'DESC';
        }

        return 'ASC';
    }

    /**
     * @param bool|false $isModule
     * @return mixed
     */
    public function getListAdv($isModule = false)
    {
        return $this->app->jbfield->getSortElementsOptionList(0, '', $isModule);
    }

    /**
     * @param string $elementId
     * @param string $type
     * @param string $appId
     * @return string
     */
    public function getNameById($elementId, $type = null, $appId = null)
    {
        if (strpos($elementId, '__')) {
            list($elementId, $paramId) = explode('__', $elementId);
        }

        if (isset($paramId)) {
            /** @type ElementJBPrice $element */
            if ($element = $this->app->jbentity->getElement($elementId, $type, $appId)) {
                if ($param = $element->getElementConfig($paramId)) {
                    return (isset($param['name']) ? $param['name'] : null);
                }
            }
        }

        return $this->app->jbentity->getFieldNameById($elementId);
    }
}