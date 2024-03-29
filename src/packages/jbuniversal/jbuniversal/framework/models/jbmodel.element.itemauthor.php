<?php
use Joomla\String\StringHelper;
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
 * Class JBModelElementItemauthor
 */
class JBModelElementItemauthor extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value, $elementId);
    }

    /**
     * Get userId's by name (LIKE %%)
     * @param $name
     * @return array
     */
    protected function _getUserIdByName($name)
    {
        $select = $this->_getSelect()
            ->select('tUsers.id')
            ->from('#__users as tUsers')
            ->where('tUsers.name LIKE ?', '%' . $name . '%', 'OR')
            ->where('tUsers.id = ?', $name, 'OR');

        $users = $this->fetchAll($select);

        $result = $this->_groupBy($users, 'id');

        return $result;
    }

    /**
     * Check is user exists by userId
     * @param int $userId
     * @return bool
     */
    protected function _isUserExists($userId)
    {
        $select = $this->_getSelect()
            ->select('tUsers.id')
            ->from('#__users as tUsers')
            ->where('tUsers.id = ?', (int)$userId);

        $user = $this->fetchRow($select);

        return (isset($user->id)) ? true : false;
    }

    /**
     * Get conditions for search
     * @param $value
     * @return array
     */
    protected function _getWhere($value)
    {
        if ($this->_isUserExists($value)) {
            return array('tItem.created_by = ' . (int)$value);
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $conditions = array();
        foreach ($value as $oneValue) {

            $oneValue = StringHelper::trim($oneValue);
            if (empty($oneValue)) {
                continue;
            }

            $userIds = $this->_getUserIdByName($oneValue);
            if (!empty($userIds)) {
                $conditions[] = 'tItem.created_by IN (' . implode(', ', $userIds) . ')';
            }

            $conditions[] = 'tItem.created_by_alias LIKE ' . $this->_db->quote('%' . $oneValue . '%');
        }

        if (!empty($conditions)) {
            return array('( ' . implode(' OR ', $conditions) . ' )');
        }

        return array('tItem.id IN (0)');
    }
}
