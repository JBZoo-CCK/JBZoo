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
 * Class JBAjaxHelper
 */
class JBCartPositionHelper extends AppHelper
{
    /**
     * @var string
     */
    protected $_mainGroup = 'cart.';


    /**
     * Load positions group with elements
     * @param $group
     * @param array $mustHave
     * @param array $forceElements
     * @return array
     */
    public function loadPostions($group, $mustHave = array(), $forceElements = array())
    {
        $jbcartelement    = $this->app->jbcartelement;
        $positionsConfigs = $this->_getConfig($group);

        $positions = array();
        foreach ($positionsConfigs as $posName => $elements) {

            if (empty($elements)) {
                continue;
            }

            $positions[$posName] = array();

            foreach ($elements as $elemConfig) {

                $element    = null;
                $identifier = $elemConfig['identifier'];

                if (!empty($forceElements)) {

                    // get from forced arg
                    if (isset($forceElements[$identifier])) {
                        $element = $forceElements[$identifier];
                    }

                } else {

                    // create new and bind configs
                    if ($element = $jbcartelement->create($elemConfig['type'], $elemConfig['group'])) {
                        $element->identifier = $elemConfig['identifier'];
                        $element->setConfig($elemConfig);
                    }
                }

                if ($element) {
                    $positions[$posName][$identifier] = $element;
                }
            }
        }

        // set order and simple validate
        $result = array();
        if (!empty($mustHave)) {
            foreach ($mustHave as $mustPosition) {

                $result[$mustPosition] = array();

                if (isset($positions[$mustPosition])) {
                    $result[$mustPosition] = $positions[$mustPosition];
                }
            }
        } else {
            $result = $positions;
        }

        return $result;
    }

    /**
     * @param $tmplGroup
     * @param $configGroup
     * @param array $tmplPoitions
     * @return array
     */
    public function loadPostionsTmpl($tmplGroup, $configGroup, $tmplPoitions = array())
    {
        $positions = array();

        if (isset($tmplPoitions['positions'])) {

            $mustHave  = array_keys($tmplPoitions['positions']);
            $elements  = $this->loadElements($configGroup);
            $positions = $this->loadPostions($tmplGroup, $mustHave, $elements);
        }

        return $positions;
    }

    /**
     * @param $tmplGroup
     * @return array
     */
    public function loadParams($tmplGroup)
    {
        $list = $this->_getConfig($tmplGroup);

        $elements = array();
        foreach ($list as $items) {
            $elements = array_merge($elements, $items);
        }

        return $elements;
    }

    /**
     * @param $group
     * @return array
     */
    public function loadElements($group)
    {
        $list = $this->loadPostions($group);

        $elements = array();
        foreach ($list as $items) {
            $elements = array_merge($elements, $items);
        }

        return $elements;
    }


    /**
     * @param  string $group
     * @param array $positions
     * @param string $layout
     */
    public function save($group, $positions, $layout = null)
    {
        $config = JBModelConfig::model();

        $configGroup = $this->_mainGroup . $group;
        if ($layout) {
            $configGroup .= '.' . $layout;
        }

        $config->removeGroup($configGroup);
        $config->setGroup($configGroup, $positions);
    }

    /**
     * @param ElementJBPriceAdvance $element
     * @return array
     */
    public function loadForPrice(ElementJBPriceAdvance $element)
    {
        $data = $this->loadPostions('priceparams', array('list'));

        if (isset($data[$element->identifier])) {
            return $data[$element->identifier];
        }

        if (isset($data['list'])) {
            return $data['list'];
        }

        return array();
    }

    /**
     * @param string $group
     * @return JSONData
     */
    protected function _getConfig($group)
    {
        $group = trim(strtolower($group));
        $model = JBModelConfig::model();

        $configs = $model->getGroup($this->_mainGroup . $group, array());

        return $configs;
    }

}
