<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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
