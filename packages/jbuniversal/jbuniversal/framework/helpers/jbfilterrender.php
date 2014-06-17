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
 * Class JBFilterRenderHelper
 */
class JBFilterRenderHelper extends AppHelper
{

    /**
     * @param $elementID
     * @param $type
     * @param $application
     * @return string
     */
    function getElementType($elementID, $type, $application)
    {
        $zooElement  = $this->getElement($elementID, $type, $application);
        $elementType = strtolower(get_class($zooElement));
        return $elementType;
    }

    /**
     * Mapping
     * @param $elementType
     * @return string
     */
    function map($elementType)
    {
        $elementType = str_replace('element', '', $elementType);
        switch ($elementType) {
            case 'text':
                $renderMethod = 'text';
                break;

            case 'radio':
            case 'select':
                $renderMethod = 'select';
                break;

            case 'checkbox':
                $renderMethod = 'checkbox';
                break;

            default:
                $renderMethod = 'text';
                break;
        }

        return $renderMethod;
    }


    /**
     * Build attributes
     * @param $params
     * @return string
     */
    function _buildAttrs($params)
    {
        return $this->app->jbhtml->buildAttrs($params);
    }

}