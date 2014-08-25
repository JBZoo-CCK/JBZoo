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
 * Class JBCartValidatorHelper
 */
class JBCartValidatorHelper extends AppHelper
{

    const EVENT_BEFORE_CREATE = 'before-create';

    /**
     * @param string $eventType
     * @param JBCartOrder $order
     * @return array
     */
    public function getByEvent($eventType = null, $order)
    {
        $result = array();

        $eventType = 'list'; // TODO add new evenet types
        $params    = JBModelConfig::model()->get($eventType, array(), 'cart.validator');

        if (!empty($params)) {
            $result = array();
            foreach ($params as $elementParams) {
                $result[$elementParams['identifier']] = $order->getValidatorElement($elementParams['identifier']);
            }

        }

        return $result;
    }

}
