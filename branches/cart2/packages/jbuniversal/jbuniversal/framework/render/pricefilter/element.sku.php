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
 * Class JBPriceFilterElementSku
 */
class JBPriceFilterElementSku extends JBPriceFilterElement
{
    /**
     * Get main attrs
     *
     * @param array $attrs
     *
     * @return array
     */
    protected function _getAttrs(array $attrs)
    {
        $attrs = parent::_getAttrs($attrs);

        $attrs['maxlength'] = '255';
        $attrs['size']      = '60';

        $attrs = $this->_addPlaceholder($attrs);

        return $attrs;
    }

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        return $this->html->text(
            $this->_getName(),
            $this->_value,
            $this->_attrs,
            $this->_getId('sku-')
        );
    }
}
