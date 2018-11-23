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
 * Class JBPRiceFilterElementImageExists
 */
class JBPRiceFilterElementImageExists extends JBPriceFilterElement
{
    /**
     * Return html
     * @return null|string
     */
    public function html()
    {
        $options = $this->_getValues();
        unset($this->_attrs['id']);

        return $this->_html->radio(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId(null, true)
        );
    }

    /**
     * Get available values
     * @param null $type
     * @return array
     */
    public function _getValues($type = null)
    {
        $values  = (array)$this->_getDbValues();
        $default = array(
            1 => array(
                'text'  => JText::_('JBZOO_YES'),
                'value' => JBCartElementPriceImage::IMAGE_EXISTS,
                'count' => null
            ),
            0 => array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => JBCartElementPriceImage::IMAGE_NO_EXISTS,
                'count' => null
            )
        );

        foreach ($values as $key => $value) {
            if (isset($default[$value['text']])) {
                $values[$key]['text'] = $default[$value['text']]['text'];
            }
        }

        return $values;
    }
}
