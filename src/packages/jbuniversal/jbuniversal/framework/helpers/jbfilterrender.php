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