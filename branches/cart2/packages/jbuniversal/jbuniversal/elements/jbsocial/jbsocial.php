<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class ElementJBSocial
 */
class ElementJBSocial extends Element implements iSubmittable
{
    /**
     * Check, has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        if ((int)$this->config->get('likes_enabled', 1) ||
            (int)$this->config->get('bookmarks_enabled', 1) ||
            (int)$this->config->get('complex_enabled', 1)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Render element
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        $this->_renderOpenGraph($params);
        $this->app->jbassets->less('elements:jbsocial/assets/styles.less');

        $html['bookmarks'] = $this->_renderBookmarks($params);
        $html['likes']     = $this->_renderLikes($params);
        $html['complex']   = $this->_renderComplex($params);

        return implode(PHP_EOL, $html);
    }

    /**
     * Render bookmarks layout
     * @param array $params
     * @return null|string
     */
    protected function _renderBookmarks($params = array())
    {
        // init vars
        $bookmarks = $this->getBookmarks();
        $params    = $this->app->data->create($params);

        if ((int)$params->get('bookmark_enabled', 1) &&
            (int)$this->config->get('bookmarks_enabled', 1) &&
            (int)$this->get('bookmarks_enabled', 1)
        ) {

            // get active jbzoobookmarks
            foreach ($bookmarks as $bookmark => $data) {
                if (!(int)$this->config->get('bookmark_' . $bookmark, 1)) {
                    unset($bookmarks[$bookmark]);
                }
            }

            // render bookmarks layout
            if ($layout = $this->getLayout('_bookmarks.php')) {
                return $this->renderLayout($layout, array(
                    'bookmarks' => $bookmarks
                ));
            }
        }
        return null;
    }

