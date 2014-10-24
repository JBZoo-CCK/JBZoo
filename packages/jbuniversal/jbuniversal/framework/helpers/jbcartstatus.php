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
     * @param string $group
     * @param bool   $asKeyValue
     * @param bool   $addUndefined
     *
     * @return array
     */
    public function getList($group = JBCart::STATUS_ORDER, $asKeyValue = false, $addUndefined = true)
    {
        $this->init();

        $list = $this->_lists->get($group, array());

        if (!$asKeyValue) {
            return $list;
        }

        $result = array();

        if ($addUndefined) {
            $und                     = $this->getUndefined();
            $result[$und->getCode()] = $und->getName();
        }

        foreach ($list as $element) {
            $result[$element->getCode()] = $element->getName();
        }

        return $result;
    }

    /**
     * @param        $code
     * @param string $group
     *
     * @return null
     */
    public function getByCode($code, $group = JBCart::STATUS_ORDER)
    {
        $list = $this->getList($group);

        if (isset($list[$code])) {
            return clone($list[$code]);
        }

        return null;
    }

    /**
     * Get exists status list
     *
     * @param string $group
     *
     * @return array
     */
    public function getExistsList($group = JBCart::STATUS_ORDER)
    {
        $rows = JBModelOrder::model()->getStatusList($group);

        $result = array();

        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($element = $this->getByCode($row->status)) {
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
        $configs = array(
            'code' => self::UNDEFINED,
            'name' => JText::_('JBZOO_STATUS_UNDEFINED')
        );

        $status = $this->app->jbcartelement->create('custom', JBCart::ELEMENT_TYPE_STATUS, $configs);

        return $status;
    }

}
