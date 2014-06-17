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
 * Class JBModelFavorite
 */
class JBModelFavorite extends JBModel
{
    /**
     * Create and return self instance
     * @return JBModelFavorite
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Create table if it no exists
     */
    public function checkTable()
    {
        return $this->_jbtables->checkFavorite();
    }

    /**
     * Check is row exists
     * @param Item $item
     * @param JUser $user
     * @return int|null
     */
    public function isExists(Item $item, JUser $user = null)
    {
        $this->checkTable();

        $user = ($user) ? $user : JFactory::getUser();

        $select = $this->_getSelect()
            ->select('*')
            ->from(ZOO_TABLE_JBZOO_FAVORITE . ' AS tFavorite')
            ->where('tFavorite.item_id = ?', (int)$item->id)
            ->where('tFavorite.user_id = ?', (int)$user->id)
            ->limit(1);

        $row = $this->fetchRow($select);

        return ($row) ? $row->id : null;
    }

    /**
     * Toggle item status
     * @param Item $item
     * @param JUser $user
     * @return bool|null
     */
    public function toggleItem(Item $item, JUser $user = null)
    {
        $this->checkTable();

        $user = ($user) ? $user : JFactory::getUser();

        if ($user->id) {

            if ($rowId = $this->isExists($item, $user)) {
                $this->_removeItem($rowId);

                return false;
            } else {
                $this->_addItem($item->id, $user->id);

                return true;
            }

        }

        return null;
    }

    /**
     * Get all items
     * @param JUser $user
     * @return array
     */
    public function getAllItems(JUser $user = null)
    {
        $this->checkTable();

        $user = ($user) ? $user : JFactory::getUser();

        $select = $this->_getSelect()
            ->select('*')
            ->from(ZOO_TABLE_JBZOO_FAVORITE . ' AS tFavorite')
            ->where('tFavorite.user_id = ?', (int)$user->id);

        $result = array();
        if ($rows = $this->fetchAll($select, true)) {
            foreach ($rows as $row) {
                $result[$row['item_id']] = $row;
            }
        }

        return $result;
    }

    /**
     * Remove favorite items for user
     * @param JUser $user
     */
    public function removeItems(JUser $user = null)
    {
        $this->checkTable();

        $user = ($user) ? $user : JFactory::getUser();

        $this->_dbHelper->query(
            "DELETE FROM `" . ZOO_TABLE_JBZOO_FAVORITE . "` WHERE (`user_id` = '" . (int)$user->id . "');"
        );
    }

    /**
     * Save item to favorites
     * @param Int $itemId
     * @param Int $userId
     */
    protected function _addItem($itemId, $userId)
    {
        $this->checkTable();

        $this->_dbHelper->query(
            "INSERT INTO `" . ZOO_TABLE_JBZOO_FAVORITE . "` (`user_id`, `item_id`, `date`) "
            . "VALUES ('" . (int)$userId . "', '" . (int)$itemId . "', now())"
        );
    }

    /**
     * Remove item from favorites
     * @param Int $rowId
     */
    protected function _removeItem($rowId)
    {
        $this->checkTable();

        $this->_dbHelper->query(
            "DELETE FROM `" . ZOO_TABLE_JBZOO_FAVORITE . "` WHERE (`id` = '" . (int)$rowId . "');"
        );
    }

}
