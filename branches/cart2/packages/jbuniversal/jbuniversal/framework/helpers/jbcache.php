<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCacheHelper
 */
class JBCacheHelper extends AppHelper
{
    /**
     * @var JCache
     */
    protected $_cache = null;

    /**
     * Check config, is enabled joomla caching
     * @return int
     */
    public function isEnabled()
    {
        $config = JFactory::getConfig();
        return (int)$config->get('caching', 0);
    }

    /**
     * Set data to cache storage by key
     * @param string $key
     * @param mixed  $data
     * @param string $group
     * @param bool   $isForce
     * @param array  $params
     * @return bool
     */
    public function set($key, $data, $group = 'default', $isForce = false, array $params = array())
    {
        $group = str_replace('-', '_', $group);
        $cache = JFactory::getCache('jbzoo_' . $group, 'output');
        $key   = $this->_simpleHash($key);
        if ($isForce) {
            $cache->setCaching(true);
        }

        if (isset($params['ttl']) && (int)$params['ttl'] > 0) {
            $cache->setLifeTime((int)$params['ttl']);
        }

        return $cache->store($data, $key);
    }

    /**
     * Get cache data by key
     * @param string $key
     * @param string $group
     * @param bool   $isForce
     * @param array  $params
     * @return null
     */
    public function get($key, $group = 'default', $isForce = false, array $params = array())
    {
        $group = str_replace('-', '_', $group);
        $cache = JFactory::getCache('jbzoo_' . $group, 'output');
        $key   = $this->_simpleHash($key);
        if ($isForce) {
            $cache->setCaching(true);
        }

        if (isset($params['ttl']) && (int)$params['ttl'] > 0) {
            $cache->setLifeTime((int)$params['ttl']);
        }

        return $cache->get($key);
    }

    /**
     * Clear cache
     * @param $group
     */
    public function clear($group)
    {
        $file = JPATH_SITE . '/cache/jbzoo/' . $group;
        if (JFile::exists($file)) {
            JFile::delete($file);
        }
    }

    /**
     * Create simple hash from var
     * @param mixed $var
     * @return string
     */
    protected function _simpleHash($var)
    {
        return serialize($var);
    }

    /**
     * @deprecated
     */
    public function start()
    {
        return null; // disibled
    }

    /**
     * @deprecated
     */
    public function stop()
    {
        return null; // disibled
    }
}
