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
     * Load positions group with elements
     * @param $group
     * @param array $mustHave
     * @return mixed
     */
    public function load($group, $mustHave = array())
    {
        $group            = trim(strtolower($group));
        $config           = JBModelConfig::model();
        $jbcartelement    = $this->app->jbcartelement;
        $positionsConfigs = $config->getGroup('cart.' . $group, array());

        $positions = array();
        foreach ($positionsConfigs as $posName => $elements) {

            if (empty($elements)) {
                continue;
            }

            $positions[$posName] = array();

            foreach ($elements as $elemConfig) {

                $identifier = $elemConfig['identifier'];
                $element    = $jbcartelement->create($elemConfig['type'], $elemConfig['group']);

                if ($element) {
                    // bind data
                    $element->identifier = $identifier;
                    $element->identifier = $elemConfig['identifier'];
                    $element->setConfig($elemConfig);

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
        }

        return $result;
    }

    /**
     * @param  string $group
     * @param array $positions
     */
    public function save($group, $positions)
    {
        $config = JBModelConfig::model();
        $config->setGroup('cart.' . $group, $positions);
    }

    /**
     * @param ElementJBPriceAdvance $element
     * @return array
     */
    public function loadForPrice(ElementJBPriceAdvance $element)
    {
        $data = $this->load('priceparams', array('list'));

        if (isset($data[$element->identifier])) {
            return $data[$element->identifier];
        }

        if (isset($data['list'])) {
            return $data['list'];
        }

        return array();
    }

}
