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
