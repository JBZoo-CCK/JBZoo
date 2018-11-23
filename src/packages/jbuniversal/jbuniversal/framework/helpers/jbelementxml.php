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
 * Class JBElementXmlHelper
 */
class JBElementXmlHelper extends AppHelper
{
    /**
     * Valid extenstions for adding new XML params
     * @var array
     */
    private $_jbzooExtensions = array(
        'mod_jbzoo_search',
        'mod_jbzoo_props',
    );

    /**
     * Add XML params for element edit action
     * @param $element       Element
     * @param $params        array
     * @param $requestParams array
     * @return array
     */
    public function editElements($element, $params, $requestParams)
    {
        if ($addPath = $this->app->path->path('jbxml:element_edit/_default.xml')) {
            array_unshift($params, $addPath);
        }

        if ($addPath = $this->app->path->path('jbxml:element_edit/' . $element->getElementType() . '.xml')) {
            $params[] = $addPath;
        }

        return $params;
    }

    /**
     * Add XML params for element assign action
     * @param $element       Element
     * @param $params        array
     * @param $requestParams array
     * @return array
     */
    public function assignElements($element, $params, $requestParams)
    {
        $newParams = $params;
        if ($extName = $this->_getExtensionName($requestParams['path'])) {

            $newParams = array($params[0]);

            if ($addPath = $this->app->path->path('jbxml:' . $extName . '.xml')) {
                $newParams[] = $addPath;
            }

            if ($addPath = $this->app->path->path('jbxml:' . $extName . '/' . $element->getElementType() . '.xml')) {
                $newParams[] = $addPath;
            } else {
                $newParams[] = $this->app->path->path('jbxml:' . $extName . '/_default.xml');
            }

        }

        return $newParams;
    }

    /**
     * Get extension name
     * @param $path
     * @return null|string
     */
    private function _getExtensionName($path)
    {
        $path  = urldecode($path);
        $parts = explode('/', $path);

        foreach ($this->_jbzooExtensions as $extension) {
            if (in_array($extension, $parts)) {
                return $extension;
            }
        }

        return null;
    }
}