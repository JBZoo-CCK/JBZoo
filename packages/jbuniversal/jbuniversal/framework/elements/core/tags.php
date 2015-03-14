<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCSVItemCoreTags
 */
class JBCSVItemCoreTags extends JBCSVItem
{
    /**
     * @return array
     */
    public function toCSV()
    {
        $params = JBModelConfig::model()->getGroup('export.items');

        if ((int)$params->merge_repeatable) {
            return implode(JBCSVItem::SEP_ROWS, $this->_item->getTags());
        } else {
            return $this->_item->getTags();
        }
    }

    /**
     * @param      $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        $tags = ($position == 1) ? array() : $this->_item->getTags();

        $tags = array_merge($tags, $this->_getArray($value, JBCSVItem::SEP_ROWS));

        $this->_item->setTags($tags);

        return $this->_item;
    }

}
