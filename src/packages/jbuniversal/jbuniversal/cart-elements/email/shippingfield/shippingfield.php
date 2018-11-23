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
 * Class JBCartElementEmailShippingField
 */
class JBCartElementEmailShippingField extends JBCartElementEmail
{
    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $shippingfields = $this->getOrder()->getShippingFields();
        return !empty($shippingfields);
    }
}
