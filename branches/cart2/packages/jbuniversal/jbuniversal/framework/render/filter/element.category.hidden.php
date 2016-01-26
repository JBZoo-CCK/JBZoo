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
 * Class JBFilterElementHidden
 */
class JBFilterElementCategoryHidden extends JBFilterElementCategory
{

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $value = (int)$this->_params->get('jbzoo_filter_default', 0);
        if (!$value) {
            $value = $this->app->jbrequest->getSystem('category', '');
        }

        $this->_isMultiple = false;
        return $this->app->jbhtml->hidden(
            $this->_getName(),
            $value,
            $this->_attrs,
            $this->_getId()
        );
    }
}
