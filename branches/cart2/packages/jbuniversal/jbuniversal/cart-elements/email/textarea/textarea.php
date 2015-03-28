<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmailTextArea
 */
class JBCartElementEmailTextArea extends JBCartElementEmail
{
    /**
     * @type null
     */
    protected $_text = null;

    /**
     * Check elements value.
     * Output element or no.
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $text = $this->_renderText();

        return !empty($text);
    }

    /**
     * Render elements data
     * @param  array $params
     * @return null|string
     */
    public function render($params = array())
    {
        return $this->_renderText();
    }

    /**
     * @return string
     */
    public function _renderText()
    {
        if (is_null($this->_text)) {
            $text        = JString::trim($this->config->get('text'));
            $this->_text = $this->app->jbordermacros->renderText($text, $this->getOrder());
        }

        return $this->_text;
    }

}
