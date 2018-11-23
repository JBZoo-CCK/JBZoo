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

        // check PHP version
        if (PHP_VERSION_ID < 50500) {
            $messages[] = 'Your host needs to use PHP 5.5.0 or higher to run this version of JBZoo!';
        }

        // check installed Zoo
        if (!JFolder::exists(JPATH_ROOT . '/components/com_zoo')) {
            $messages[] = 'Please, first of all, you need to install <a href="http://www.yootheme.com/zoo" target="_blank">YOOtheme Zoo (free)</a>. After this, please, try again.';
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
        $db->setQuery('UPDATE #__extensions SET enabled = 1 WHERE element = "' . trim($plugin) . '"');
        $db->execute();
    }
}
