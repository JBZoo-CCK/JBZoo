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
