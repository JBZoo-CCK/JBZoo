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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class pkg_jbzooInstallerScript
 */
class pkg_jbzooInstallerScript
{

    /**
     * @param $parent
     */
    public function install($parent)
    {
    }

    /**
     * @param $parent
     */
    public function uninstall($parent)
    {
    }

    /**
     * @param $type
     * @param $parent
     * @return bool
     */
    public function preflight($type, $parent)
    {
        $messages = [];

        // check webserver
        if (!extension_loaded('ionCube Loader')) {
            $messages[] = 'On your web server must be installed PHP-module '
                . '<a href="http://www.ioncube.com/loaders.php" target="_blank">ionCube Loader</a>. '
                . 'Please, check for it and try to install again.';
        }

        if (version_compare(PHP_VERSION, '5.3.1', '<')) {
            $messages[] = 'Your host needs to use PHP 5.3.0 or higher to run this version of JBZoo!';
        }

        // check installed Zoo
        if (!JFolder::exists(JPATH_ROOT . '/components/com_zoo')) {
            $messages[] = 'Please, first of all, you need to install <a href="http://www.yootheme.com/zoo" target="_blank">YOOtheme Zoo (free)</a>. After this, please, try again.';
        }

        // no auto update!
        if (JFolder::exists(JPATH_ROOT . '/media/zoo/applications/jbuniversal')) {
            $messages[] = 'JBZoo is already installed. ' .
                'Please, <a href="http://server.jbzoo.com/download#patches" target="_blank">use patches</a> for update!';
        }

        if (!empty($messages)) {
            $message = implode("<br /><br /> \n", $messages);
            $parent->getParent()->abort('<strong style="font-size:14px;color:red;">' . $message . '</strong>');
            return false;
        }
    }

    /**
     * @param $parent
     */
    public function update($parent)
    {
    }

    /**
     * @param $type
     * @param $parent
     * @param $results
     */
    public function postflight($type, $parent, $results)
    {
        self::_enablePlugin('jbzoo');
    }

    /**
     * Enable plugin by name
     * @param $plugin
     */
    private static function _enablePlugin($plugin)
    {
        $db = JFactory::getDbo();
        $db->setQuery('UPDATE #__extensions SET enabled = 1 WHERE element = "' . $plugin . '"');
        $db->execute();
    }
}
