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
 * Class JBPriceFilterElementValueHidden
 */
class JBPriceFilterElementValueHidden extends JBPriceFilterElementValue
{

    /**
     * @return string
     */
    public function html()
    {
        $value = $this->_prepareValues();
        return $this->_html->hidden(
            $this->_getName('value'),
            $value['value'],
            'class="jbprice-filter-value"',
            $this->_getId()
        );
    }
}
