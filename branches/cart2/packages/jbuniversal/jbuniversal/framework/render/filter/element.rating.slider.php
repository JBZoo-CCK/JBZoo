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
 * Class JBFilterElementRatingSlider
 */
class JBFilterElementRatingSlider extends JBFilterElementRating
{

    /**
     * Render HTML
     * @return string
     */
    public function html()
    {
        $params = array(
            'min'  => 0,
            'max'  => $this->_config->get('stars'),
            'step' => 1,
        );

        return $this->app->jbhtml->slider(
            $params,
            $this->_value,
            $this->_getName(),
            $this->_getId()
        );

    }
}
