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
 * Class JBShippingHelper
 */
class JBShippingHelper extends AppHelper
{

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        $list = $this->app->jbcartposition->loadElements(JBCart::ELEMENT_TYPE_SHIPPING);

        return $list;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        $list = $this->app->jbcartposition->loadElements(JBCart::ELEMENT_TYPE_SHIPPINGFIELD);

        return $list;
    }

    /**
     * @return array
     */
    public function getConfigAssign()
    {
        $elements = $this->getEnabled();

        $result = array();
        foreach ($elements as $element) {
            $result[$element->identifier] = $element->config->get('shippingfields');
        }

        return $result;
    }

}
