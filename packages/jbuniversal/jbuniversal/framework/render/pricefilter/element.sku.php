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
        return $this->_html->text(
            $this->_getName(),
            $this->_value,
            $this->_attrs,
            $this->_getId()
        );
    }
}
