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

    protected $_list = null;

    /**
     * @return array
     */
    protected function init()
    {
        if (is_null($this->_list)) {

            $elements = $this->app->jbcartposition->loadElements(JBCart::ELEMENT_TYPE_STATUS);

            $this->_list = array();
            foreach ($elements as $element) {
                $this->_list[$element->getCode()] = $element;
            }
        }

        return $this->_list;
    }

    /**
     *
     */
    public function getList()
    {
        $this->init();
        return $this->_list;
    }

    /**
     * @param $code
     */
    /**
     * @param $code
     * @return null
     */
    public function getByCode($code)
    {
        $this->init();

        if (isset($this->_list[$code])) {
            return clone($this->_list[$code]);
        }

        return null;
    }

    /**
     * Get exists status list
     */
    public function getExistsList()
    {
        $rows = JBModelOrder::model()->getStatusList();

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

}

