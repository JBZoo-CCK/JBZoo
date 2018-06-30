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
 * Class JBCSVItemCoreAlias
 */
class JBCSVItemCoreAlias extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        return $this->_item->alias;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {

        if ($alias = $this->_getAlias($value)) {
            $this->_item->alias = $this->app->alias->item->getUniqueAlias($this->_item->id, $alias);
        }

        return $this->_item;
    }

}
