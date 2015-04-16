<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBZooItemType
 */
abstract class JBZooItemType
{
    /**
     * @var App
     */
    public $app = null;

    /**
     * @var JRegistry
     */
    protected $_params = null;

    /**
     * Init Zoo
     * @param JRegistry $params
     */
    public function __construct(JRegistry $params)
    {
        $this->app     = App::getInstance('zoo');
        $this->_params = $params;
    }

    /**
     * @return mixed
     */
    abstract function getItems();
}