<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmailShopName
 */
class JBCartElementEmailShopName extends JBCartElementEmail
{
    /**
     * Check elements value.
     * Output element or no.
     *
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $shopname = JString::trim($this->_cartConfig->get('shop_name'));
        if (!empty($shopname)) {
            return true;
        }

        return false;
    }

    /**
     * Render elements data
     *
     * @param  array $params
     * @return null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout($params->get('_layout') . '.php')) {
            return self::renderLayout($layout, array(
                'shopname' => $this->_cartConfig->get('shop_name')
            ));
        }

        return false;
    }
}
