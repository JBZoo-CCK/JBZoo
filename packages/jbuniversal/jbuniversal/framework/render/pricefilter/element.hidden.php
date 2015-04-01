<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBPriceFilterElementHidden
 */
class JBPriceFilterElementHidden extends JBPriceFilterElement
{

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $html = array();

        if (is_array($this->_value)) {

            unset($this->_attrs['multiple']);
            unset($this->_attrs['size']);

            foreach ($this->_value as $key => $value) {
                $html[] = $this->_html->hidden(
                    $this->_getName(),
                    $value,
                    $this->_attrs,
                    $this->_getId($key)
                );
            }

        } else {
            $html[] = $this->_html->hidden(
                $this->_getName(),
                $this->_value,
                $this->_attrs,
                $this->_getId()
            );
        }

        return implode(PHP_EOL, $html);
    }

}
