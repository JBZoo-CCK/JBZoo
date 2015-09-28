<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBUikitHelper
 */
class JBUikitHelper extends AppHelper
{

    /**
     * Include uikit assets
     * @param $params
     * @return void
     */
    public function assets($params)
    {
        if ($params) {
            $isAddJs  = (int)$params->get('global.template.add_js', true);
            $isAddCss = $params->get('global.template.add_css', 'yes_gradient');

            $this->js($isAddJs);
            $this->cssVariation($isAddCss);

            if ($isQuickView = $this->app->jbrequest->get('jbquickview', false)) {
                $isAddCss = ($isAddCss == 'no') ? 'yes' : $isAddCss;

                $this->js(true);
                $this->cssVariation($isAddCss);
            }
        }
    }

    /**
     * Uikit pagination
     * @param $pagination
     * @param $url
     * @return string
     */
    public function paginate($pagination, $url)
    {
        $html = '';

        if ($pagination->pages() > 1) {

            $rangeStart = max($pagination->current() - $pagination->range(), 1);
            $rangeEnd   = min($pagination->current() + $pagination->range() - 1, $pagination->pages());

            if ($pagination->current() > 1) {
                $link = $url;
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_UIKIT_PAGINATE_FIRST') . '</a></li>';
                $link = $pagination->current() - 1 == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . ($pagination->current() - 1));
                $html .= '<li><a href="' . JRoute::_($link) . '"><i class="uk-icon-angle-double-left"></i></a></li>';
            }

            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                if ($i == $pagination->current()) {
                    $html .= '<li class="uk-active"><span>' . $i . '</span></li>';
                } else {
                    $link = $i == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . $i);
                    $html .= '<li><a href="' . JRoute::_($link) . '">' . $i . '</a></li>';
                }
            }

            if ($pagination->current() < $pagination->pages()) {
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->current() + 1));
                $html .= '<li><a href="' . JRoute::_($link) . '"><i class="uk-icon-angle-double-right"></i></a></li>';
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->pages()));
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_UIKIT_PAGINATE_LAST') . '</a></li>';
            }

        }

        return $html;
    }

    /**
     * Include css variation
     * @param string $addCss
     * @return void
     */
    public function cssVariation($addCss = 'no')
    {
        switch ($addCss) {
            case 'yes':
                $this->app->jbassets->uikit(false, false);
                break;

            case 'yes_gradient':
                $this->app->jbassets->uikit(false, true);
                break;
        }
    }

    /**
     * Include uikit js
     * @param bool $isTrue
     */
    public function js($isTrue = false)
    {
        if ($isTrue) {
            $this->app->jbassets->js('jbassets:js/libs/uikit.min.js', JBAssetsHelper::GROUP_CORE);
        }
    }

}
