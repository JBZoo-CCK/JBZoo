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
 * Class JBCSVItemCoreAuthor
 */
class JBCSVItemCoreAuthor extends JBCSVItem
{
    /**
     * @return int
     */
    public function toCSV()
    {
        return $this->_item->getAuthor();
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|null
     */
    public function fromCSV($value, $position = null)
    {
        $value = $this->_getString($value);

        if ($id = $this->_getInt($value)) {
            $user = $this->app->user->get($id);

            if ($user && $user->id) {
                $this->_item->created_by = $user->id;
            }

        } else {
            $this->_item->created_by_alias = $value;
        }

        return $this->_item;
    }

}