    /**
     * Render likes layout
     * @param array $params
     * @return string|null
     */
    protected function _renderLikes($params = array())
    {
        // init vars
        $document = JFactory::getDocument();
        $params   = $this->app->data->create($params);
        $item     = $this->getItem();

        $vkOptions = $fbOptions = $twOptions = $okOptions = $gpOptions = $liOptions = array();

        if ((int)$this->config->get('likes_enabled', 1) &&
            (int)$this->get('likes_enabled', 1)
        ) {
            // Vkontakte Button
            if ((int)$params->get('like_vk_enabled', 1)) {

                $document->addScript('http://vkontakte.ru/js/api/openapi.js');

                // get options like_vk button
                $vkOptions = array(
                    'vkEnabled' => $params->get('like_vk_enabled', 1),
                    'vkId'      => uniqid('like_vk'),
                    'id'        => $params->get('like_vk_id'),
                    'pageId'    => $item->id,
                    'params'    => array(
                        'type'      => str_replace('like_vk_', '', $params->get('like_vk_variants')),
                        'verb'      => $params->get('like_vk_verb'),
                        'height'    => $params->get('like_vk_height'),
                        'pageTitle' => $this->getItem()->name,
                        'pageUrl'   => substr(JUri::root(), 0, -1) . $this->app->route->item($item)
                    )
                );
            }

            // Facebook Button
            if ((int)$params->get('like_fb_enabled', 1)) {

                // get locale
                $lang = $params->get('like_fb_locale');

                // get string request
                $src = '//connect.facebook.net/' . $lang . '/all.js#xfbml=1';

                // get options FB like button
                $fbOptions = array(
                    'fbEnabled' => (int)$params->get('like_fb_enabled', 1),
                    'fbId'      => uniqid('fb-root'),
                    'class'     => 'fb-like',
                    'data-src'  => $src,
                    'params'    => array(
                        'class'            => 'fb-like',
                        'data-href'        => substr(JUri::root(), 0, -1) . $this->app->route->item($item),
                        'data-send'        => $params->get('like_fb_send'),
                        'data-layout'      => $params->get('like_fb_layout'),
                        'data-show-faces'  => $params->get('like_fb_show_faces'),
                        'data-colorscheme' => $params->get('like_fb_colorscheme'),
                        'data-action'      => $params->get('like_fb_action')
                    )
                );
            }

            // Tweet Button
            if ((int)$params->get('like_tw_enabled', 1)) {

                // get options TW like button
                $twOptions = array(
                    'twEnabled' => (int)$params->get('like_tw_enabled', 1),
                    'params'    => array(
                        'href'          => "https://twitter.com/share",
                        'class'         => 'twitter-share-button',
                        'data-url'      => substr(JUri::root(), 0, -1) . $this->app->route->item($item),
                        'data-lang'     => $params->get('like_tw_locale'),
                        'data-via'      => $params->get('like_tw_via'),
                        'data-size'     => $params->get('like_tw_size'),
                        'data-related'  => $params->get('like_tw_related'),
                        'data-hashtags' => str_replace(' ', '', $params->get('like_tw_hashtags')),
                        'data-dnt'      => $params->get('like_tw_dnt')
                    )
                );

                if ((int)$params->get('like_tw_count', 1) == 0) {
                    $twOptions['params']['data-count'] = 'none';
                }
            }

            // Odnoklassniki button
            if ((int)$params->get('like_ok_enabled', 1)) {

                // get options OK like button
                $okOptions = array(
                    'okEnabled' => $params->get('like_ok_enabled'),
                    'okUrl'     => $params->get('like_ok_url'),
                    'okId'      => uniqid('ok_shareWidget'),
                    'params'    => array(
                        'nc' => $params->get('like_ok_count_enable'),
                        'vt' => $params->get('like_ok_count'),
                        'nt' => $params->get('like_ok_text'),
                        'ck' => $params->get('like_ok_ok_text'),
                        'st' => $params->get('like_ok_view'),
                        'sz' => (int)$params->get('like_ok_size')
                    ),

                );
            }

            // Google+ button
            if ((int)$params->get('like_gp_enabled', 1)) {

                // get options Google+ like button
                $gpOptions = array(
                    'gpEnabled'    => $params->get('like_gp_enabled'),
                    'scriptParams' => array(
                        'lang' => $params->get('like_gp_lang'),
                    ),
                    'params'       => array(
                        'class'           => 'g-plusone',
                        'data-size'       => $params->get('like_gp_size'),
                        'data-annotation' => $params->get('like_gp_annotation'),
                        'data-width'      => $params->get('like_gp_width')

                    )
                );
            }

            // Linked In button
            if ((int)$params->get('like_li_enabled', 1)) {

                // get options Google+ like button
                $liOptions = array(
                    'liEnabled' => $params->get('like_li_enabled'),
                    'lang'      => $params->get('like_li_lang'),
                    'params'    => array(
                        'data-counter' => $params->get('like_li_counter'),
                        'data-url'     => $params->get('like_li_url')
                    )
                );
            }

            // render likes layout
            if ($layout = $this->getLayout('_likes.php')) {
                return $this->renderLayout($layout, array(
                    'vkOption' => $vkOptions,
                    'fbOption' => $fbOptions,
                    'twOption' => $twOptions,
                    'okOption' => $okOptions,
                    'liOption' => $liOptions,
                    'gpOption' => $gpOptions
                ));
            }
        }

        return null;
    }

    /**
     * Render complex layout
     * @param array $params
     * @return string|null
     */
    protected function _renderComplex($params = array())
    {
        $doc         = JFactory::getDocument();
        $params      = $this->app->data->create($params);
        $yaOptions   = array('element' => uniqid('ya_share'));
        $allServices = array(
            'blogger', 'delicious', 'diary', 'digg', 'evernote', 'facebook', 'friendfeed', 'gplus', 'juick',
            'liveinternet', 'linkedin', 'lj', 'moikrug', 'moimir', 'myspace', 'odnoklassniki', 'pinterest',
            'surfingbird', 'tutby', 'twitter', 'vkontakte', 'yaru', 'yazakladki'
        );

        if ((int)$params->get('like_ya_share_enabled', 1) &&
            (int)$this->config->get('complex_enabled', 1) &&
            (int)$this->get('complex_enabled', 1)
        ) {
            $yaOptions['yaEnabled'] = $params->get('like_ya_share_enabled');
            $doc->addScript('//yandex.st/share/share.js');

            $services = str_replace(' ', '', $params->get('like_ya_share_services'));
            if (!empty($services)) {
                $services = explode(',', $services);
            } else {
                $services = array('yaru', 'vkontakte', 'odnoklassniki', 'moimir', 'myspace', 'tutby', 'yazakladki');
            }

            $boxServices = array_values(array_diff($allServices, $services));
            $moreParams  = $this->_getOGData();

            $yaOptions['elementStyle'] = array(
                'type'          => $params->get('like_ya_share_style'),
                'quickServices' => $services
            );

            $yaOptions['title']       = $moreParams['og:title'];
            $yaOptions['description'] = isset($moreParams['og:description']) ? $this->_replaceSpecial($moreParams['og:description']) : '';
            $yaOptions['link']        = $this->app->jbrouter->getHostUrl() . $this->app->route->item($this->getItem());
            $yaOptions['image']       = isset($moreParams['og:image']) ? $moreParams['og:image'] : '';
            $yaOptions['popupStyle']  = array('blocks' => $boxServices);
            $yaOptions['theme']       = $params->get('like_ya_share_theme', 'default');
        }

        if ($layout = $this->getLayout('_complex.php')) {
            return $this->renderLayout($layout, array(
                'yaOption' => $yaOptions
            ));
        }

        return null;
    }

