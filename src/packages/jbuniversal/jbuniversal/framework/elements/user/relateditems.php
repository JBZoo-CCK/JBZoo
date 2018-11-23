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
