<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBPriceFilterElementCurrency
 */
class JBPriceFilterElementCurrency extends JBPriceFilterElement
{
    /**
     * Get main attrs
     * @param array $attrs
     * @return array
     */
    protected function _getAttrs(array $attrs)
    {
        $attrs = parent::_getAttrs($attrs);

        $attrs['maxlength'] = '255';
        $attrs['size']      = '60';

        //$attrs = $this->_addPlaceholder($attrs);

        return $attrs;
    }

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $options = $this->_getValues();

        return $this->_html->buttonsJqueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

    /**
     * Get DB values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        return $this->_getDbValues();
    }
}
