<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
     * User formats of name
     *
     * @var array
     */
    protected $_formats = array(
        'id'         => 'id',
        'name'       => 'name',
        'username'   => 'username',
        'full'       => array(
            'username', 'name'
        ),
        'full_id'    => 'id',
        'full_group' => 'group'
    );

    const USERNAME_FORMAT_FULL       = 'full';
    const USERNAME_FORMAT_FULL_GROUP = 'full_group';
    const USERNAME_FORMAT_FULL_ID    = 'full_id';

    /**
     * Render elements data
     *
     * @param  array $params
     * @return null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout('order.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'order'  => $this->getOrder(),
                'name'   => $this->toFormat()
            ));
        }

        return false;
    }

    /**
     * Get user formated name
     *
     * @param  null $format
     * @return mixed|string
     */
    public function toFormat($format = null)
    {
        if ($format === null) {
            $format = $this->config->get('format', 'name');
        }

        $userid = $this->getOrder()->created_by;
        $user   = JFactory::getUser($userid);
        $full   = $this->_toFullFormat();

        if ($userid === 0) {
            return JText::_('JBZOO_ORDER_CREATED_BY_GUEST');

        } else if ($format === self::USERNAME_FORMAT_FULL) {
            return $full;

        } else if ($format === self::USERNAME_FORMAT_FULL_GROUP) {

            $groupId = key($user->groups);
            $title   = $this->_getGroupTitle($groupId);

            return $full . ' (' . $title . ')';

        } else if ($format === self::USERNAME_FORMAT_FULL_ID) {
            return $full . ' (' . $user->get('id') . ')';
        }

        return $user->get($this->_formats[$format]);
    }

    /**
     * Get full format user name. USERNAME - NAME
     *
     * @return string
     */
    protected function _toFullFormat()
    {
        $created_by = $this->getOrder()->created_by;

        if ($created_by === 0) {
            return JText::_('JBZOO_ORDER_CREATED_BY_GUEST');
        }

        $user = JFactory::getUser($created_by);
        $name = $user->get('username') . ' - ' . $user->get('name');

        return $name;
    }

    /**
     * Get group title by id
     *
     * @param  $id
     * @return mixed
     */
    protected function _getGroupTitle($id)
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
