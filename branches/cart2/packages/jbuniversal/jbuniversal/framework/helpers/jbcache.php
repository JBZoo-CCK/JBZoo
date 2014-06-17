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
 * Class JBCacheHelper
 */
class JBCacheHelper extends AppHelper
{
    /**
     * @var JCache
     */
    protected $_cache = null;

    /**
     * Start cache process
     * @deprecated
     * @param mixed $params
     * @param string $type
     * @return mixed
     */
    public function start($params = null, $type = null)
    {
        return null; // disibled
        !$type && $type = $this->app->jbrequest->get('view');
        !$type && $type = $this->app->jbrequest->get('task');

        $application = $this->app->zoo->getApplication();
        if ($application) {
            $group = 'jbzoo_' . $application->alias . '_' . $type;
        } else {
            $group = 'jbzoo_' . $type;
        }

        $this->_cache = JFactory::getCache($group, 'output');

        $result = $this->_cache->start($this->_getKey($params));

        return $result;
    }

    /**
     * Stop cache
     * @deprecated
     */
    public function stop()
    {
        return null; // disibled
        return $this->_cache->end();
    }

    /**
     * Create uniq cache key
     * @param array $params
     * @return string
     */
    public function _getKey($params = null)
    {
        $result   = array();
        $result[] = $this->app->jbwrapper->attrs();
        $result[] = serialize($params);
        $result[] = serialize($_GET);
        $result[] = $this->app->jbrequest->get('tmpl', 'index');
        $result[] = $this->app->jbrequest->get('page', 1);

        return implode('||', $result);
    }

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
     * @param mixed $data
     * @param string $group
     * @param bool $isForce
     * @param array $params
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
     * @param bool $isForce
     * @param array $params
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
        return sha1(serialize($var));
    }

}
