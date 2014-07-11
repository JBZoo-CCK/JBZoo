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
 * Class CartRenderer
 */
class OrderRenderer extends PositionRenderer
{

    /**
     * @param string $dir
     * @param null $item
     * @return bool|void
     */
    public function checkPosition($dir, $item = null)
    {

    }

    /**
     * @param string $position
     * @param array $args
     * @return string|void
     */
    public function renderPosition($position, $args = array())
    {

    }

    /**
     * @param string $dir
     * @return array
     */
    public function getLayouts($dir)
    {
        // init vars
        $layoutList = array();
        $parts      = explode('.', $dir);
        $path       = implode('/', $parts);

        // parse positions xml
        if ($xml = simplexml_load_file($this->_getPath($path . '/' . $this->_xml_file))) {

            $layouts = $xml->xpath('positions[@layout]');

            foreach ($layouts as $layout) {

                $name = (string)$layout->attributes()->layout;

                $layoutList[$name] = $name;
            }

        }

        return $layoutList;
    }

}
