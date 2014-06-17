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
 * Class JBFilterElementJqueryui
 */
class JBFilterElementJqueryui extends JBFilterElement
{
    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $values = $this->_getValues();

        return $this->app->jbhtml->buttonsJqueryUI(
            $this->_createOptionsList($values),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        return $this->_getDbValues();
    }

}
