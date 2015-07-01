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
 * Class JBToolbarHelper
 */
class JBToolbarHelper extends AppHelper
{

    /**
     * Show toolbar buttons
     */
    public function toolbar()
    {
        $this->_customLink('jbzoosupport', 'JBZOO_BUTTON_SUPPORT', 'http://forum.jbzoo.com');
        $this->_separator();
    }

    /**
     * @return string
     */
    public function save()
    {
        $this->app->jbassets->addScript('
        Joomla.submitform = submitform = function() {
            var $ = jQuery,
                $jbform = $("#jbzooForm"),
                $jform = $("#adminForm");

            if ($jbform.length) {
                $jbform.submit();

            } else if ($jform.length) {
                $jform.submit();
            }

        }');

        return JToolbar::getInstance('toolbar')->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'save', false);
    }

    /**
     * Show button for popup window
     * @param string $icon
     * @param string $name
     * @param array $urlParams
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function _popup($icon, $name, array $urlParams, $width = 600, $height = 450)
    {
        $urlParams = array_merge(array(
            'option'     => 'com_zoo',
            'tmpl'       => 'component',
            'controller' => 'jbtools',
        ), $urlParams);

        $link = JRoute::_(JURI::root() . 'administrator/index.php?' . $this->app->jbrouter->query($urlParams), true, -1);

        return JToolBar::getInstance('toolbar')->appendButton('Popup', $icon, $name, $link, $width, $height);
    }

    /**
     * Show link-button
     * @param string $icon
     * @param string $name
     * @param array $urlParams
     * @return string
     */
    protected function _link($icon, $name, $urlParams)
    {
        $urlParams = array_merge(array(
            'option' => 'com_zoo',
            'tmpl'   => 'component'
        ), $urlParams);

        $link = JRoute::_(JURI::root() . 'administrator/index.php?' . $this->app->jbrouter->query($urlParams), true, -1);

        return JToolBar::getInstance('toolbar')->appendButton('Link', $icon, $name, $link);
    }

    /**
     * Show custom link-button
     * @param string $icon
     * @param string $name
     * @param string $link
     * @return string
     */
    protected function _customLink($icon, $name, $link)
    {
        return JToolBar::getInstance('toolbar')->appendButton('Link', $icon, $name, $link);
    }

    /**
     * Add seporator
     * @return string
     */
    protected function _separator()
    {
        JToolBar::getInstance('toolbar')->appendButton('Separator', 'spacer', '90');
    }
}