    /**
     * Render open graph meta tags
     * @param $params
     */
    protected function _renderOpenGraph($params)
    {
        static $isOGShow = true;

        if ($this->app->jblayout->getView()->task == 'item') {
            $doc    = JFactory::getDocument();
            $ogTags = $this->_getOGData();

            if ($isOGShow == true) {

                if ($params['like_vk_enabled'] == '1' && $this->config->get('like_vk_id')) {
                    $ogTags['vk:app_id'] = $this->config->get('like_vk_id');
                }

                foreach ($ogTags as $key => $value) {
                    $value = strip_tags($value);
                    $value = str_replace(array("\n", "\r"), ' ', $value);
                    $value = htmlspecialchars_decode($value, ENT_NOQUOTES);
                    $value = $this->app->jbstring->cutByWords($value, 250);
                    $doc->addCustomTag('<meta property="' . $key . '" content="' . $value . '" />');
                }
            }

            $isOGShow = false;
        }
    }

    /**
     * Get open graph tags
     * @return array
     */
    protected function _getOGData()
    {
        $ogTags = array(
            'og:type'  => 'article',
            'og:title' => addslashes(htmlspecialchars(strip_tags($this->getItem()->name))),
            'og:url'   => JUri::base() . JString::trim($this->app->route->item($this->getItem()), '/'),
        );

        $item     = $this->getItem();
        $elements = $item->getElements();

        foreach ($elements as $key => $value) {
            $class = strtolower(get_class($value));
            if ($class == 'elementtextarea') {
                $str = $value->data();
                if (!empty($str[0]['value'])) {
                    $ogTags['og:description'] = $str[0]['value'];
                    break;
                } else {
                    $str = $this->getItem()->params->get('metadata.description');
                    if (!empty($str)) {
                        $ogTags['og:description'] = $str;
                        break;
                    } else {
                        $ogTags['og:description'] = JString::trim(JFactory::getDocument()->getDescription());
                        break;
                    }
                }
            }
        }

        foreach ($elements as $key => $value) {
            $type = strtolower($value->getElementType());
            $path = '';
            if ($type == 'jbimage') {
                $data = $value->data();
                $path = $data[0];
            }

            if ($type == 'image') {
                $path = $value->data();
            }

            if (isset($path['file']) && !empty($path['file'])) {
                $ogTags['og:image'] = JUri::base() . $path['file'];
                break;
            }
        }

        if (isset($ogTags['og:description'])) {
            $ogTags['og:description'] = $this->app->zoo->triggerContentPlugins($ogTags['og:description']);
        }

        return $ogTags;
    }

    /**
     * @param $string
     * @return string
     */
    protected function _replaceSpecial($string)
    {
        $string = addslashes(str_replace('&nbsp;', ' ', strip_tags($string)));
        $string = str_replace(array("\r\n", "\r", "\n"), '', $string);
        $string = $this->app->jbstring->cutByWords($string, 250);

        return htmlspecialchars_decode($string, ENT_NOQUOTES);
    }

    /**
     * Edit action
     * @return string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            if ((int)$this->config->get('likes_enabled', 0) ||
                (int)$this->config->get('bookmarks_enabled', 0) ||
                (int)$this->config->get('complex_enabled', 0)
            ) {
                return $this->renderLayout($layout);
            }
        }
        return null;
    }

    /**
     * Render submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit();
    }

    /**
     * Validate submission
     * @param array $value
     * @param       array submission parameters
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        return array('value' => $value->get('value'));
    }

    /**
     * Get supported bookmarks
     * getBookmarks action
     * @return array
     */
    public function getBookmarks()
    {
        return $this->app->jbbookmarks->getBookmarkList();
    }

}


