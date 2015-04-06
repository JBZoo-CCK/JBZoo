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
 * Class JBFilterElementImageexists
 */
class JBFilterElementImageexists extends JBFilterElement
{
    /**
     * Return html
     * @return null|string
     */
    public function html()
    {
        $this->_isMultiple = false;

        $options = array(
            array(
                'text'  => JText::_('JBZOO_YES'),
                'value' => JBModelElementJBImage::IMAGE_EXISTS,
                'count' => null
            ),
            array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => JBModelElementJBImage::IMAGE_NO_EXISTS,
                'count' => null
            )
        );

        return $this->app->jbhtml->radio(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

}
