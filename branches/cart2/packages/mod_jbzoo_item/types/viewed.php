<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
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