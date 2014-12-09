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
        $options = array(
            array(
                'text'  => JText::_('JBZOO_YES'),
                'value' => JBCartElementPriceImage::IMAGE_EXISTS,
                'count' => null
            ),
            array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => JBCartElementPriceImage::IMAGE_NO_EXISTS,
                'count' => null
            )
        );

        return $this->html->buttonsJQueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

}
