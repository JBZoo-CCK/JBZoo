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

        if (!empty($this->_item) && $layout = $this->getLayout('button.php')) {
            $buttonData    = $this->_buttonData($params);
            $dataForLayout = $this->app->data->create($buttonData);

            return $this->renderLayout($layout, array(
                'quickView' => $dataForLayout
            ));
        }

        return null;
    }

    /**
     * @param $params
     * @return string
     */
    protected function _buttonData($params)
    {
        $buttonText  = $params->get('button_text');
        $popupWidth  = (int)$params->get('popup_width', 600) ? $params->get('popup_width', 600) : 600;
        $popupHeight = (int)$params->get('popup_height', 400) ? $params->get('popup_height', 400) : 400;
        $scroll      = (int)$params->get('scroll_enable', 0) ? 'yes' : 'no';
        $autoSize    = (int)$params->get('auto_size_enable', 0) ? 'true' : 'false';

        $layout  = $params->get('layout') ? $params->get('layout') : 'quickview';
        $itemUrl = $this->_getParamsLink($layout);

        if (empty($buttonText)) {
            $buttonText = JText::_('JBZOO_QUICKVIEW');
        }

        $uniqId = $this->app->jbstring->getId('quickview');

        $return = array();

        $return['js'] = $this->app->jbassets->widget('#' . $uniqId, 'fancybox', array(
            'type'       => "iframe",
            'fitToView'  => true,
            'width'      => $popupWidth,
            'height'     => $popupHeight,
            'autoSize'   => $autoSize,
            'iframe'     => array(
                'scrolling' => $scroll,
                'preload'   => true,
            ),
            'closeClick' => false,
            'title'      => false,
            'helpers'    => array(
                'overlay' => array(
                    'locked' => true,
                )
            ),
        ), true);

        $return['btnAttrs'] = array(
            'id'    => $uniqId,
            'rel'   => 'nofollow',
            'href'  => $itemUrl,
            'title' => $this->getItem()->name,
            'class' => array(
                'jbbutton small',
                'quickview',
                'jsQuickView',
            )
        );

        $return['buttonText'] = $buttonText;

        return $return;
    }

    /**
     * Get url with param
     * @param string $layout
     * @return string
     */
    protected function _getParamsLink($layout = 'quickview')
    {
        $itemLink = $this->app->route->item($this->_item);

        return $this->app->jbrouter->addParamsToUrl($itemLink, array(
            'tmpl'        => 'component',
            'jbquickview' => $layout,
            'modal'       => 1
        ));
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
