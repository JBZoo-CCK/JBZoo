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
 * Class JBCartForm
 */
class JBCartForm
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @var JSONData
     */
    protected $_config = null;

    /**
     * @param $app
     */
    function __construct($app)
    {
        $this->app = $app;

        $this->_elements = $this->app->jbcartposition->load('fields');
    }

    /**
     * Check if the user can access the submission
     * @param  JUser $user The user object
     * @return boolean
     */
    public function canAccess($user = null)
    {
        return $this->app->user->canAccess($user, $this->access);
    }


}
