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
 * Class JBVersionHelper
 */
class JBVersionHelper extends AppHelper
{
    /**
     * Get Zoo version
     * @param null $compareWirh
     * @return bool|string
     */
    public function zoo($compareWirh = null)
    {
        static $version;

        if (!isset($version)) {
            $version = $this->_getversionByXml('/administrator/components/com_zoo/zoo.xml');
        }

        return $version;
    }

    /**
     * Get Joomla version
     * @param null $compareWirh
     * @return bool|string
     */
    public function joomla($compareWirh = null)
    {
        return $this->_compareVersion(JVERSION, $compareWirh);
    }

    /**
     * Get JBZoo version
     * @param null $compareWirh
     * @return bool|string
     */
    public function jbzoo($compareWirh = null)
    {
        static $version;

        if (!isset($version)) {
            $version = $this->_getversionByXml('/media/zoo/applications/jbuniversal/application.xml');
        }

        return $version . ' GNU GPL (Community Edition) by <a href="https://github.com/SmetDenis" target="_blank">SmetDenis</a>';
    }

    /**
     * Get widgetkit version
     * @param null $compareWirh
     * @return bool|string
     */
    public function widgetkit($compareWirh = null)
    {
        static $version;

        if (!isset($version)) {
            $version = $this->_getversionByXml('/administrator/components/com_widgetkit/widgetkit.xml');
        }

        return $version;
    }

    /**
     * Get version by XML
     * @param $xml
     * @param null $compareWirh
     * @return bool|string
     */
    protected function _getVersionByXml($xml, $compareWirh = null)
    {
        if (JFile::exists(JPATH_SITE . $xml)) {
            $xml = simplexml_load_file(JPATH_SITE . $xml);
            return $this->_compareVersion((string)$xml->version, $compareWirh);
        }

        return null;
    }

    /**
     * Compare version
     * @param string $currentVersion
     * @param null $compareWith
     * @return bool|string
     */
    private function _compareVersion($currentVersion, $compareWith = null)
    {
        if ($compareWith) {
            $compareResult = version_compare($compareWith, $currentVersion);

            return ($compareResult <= 0) ? true : false;
        }

        return $currentVersion;
    }

}
