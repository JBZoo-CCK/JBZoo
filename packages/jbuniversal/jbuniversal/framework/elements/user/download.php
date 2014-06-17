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
 * Class JBCSVItemUserDownload
 */
class JBCSVItemUserDownload extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        if (isset($this->_value['file'])) {
            return $this->_value['file'];
        }

        return '';
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $value = $this->_getString($value);
        $this->_element->bindData(array('file' => $value));

        return $this->_item;
    }

}
