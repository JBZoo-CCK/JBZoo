<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
