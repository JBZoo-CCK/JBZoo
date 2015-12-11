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
 * Class ElementJBComments
 */
class ElementJBComments extends Element implements iSubmittable
{
    /**
     * Check, has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        if ((int)$this->get('fb_comments_enabled', (int)$this->config->get('fb_comments_enabled', 0)) ||
            (int)$this->get('vk_comments_enabled', (int)$this->config->get('vk_comments_enabled', 0)) ||
            (int)$this->_item->isCommentsEnabled()
        ) {

            return true;
        }
        return false;
    }

    /**
     * Render element
     * @param array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);

        $this->loadAssets();

        $html['JBZOO_COMMENTS_STANDARD']  = $this->_renderZooComments($params);
        $html['JBZOO_COMMENTS_VKONTAKTE'] = $this->_renderVKComments($params);
        $html['JBZOO_COMMENTS_FACEBOOK']  = $this->_renderFBComments($params);

        if (count(array_filter($html)) > 1) {
            return $this->_renderComplex($html);
        } else {
            return implode(PHP_EOL, $html);
        }

    }

    /**
     * Render bookmarks layout
     * @param array $params
     * @return null|string
     */
    protected function _renderVKComments($params = array())
    {
        $params = $this->app->data->create($params);
        if ((int)$this->get('vk_comments_enabled', 1) && (int)$this->config->get('vk_comments_enabled', 0)
            && $this->config->get('vk_comm_app_id', false)
        ) {

            $doc = JFactory::getDocument();
            $doc->addScript('http://vk.com/js/api/openapi.js');

            $vkCommOpt = array(
                'name'      => 'JBZOO_COMMENTS_VKONTAKTE',
                'vkId'      => uniqid('vk-comments-'),
                'vkInit'    => array(
                    'apiId'       => $this->config->get('vk_comm_app_id', false),
                    'onlyWidgets' => (int)$params->get('vk_comm_enable', 1)
                ),
                'vkParams'  => array(
                    'width'       => (int)$params->get('vk_comm_width', 300),
                    'limit'       => (int)$params->get('vk_comm_limit', 10),
                    'attach'      => $params->get('vk_comm_attach', 1) ? '*' : false,
                    'autoPublish' => (int)$params->get('vk_comm_auto_publish', 1),
                    'height'      => (int)$params->get('vk_comm_height', 500),
                    'norealtime'  => (int)$params->get('vk_comm_norealtime', 0),
                    'mini'        => $params->get('vk_comm_mini', 'auto')
                ),
                'vkPageUrl' => 'jbcomments-vk-' . $this->getItem()->id
            );

            // render layout
            if ($layout = $this->getLayout('_vkcomments.php')) {
                return $this->renderLayout($layout, array('vkParams' => $vkCommOpt));
            }
        }

        return null;
    }

    /**
     * Render likes layout
     * @param array $params
     * @return string|null
     */
    protected function _renderFBComments($params = array())
    {
        $params = $this->app->data->create($params);

        if ((int)$this->get('fb_comments_enabled', 1) && (int)$this->config->get('fb_comments_enabled', 0)) {

            $fbCommOpt = array(
                'name'         => 'JBZOO_COMMENTS_FACEBOOK',
                'fbClass'      => uniqid('fb-comments-'),
                'fbAppId'      => $this->config->get('fb_comm_app_id', false),
                'fbLocale'     => $params->get('fb_comm_locale', 'ru_RU'),
                'fbAttributes' => array(
                    'class'            => 'fb-comments',
                    'data-href'        => JUri::base() . JString::trim($this->app->route->item($this->getItem()), '/'),
                    'data-width'       => $params->get('fb_comm_width', 470),
                    'data-num-post'    => $params->get('fb_comm_limit', 10),
                    'data-colorscheme' => $params->get('fb_comm_color_scheme', 'light')
                )
            );

            // render layout
            if ($layout = $this->getLayout('_fbcomments.php')) {
                return $this->renderLayout($layout, array('fbParams' => $fbCommOpt));
            }
        }
        return null;
    }

    /**
     * Render complex layout
     * @return string|null
     */
    protected function _renderZooComments()
    {
        $view = $this->app->jblayout->getView();

        if ($view) {
            !defined('JBZOO_COMMENTS_RENDERED_' . $this->getItem()->id) && define('JBZOO_COMMENTS_RENDERED_' . $this->getItem()->id, true);
            return $this->app->comment->renderComments($view, $this->getItem());
        } else {
            return null;
        }
    }

    /**
     * @param $html
     * @return string
     */
    protected function _renderComplex($html)
    {
        // render layout
        if ($layout = $this->getLayout('_commentstabs.php')) {
            return $this->renderLayout($layout, array('jbcomments' => $html));
        }
    }

    /**
     * Edit action
     * @return string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            if ((int)$this->config->get('fb_comments_enabled', 0) || (int)$this->config->get('vk_comments_enabled', 0)) {
                return $this->renderLayout($layout);
            }
        }
        return null;
    }

    /**
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit();
    }

    /**
     * loadAssets action
     * @return void
     */
    public function loadAssets()
    {
        $this->app->jbassets->tabs();
        $this->app->jbassets->less('elements:jbcomments/assets/less/jbcomments.less');
    }

    /**
     * @param array $value
     * @param array submission parameters
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        return $value;
    }

}
