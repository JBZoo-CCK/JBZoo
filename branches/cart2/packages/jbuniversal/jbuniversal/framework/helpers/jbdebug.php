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
 * Class JBDebugHelper
 */
class JBDebugHelper extends AppHelper
{
    /**
     * JBDump instance
     * @var JBDump|JProfiler
     */
    protected static $_jbdump = null;

    /**
     * JBDump context
     * @var string
     */
    protected $_jbdumpContext = 'jbzoo';

    /**
     * JBDump params
     * @var array
     */
    protected $_jbdumpParams = array();

    /**
     * @param Application $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        return; // for debug only

        if (self::$_jbdump === null) {
            // Joomla standart profiler
            if (defined('JDEBUG') && JDEBUG) {
                self::$_jbdump = JProfiler::getInstance('Application');
            }

            // jbdump plugin
            if (class_exists('jbdump')) {
                self::$_jbdump = JBDump::i($this->_jbdumpParams);
            }
        }
    }

    /**
     * Set profiler mark
     * @param string $name
     */
    public function mark($name = '')
    {
        if (self::$_jbdump !== null) {
            self::$_jbdump->mark($name);
        }
    }

    /**
     * Dump sql queries
     * @param $select
     */
    public function sql($select)
    {
        if (self::$_jbdump !== null) {
            self::$_jbdump->sql((string)$select, 'jbdebug::sql');
        }
    }
}
