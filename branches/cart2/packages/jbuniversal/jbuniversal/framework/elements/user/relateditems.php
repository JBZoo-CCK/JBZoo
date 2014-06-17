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
 * Class JBCSVItemUserRelateditems
 */
class JBCSVItemUserRelateditems extends JBCSVItem
{
    /**
     * @return null|string
     */
    public function toCSV()
    {
        $result = array();

        if (isset($this->_value['item'])) {

            $items = JBModelItem::model()->getZooItemsByIds($this->_value['item']);
            foreach ($items as $item) {
                $result[] = $item->alias;
            }
        }

        return implode(JBCSVItem::SEP_ROWS, $result);
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $itemsAlias = $this->_getArray($value, JBCSVItem::SEP_ROWS, 'alias');

        $result = array();
        foreach ($itemsAlias as $alias) {

            if ($item = JBModelItem::model()->getByAlias($alias, $this->_item->application_id)) {
                $result[] = $item->id;
            }
        }

        $result = array_unique($result);

        $this->_element->bindData(array('item' => $result));
    }

}
