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
 * Class JBUserHelper
 */
class JBUserHelper extends AppHelper
{

    const PARAM_NAMESPACE = 'jbzoo';

    /**
     * @var JUser
     */
    protected $_user = null;

    /**
     * @type JTableUser
     */
    protected $_table = null;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->_user  = JFactory::getUser();
        $this->_table = $this->_user->getTable();
    }


    /**
     * Set param to user storage
     * @param string $key
     * @param mixed  $value
     * @return bool
     */
    public function setParam($key, $value)
    {
        $params = (array)$this->_user->getParam(self::PARAM_NAMESPACE, array());

        $params[$key] = $value;

        $this->_user->setParam(self::PARAM_NAMESPACE, $params);

        return $this->_user->save(true);
    }

    /**
     * Get param from user storage
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $params = (array)$this->_user->getParam(self::PARAM_NAMESPACE, array());

        if (isset($params[$key])) {
            return $params[$key];
        }

        return $default;
    }

    /**
     * Purge all JBZoo params
     * @return bool
     */
    public function purgeParams()
    {
        $this->_user->setParam(self::PARAM_NAMESPACE, array());

        return $this->_user->save(true);
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->_table->getFields();
    }
}
