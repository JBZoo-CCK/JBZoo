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
