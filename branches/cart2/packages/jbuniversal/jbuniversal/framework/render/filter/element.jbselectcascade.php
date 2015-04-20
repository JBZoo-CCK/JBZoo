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
 * Class JBFilterElementJBSelectcascade
 */
class JBFilterElementJBSelectcascade extends JBFilterElement
{
    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $selectInfo = $this->app->jbselectcascade->getItemList(
            $this->_config->get('select_names', ''),
            $this->_config->get('items', '')
        );

        return $this->app->jbhtml->selectCascade(
            $selectInfo,
            $this->_getName('%s'),
            $this->_value,
            $this->_attrs,
            $this->_getId()
        );
    }
}
