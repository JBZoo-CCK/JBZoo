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
     * @param string $order
     * @param null|string $context
     * @param bool $category
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
        $curApp = $this->app->zoo->getApplication();
        if (method_exists($curApp, 'setItemOrder')) {
            return $curApp->setItemOrder($order, $prevResult);
        }

        return null;
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
     * @return array
     */
    public function getListAdv()
    {
        return $this->app->jbfield->getSortElementsOptionList(0, '');
    }

    /**
     * @param $elementId
     * @return string
     */
    public function getNameById($elementId)
    {
        if (strpos($elementId, '__')) {
            list($elementId, $params) = explode('__', $elementId);
        }

        return $this->app->jbentity->getFieldNameById($elementId);
    }
}