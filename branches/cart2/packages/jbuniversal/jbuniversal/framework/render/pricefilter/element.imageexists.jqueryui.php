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
 * Class JBPRiceFilterElementImageExistsJqueryUI
 */
class JBPRiceFilterElementImageExistsJqueryUI extends JBPriceFilterElement
{
    /**
     * Return html
     * @return null|string
     */
    public function html()
    {
        $options = $this->_getValues();
        unset($this->_attrs['id']);

        return $this->_html->buttonsJQueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId(time(), true)
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
                'value' => 1,
                'count' => null
            ),
            0 => array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => 0,
                'count' => null
            )
        );

        foreach ($values as $key => $value) {
            if (isset($default[$value['text']])) {
                $values[$key]['text']  = $default[$value['text']]['text'];
                $values[$key]['value'] = $default[$value['text']]['value'];
            }
        }

        return $values;
    }
}
