<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class ElementJBQuickView
 */
class ElementJBQuickView extends Element
{
    /**
     * Check, has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Render element
     * @param array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);

        if (!empty($this->_item)) {
            return $this->_getLink($params);
        }
        return null;
    }

    /**
     * @param $params
     * @return string
     */
    protected function _getLink($params)
    {
        static $jsDefined;

        $buttonText  = $params->get('button_text');
        $popupWidth  = (int)$params->get('popup_width', 600) ? $params->get('popup_width', 600) : 600;
        $popupHeight = (int)$params->get('popup_height', 400) ? $params->get('popup_height', 400) : 400;
        $scroll      = (int)$params->get('scroll_enable', 0) ? 'yes' : 'no';
        $autoSize    = (int)$params->get('auto_size_enable', 0) ? 'true' : 'false';

        $layout    = $params->get('layout') ? $params->get('layout') : 'quickview';
        $paramLink = $this->_getParamsLink($layout);

        if (empty($buttonText)) {
            $buttonText = JText::_('JBZOO_QUICKVIEW');
        }

        $this->app->jbassets->fancybox();

        $html = array();

        if (!isset($jsDefined)) {
            $html[] = '<script type="text/javascript">jQuery(function ($) {
                $("a.jbquickview-modal-window").fancybox({
                    type       : "iframe",
                    fitToView  : true,
                    width      : "' . $popupWidth . '",
                    height     : "' . $popupHeight . '",
                    autoSize   : ' . $autoSize . ',
                    iframe     : {
                        scrolling : "' . $scroll . '",
                        preload   : true
                    },
                    closeClick : false,
                    title      : null,
                    helpers   : {
                        overlay: { locked: false }
                    }
                });
            });</script>';
            $jsDefined = true;
        }

        $html[] = '<!--noindex--><a href="' . $paramLink . '" title="' . $this->_item->name . '"'
            . ' rel="nofollow" class="jbquickview-modal-window"><span class="quickview">'
            . $buttonText . '</span></a><!--/noindex-->';

        return implode("\n ", $html);
    }

    /**
     * Get url with param
     * @param string $layout
     * @return string
     */
    protected function _getParamsLink($layout = 'quickview')
    {
        $itemLink = $this->app->route->item($this->_item);

        if (strripos($itemLink, '?')) {
            $paramLink = '&amp;tmpl=component&amp;jbquickview=' . $layout;
        } else {
            $paramLink = '?tmpl=component&amp;jbquickview=' . $layout;
        }

        return JRoute::_($itemLink . $paramLink);
    }

    /**
     * Edit action
     * @return string
     */
    public function edit()
    {
        return false;
    }
}
