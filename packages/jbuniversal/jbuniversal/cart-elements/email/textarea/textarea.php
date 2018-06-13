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
