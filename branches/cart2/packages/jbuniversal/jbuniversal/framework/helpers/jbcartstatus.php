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
 * Class JBCartStatusHelper
 */
class JBCartStatusHelper extends AppHelper
{

    const UNDEFINED = 'undefined';

    /**
     * @var JSONData
     */
    protected $_lists = null;

    /**
     * @return array
     */
    protected function init()
    {
        if (is_null($this->_lists)) {

            $elements = $this->app->jbcartposition->loadPositions(JBCart::ELEMENT_TYPE_STATUS);

            $this->_lists = array();
            foreach ($elements as $groupName => $list) {

                $this->_lists[$groupName] = array();

                foreach ($list as $element) {
                    $this->_lists[$groupName][$element->getCode()] = $element;
                }
            }

            $this->_lists = $this->app->data->create($this->_lists);

            return $this->_lists;
        }
    }

    /**
     * @param string      $group
     * @param bool|false  $asKeyValue
     * @param bool|true   $addUndefined
     * @param JBCartOrder $order
     * @return array|mixed
     */
    public function getList($group = JBCart::STATUS_ORDER, $asKeyValue = false, $addUndefined = true, JBCartOrder $order = null)
    {
        $this->init();

        /** @var AppData $statusList */
        $statusList = $this->_lists;
        if ($order) {
            $statusList = $this->_getListByOrder($order);
            if (!$statusList) {
                $statusList = $this->_lists;
            }
        }

        $list = $statusList->get($group, array());

        if (!$asKeyValue) {
            return $list;
        }

        $result = array();

        if ($addUndefined) {
            $und = $this->getUndefined();

            $result[$und->getCode()] = $und->getName();
        }

        /** @var JBCartElementStatus $element */
        foreach ($list as $element) {
            $result[$element->getCode()] = $element->getName();
        }

        return $result;
    }

    /**
     * @param string      $code
     * @param string      $group
     * @param JBCartOrder $order
     * @return null
     */
    public function getByCode($code, $group = JBCart::STATUS_ORDER, JBCartOrder $order = null)
    {
        $list = $this->getList($group, false, true, $order);

        if (isset($list[$code])) {
            return clone($list[$code]);
        }

        return null;
    }

    /**
     * Get exists status list
     * @param string      $group
     * @param JBCartOrder $order
     * @return array
     */
    public function getExistsList($group = JBCart::STATUS_ORDER, JBCartOrder $order = null)
    {
        $rows = JBModelOrder::model()->getStatusList($group);

        $result = array();

        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($element = $this->getByCode($row->status, JBCart::STATUS_ORDER, $order)) {
                    $result[$row->status] = $element->getName();
                } else {
                    $result[$row->status] = $row->status;
                }
            }
        }

        return $result;
    }

    /**
     * Create undefined status element (as default)
     * @return JBCartElementStatusCustom
     */
    public function getUndefined()
    {
        $status = $this->app->jbcartelement->create('custom', JBCart::ELEMENT_TYPE_STATUS, array(
            'code' => self::UNDEFINED,
            'name' => JText::_('JBZOO_STATUS_UNDEFINED'),
        ));

        return $status;
    }

    /**
     * @param JBCartOrder $order
     * @return AppData|null
     */
    protected function _getListByOrder(JBCartOrder $order)
    {
        if (!$order || !$order->params) {
            return null;
        }

        $paramList = $order->params->find('status');

        /** @var JBCartElementHelper $jbelement */
        $jbelement = $this->app->jbcartelement;

        $result = array();

        if (is_array($paramList) && count($paramList) > 0) {
            foreach ($paramList as $key => $groups) {
                foreach ($groups as $group => $config) {
                    $result[$key][$config['code']] = $jbelement->create('custom', JBCart::ELEMENT_TYPE_STATUS, array(
                        'name'       => $config['name'],
                        'code'       => $config['code'],
                        'identifier' => $config['identifier'],
                    ));
                }
            }

            return $this->app->data->create($result);
        }

        return null;
    }

}
