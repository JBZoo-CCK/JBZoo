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
