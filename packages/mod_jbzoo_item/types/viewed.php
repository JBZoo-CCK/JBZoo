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
 * Class JBZooModItemViewed
 */
class JBZooModItemViewed extends JBZooItemType
{
    /**
     * @return array
     */
    public function getItems()
    {
        $types = $this->_params->get('recently_type', array());
        $order = $this->_params->get('order_default', array());
        $limit = $this->_params->get('pages', 20);

        $items = $this->app->jbviewed->getList($types, $order, $limit);

        return $items;
    }
}