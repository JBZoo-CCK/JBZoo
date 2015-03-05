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
 * Class JBFilterElementTextRange
 */
class JBFilterElementTextRange extends JBFilterElementText
{
    /**
     * Render HTML
     * @return string
     */
    function html()
    {
        $html = array();

        $values = array('', '');

        if (isset($this->_value['range'])) {

            if (!is_array($this->_value['range'])) {
                $values = explode('/', $this->_value['range']);

            } else if (is_array($this->_value['range'])) {
                $values = $this->_value['range'];
            }
        }

        $html[] = '<label for="' . $this->_getId('1') . '">' . JText::_('JBZOO_FROM') . '</label>';
        $html[] = $this->app->jbhtml->text(
            $this->_getName('0'),
            $values[0],
            $this->_attrs,
            $this->_getId('1')
        );

        $html[] = '<br />';

        $html[] = '<label for="' . $this->_getId('2') . '">' . JText::_('JBZOO_TO') . '</label>';
        $html[] = $this->app->jbhtml->text(
            $this->_getName('1'),
            $values[1],
            $this->_attrs,
            $this->_getId('2')
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * Get name
     * @param $postFix
     * @return string
     */
    protected function _getName($postFix = null)
    {
        return parent::_getName('range') . '[' . $postFix . ']';
    }
}
