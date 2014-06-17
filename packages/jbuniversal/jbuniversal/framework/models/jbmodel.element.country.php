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
 * Class JBModelElementCountry
 */
class JBModelElementCountry extends JBModelElement
{

    /**
     * Prepare value
     * @param $value
     * @param $exact
     * @return mixed
     */
    protected function _prepareValue($value, $exact = false)
    {
        $values = $value;

        if (empty($values)) {
            return array();
        }

        if ($exact) {

            if (!is_array($values)) {
                $values = array($values);
            }

            return $values;

        } else {
            $countryMap = $this->_getCountries();

            $result = array();
            if (!is_array($values)) {
                $values = array($values);
            }

            foreach ($values as $value) {
                if ($key = array_search(JText::_($value), $countryMap)) {
                    $result[] = JText::_($this->app->country->isoToName($key));
                }
            }

            return $result;
        }
    }

    /**
     * Get values
     * @return array
     */
    private function _getCountries()
    {
        $selectableCountries = $this->_config->get('selectable_country', array());

        $countries = $this->app->country->getIsoToNameMapping();
        $keys      = array_flip($selectableCountries);
        $countries = array_intersect_key($countries, $keys);

        $result = array();
        foreach ($countries as $key => $country) {
            $translite    = JText::_($country);
            $result[$key] = $translite;
        }

        return $result;
    }
}
