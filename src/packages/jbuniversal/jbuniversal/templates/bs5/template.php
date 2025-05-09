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
 * Class JBTemplateBootstrap
 */
class JBTemplateBs5 extends JBTemplate
{

    /**
     * On init template.
     *
     * @return void
     */
    public function onInit()
    {
        $version = (int)$this->params->get('global.template.version', 2);

        // if ($this->params->get('global.template.add_js', true)) {
        //     $this->app->jbassets->js('jbassets:js/bootstrap_v' . $version . '.x.min.js');
        // }

        // if ($this->params->get('global.template.add_css', true)) {
        //     $this->app->jbassets->css('jbassets:css/bootstrap_v' . $version . '.x.min.css');
        // }

        $this->app->jbassets->less(array(
            'jbassets:less/bootstrap.styles.less',
        ));

        // $this->app->jbassets->widget('[data-toggle=\'tooltip\']', 'tooltip');
    }

    /**
     * Build layout variants.
     *
     * Add new default variants for bootstrap application version.
     *
     * @param $vars
     * @return array
     */
    public function buildVariants($vars)
    {
        $tmplFullPaths = array();

        if (isset($vars['params']) && $vars['layout'] != '__auto__') {
            if ($tmpl = $vars['params']->get('config.layout_' . $vars['layout'], null)) {
                $tmplFullPaths[] = $tmpl;
            }
        }

        if (isset($vars['alias'])) {
            $tmplFullPaths[] = $this->application->alias . '.' . $vars['alias'];
        }

        if (isset($vars['id'])) {
            $tmplFullPaths[] = $this->application->alias . '.' . $vars['id'];
        }

        if ($template = $this->application->params->get('template')) {
            $tmplFullPaths[] = $this->application->alias . '.' . $template;
        }

        $tmplFullPaths[] = $this->application->alias;

        if ($this->app->jbbootstrap->version() == 2) {
            $tmplFullPaths[] = '_default_v2';
        } else {
            $tmplFullPaths[] = '_default_v3';
        }

        $tmplFullPaths[] = $this->getDefaultLayout();

        return $tmplFullPaths;
    }

    /**
     * Attributes for jbzoo wrapper.
     *
     * @return array|string
     */
    public function wrapperAttrs()
    {
        $attrs        = array();
        $defaultAttrs = parent::wrapperAttrs();

        if ($this->application) {
            if (!(int)$this->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->prefix . '-no-border';
            }

            if ($isQuickView = $this->app->jbrequest->get('jbquickview', false)) {
                $attrs['class'][] = 'jbmodal';
            }
        }

        return array_merge_recursive($defaultAttrs, $attrs);
    }

    /**
     * Bootstrap pagination.
     *
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
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_BOOTSTRAP_PAGINATE_FIRST') . '</a></li>';
                $link = $pagination->current() - 1 == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . ($pagination->current() - 1));
                $html .= '<li><a href="' . JRoute::_($link) . '">&laquo;</a></li>';
            }

            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                if ($i == $pagination->current()) {
                    $html .= '<li class="active"><span>' . $i . '</span>';
                } else {
                    $link = $i == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . $i);
                    $html .= '<li><a href="' . JRoute::_($link) . '">' . $i . '</a></li>';
                }
            }

            if ($pagination->current() < $pagination->pages()) {
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->current() + 1));
                $html .= '<li><a href="' . JRoute::_($link) . '">&raquo;</a></li>';
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->pages()));
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_BOOTSTRAP_PAGINATE_LAST') . '</a></li>';
            }

        }

        return $html;
    }

}
