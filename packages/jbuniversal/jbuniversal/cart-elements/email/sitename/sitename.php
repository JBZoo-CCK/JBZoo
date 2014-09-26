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
 * Class JBCartElementEmailSiteName
 */
class JBCartElementEmailSiteName extends JBCartElementEmail
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
        $config   = $config = JFactory::getConfig();
        $sitename = JString::trim($config->get('sitename'));
        if (!empty($sitename)) {
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
        $config = JFactory::getConfig();
        if ($layout = $this->getLayout($params->get('_layout') . '.php')) {
            return self::renderLayout($layout, array(
                    'sitename' => $config->get('sitename')
                )
            );
        }

        return false;
    }
}
