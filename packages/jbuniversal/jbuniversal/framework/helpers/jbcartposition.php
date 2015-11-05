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
     * @param       $group
     * @param array $mustHave
     * @param array $forceElements
     * @return array
     */
    public function loadPositions($group, $mustHave = array(), $forceElements = array())
    {
        $jbcartelement    = $this->app->jbcartelement;
        $positionsConfigs = $this->_getConfig($group);

        $positions = array();
        foreach ($positionsConfigs as $posName => $elements) {

            if (empty($elements)) {
                continue;
            }

            $positions[$posName] = array();
            $posIndex            = 0;

            foreach ($elements as $elemConfig) {

                $element = null;
                if (isset($elemConfig['identifier'])) {
                    $identifier = $elemConfig['identifier'];
                } else {
                    continue;
                }

                if (!empty($forceElements)) {

                    // get from forced arg
                    foreach ($forceElements as $forceElement) {
                        if ($forceElement->identifier == $identifier) {
                            $element = $forceElement;
                            break;
                        }
                    }
                }

                if (!$element) {

                    // create new and bind configs
                    if ($element = $jbcartelement->create($elemConfig['type'], $elemConfig['group'])) {
                        $identifier = $elemConfig['identifier'];

                        if (!isset($elemConfig['name'])) {
                            $elemConfig['name'] = 'JBZOO_ELEMENT_CORE_' . $element->getElementType();
                        }

                        $element->identifier = $identifier;
                        $element->setConfig($elemConfig);
                    }
                }

                if ($element) {
                    $positions[$posName][$posIndex] = $element;
                    $posIndex++;
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
     * @param       $tmplGroup
     * @param       $configGroup
     * @param array $tmplPoitions
     * @return array
     */
    public function loadPositionsTmpl($tmplGroup, $configGroup, $tmplPoitions = array())
    {
        $positions = array();

        if (isset($tmplPoitions['positions'])) {

            $mustHave  = array_keys($tmplPoitions['positions']);
            $elements  = $this->loadElements($configGroup);
            $positions = $this->loadPositions($tmplGroup, $mustHave, $elements);
        }

        return $positions;
    }

    /**
     * @param string $tmplGroup
     * @param bool   $merge
     * @return array|JSONData
     */
    public function loadParams($tmplGroup, $merge = true, $resetIndexes = false)
    {
        $list = $this->_getConfig($tmplGroup);
        if (!$merge) {

            if ($resetIndexes) {
                $result = array();
                foreach ($list as $position => $items) {

                    if (!isset($result[$position])) {
                        $result[$position] = array();
                    }

                    foreach ($items as $item) {
                        $result[$position][] = $item;
                    }
                }

                return $result;
            }

            return $list;
        }

        $elements = array();
        foreach ($list as $items) {
            $elements = array_merge((array)$elements, (array)$items);
        }

        return $elements;
    }

    /**
     * @param $group
     * @return array
     */
    public function loadElements($group)
    {
        $list = $this->loadPositions($group);

        $elements = array();
        foreach ($list as $items) {
            $elements = array_merge($elements, $items);
        }

        return $elements;
    }


    /**
     * @param string $group
     * @param array  $positions
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
     * @param $group
     * @param $positions
     * @param $identifier
     * @param $layout
     */
    public function savePrice($group, $positions, $identifier, $layout = null)
    {
        $config      = JBModelConfig::model();
        $configGroup = $this->_mainGroup . $group;

        if ($identifier) {
            $configGroup .= '.' . $identifier;
        }

        if ($layout) {
            $configGroup .= '.' . $layout;
        }

        $config->removeGroup($configGroup);
        $config->setGroup($configGroup, $positions);
    }

    /**
     * @param ElementJBPrice $element
     * @return array
     */
    public function loadForPrice($element)
    {
        $data = $this->loadPositions(JBCart::ELEMENT_TYPE_PRICE . '.' . $element->identifier,
            array(JBCart::DEFAULT_POSITION));

        if (isset($data[$element->identifier])) {
            return $data[$element->identifier];
        }

        if (isset($data[JBCart::DEFAULT_POSITION])) {
            return $data[JBCart::DEFAULT_POSITION];
        }

        return array();
    }

    /**
     * @param array $positions
     * @param array $needleElements
     * @return mixed
     */
    public function filter($positions, $needleElements)
    {
        $needleList = array();
        foreach ($needleElements as $needleElement) {
            $needleList[] = $needleElement->identifier;
        }

        if ($needleList) {
            foreach ($positions as $position => $elements) {
                foreach ($elements as $elementId => $element) {

                    if (!in_array($element->identifier, $needleList, true)) {
                        unset($positions[$position][$elementId]);
                    }
                }
            }
        }

        return $positions;
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
