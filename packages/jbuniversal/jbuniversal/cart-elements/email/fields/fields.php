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
 * Class JBCartElementEmailOrderFields
 */
class JBCartElementEmailFields extends JBCartElementEmail
{
    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $fields = $this->_getFields();
        return !empty($fields);
    }

    /**
     * @return array
     */
    protected function _getFields()
    {
        $orderFields = $this->_order->getFields();
        $selected    = $this->config->get('fields', array());

        $result = array();
        foreach ($selected as $elementId) {
            if ($element = $orderFields->get($elementId)) {
                $result[$elementId] = $element;
            }
        }

        return $result;
    }

}
