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
 * Class JBCartElementEmailUser
 */
class JBCartElementEmailUser extends JBCartElementEmail
{
    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @return JUser
     */
    protected function _getUser()
    {
        return JFactory::getUser();
    }

    /**
     * Render elements data
     * @param  array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $juser    = $this->_getUser();
        $infotype = $params->get('infotype', 'name');

        $result = null;

        if ($infotype == 'username') {
            $result = JText::_('JBZOO_ELEMENT_EMAIL_USER_GUEST');
            if (!empty($juser->username)) {
                $result = $juser->username;
            }

        } else if ($infotype == 'name') {
            $result = JText::_('JBZOO_ELEMENT_EMAIL_USER_GUEST');
            if (!empty($juser->name)) {
                $result = $juser->name;
            }

        } else if ($infotype == 'userid') {
            $result = (int)$juser->id;

        } else if ($infotype == 'groupname') {
            $result = $this->_getGroupName(key($juser->groups));
        }

        return $result;
    }

    /**
     * Get group title by id
     * @param  $id
     * @return mixed
     */
    protected function _getGroupName($id)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('title')
            ->from('#__usergroups')
            ->where('id = "' . (int)$id . '"');

        $db->setQuery($query);
        $title = $db->loadResult();

        return $title;
    }

}
