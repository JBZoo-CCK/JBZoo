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
 * Class JBSessionHelper
 */
class JBSessionHelper extends AppHelper
{
    /**
     * @var string
     */
    protected $_namespace = 'jbzoo';

    /**
     * @var JSession
     */
    protected $_session = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_session = JFactory::getSession();
    }

    /**
     * Set value to session
     * @param $key
     * @param $value
     * @param string $group
     * @return mixed
     */
    public function set($key, $value, $group = 'default')
    {
        $data = $this->_session->get($group, array(), $this->_namespace);

        $data[$key] = $value;

        return $this->_session->set($group, $data, $this->_namespace);
    }

    /**
     * Get value from session
     * @param string $key
     * @param string $group
     * @param null $default
     * @return JSONData
     */
    public function get($key, $group = 'default', $default = null)
    {
        $data = $this->getGroup($group, $default);

        if (isset($data[$key])) {
            return $data[$key];
        }

        return $default;
    }

    /**
     * Get group data from session
     * @param string $group
     * @param mixed $default
     * @return JSONData
     */
    public function getGroup($group = 'default', $default = array())
    {
        $data = $this->_session->get($group, $default, $this->_namespace);

        return $data;
    }

    /**
     * Set group data from session
     * @param $data
     * @param string $group
     * @return mixed
     */
    public function setGroup($data, $group = 'default')
    {
        $this->clearGroup($group);

        foreach ($data as $key => $value) {
            $this->set($key, $value, $group);
        }
    }

    /**
     * Set group data from session
     * @param $data
     * @param string $group
     * @return mixed
     */
    public function setBatch($data, $group = 'default')
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value, $group);
        }
    }

    /**
     * Clear group in session
     * @param string $group
     * @return mixed
     */
    public function clearGroup($group = 'default')
    {
        return $this->_session->clear($group, $this->_namespace);
    }

    /**
     * Clear value in group
     * @param $key
     * @param string $group
     * @return mixed
     */
    public function clearValue($key, $group = 'default')
    {
        return $this->_session->set($key, null, $group);
    }
}
