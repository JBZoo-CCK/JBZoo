<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Segrey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBTemplate
 */
class JBTemplate
{

    /**
     * Link to Zoo Application instance.
     *
     * @var
     */
    public $app;

    /**
     * Link to Zoo Application.
     *
     * @var
     */
    public $application;

    /**
     * Event application.
     *
     * @var
     */
    public $event;

    /**
     * Application params.
     *
     * @var
     */
    public $params;

    /**
     * JBZoo wrapper id.
     *
     * @var string
     */
    public $id = 'jbzoo';

    /**
     * JBZoo class prefix.
     *
     * @var string
     */
    public $prefix = 'jbzoo';

    /**
     * Zoo class, id for support youthemes.
     *
     * @var string
     */
    public $yoo = 'yoo-zoo';

    /**
     * Defaul layout.
     *
     * @var string
     */
    private $_layoutDefault = '_default';

    /**
     * JBTemplate constructor.
     *
     * @param $event
     */
    public function __construct($event)
    {
        $this->event         = $event;
        $this->app           = App::getInstance('zoo');
        $this->application   = $this->event->getSubject();
        $this->params        = $this->application->params;
    }

    /**
     * Init JBTemplate.
     *
     * @return void
     */
    public function init()
    {
        $templateName = $this->getTemplateName();
        $templates    = $this->application->getTemplates();

        if (isset($this->application->basketTmpl)) {
            $templateName = $this->application->basketTmpl;
        }

        $this->application->template = $this->application->getTemplate();
        if (isset($templates[$templateName])) {
            $this->application->template = $templates[$templateName];
        }

        $this->_registerPaths($templateName);
        $this->_incDemoLess();
    }

    /**
     * On init (call after init method).
     *
     * @return void
     */
    public function onInit()
    {
    }

    /**
     * Default layout.
     *
     * @return string
     */
    public function getDefaultLayout()
    {
        return $this->_layoutDefault;
    }

    /**
     * JBZoo wrapper start.
     *
     * @return string
     */
    public function wrapStart()
    {
        $arrayAttrs = $this->wrapperAttrs();
        $wrapAttrs  = $this->app->jbhtml->buildAttrs($arrayAttrs);

        return '<div ' . $wrapAttrs . '>' . PHP_EOL;
    }

    /**
     * JBZoo wrapper end.
     *
     * @return string
     */
    public function wrapEnd()
    {
        return '</div>' . PHP_EOL;
    }

    /**
     * Render item wrapper.
     *
     * @param Item $item
     * @param string $defaultLayout
     * @param $htmlItem
     * @return null|string
     */
    public function renderItem(Item $item, $defaultLayout = 'teaser', $htmlItem)
    {
        $attrs = array(
            'class' => array(
                'jbzoo-item',
                'jbzoo-item-' . $item->type,
                'jbzoo-item-' . $defaultLayout,
                'jbzoo-item-' . $item->id
            )
        );

        $output = $htmlItem;
        $attrs  = $this->app->jbhtml->buildAttrs($attrs);

        $wrapperTag = 'none';
        if ($this->application) {
            $wrapperTag = $this->params->get('global.config.wrap_item_style', 'none');
        }

        if ($wrapperTag != 'none') {
            $output = '<' . $wrapperTag . ' ' . $attrs . '>' . $htmlItem . '</' . $wrapperTag . '>';
        }

        return $output;
    }

    /**
     * Columns ordering.
     *
     * @param $layoutName
     * @param $objects
     * @param $view
     * @return bool
     */
    public function columns($layoutName, $objects, $view)
    {
        $colsNum   = $view->params->get('template.' . $layoutName . '_cols', 1);
        $colsOrder = $view->params->get('template.' . $layoutName . '_order', 1);

        $vars = array(
            'cols_num'   => $colsNum,
            'cols_order' => $colsOrder
        );

        // init vars
        $i            = 0;
        $columns      = array();
        $column       = 0;
        $row          = 0;
        $countObjects = count($objects);
        $rows         = ceil($countObjects / $colsNum);

        if ($countObjects > 0) {
            foreach ($objects as $object) {

                if ($colsOrder) {
                    // order down
                    if ($row >= $rows) {
                        $column++;
                        $row  = 0;
                        $rows = ceil(($countObjects - $i) / ($colsNum - $column));
                    }
                    $row++;
                    $i++;
                } else {
                    // order across
                    $column = $i++ % $colsNum;
                    $column = $i;
                }

                if (!isset($columns[$column])) {
                    $columns[$column] = '';
                }

                if ($object instanceof Item) {
                    $columns[$column] .= $this->app->jblayout->renderItem($object);
                } else {
                    $columns[$column] .= $this->app->jblayout->render($layoutName, $object, $vars);
                }
            }

            return $this->app->jblayout->render($layoutName . '_columns', $columns, $vars);
        }

        return false;
    }

    /**
     * Get differen system classes for parent wrapper element.
     *
     * @return string
     */
    public function wrapperAttrs()
    {
        $attrs = array();

        // standard
        $attrs['id']      = $this->id;
        $attrs['class'][] = $this->prefix;

        // view or task
        if ($view = $this->app->jbrequest->get('view')) {
            $attrs['class'][] = $this->prefix . '-view-' . $view;
        }

        if ($task = $this->app->jbrequest->get('task')) {
            $attrs['class'][] = $this->prefix . '-view-' . $task;
        }

        // application info
        if ($this->application) {
            $attrs['class'][] = $this->prefix . '-app-' . $this->application->alias;
            $attrs['class'][] = $this->prefix . '-tmpl-' . $this->application->getTemplate()->name;

            $attrs['id']      = $this->yoo;
            $attrs['class'][] = $this->yoo;

            if ((int)$this->application->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->prefix . '-rborder';
            }
        }

        $attrs['class'][] = 'clearfix';

        return $attrs;
    }

    /**
     * Build layout variants.
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
        $tmplFullPaths[] = $this->_layoutDefault;

        return $tmplFullPaths;
    }

    /**
     * Get template name.
     *
     * @return mixed
     */
    public function getTemplateName()
    {
        return $this->application->params->get('template');
    }

    /**
     * Register template paths.
     *
     * @param $templateName
     */
    protected function _registerPaths($templateName)
    {
        $this->app->path->register($this->app->path->path('jbtmpl:' . $templateName . '/assets'), 'jbassets');
        $this->app->path->register($this->app->path->path('jbtmpl:' . $templateName . '/helpers'), 'helpers');
        $this->app->path->register($this->app->path->path('jbtmpl:' . $templateName . '/cart-elements'), 'cart-elements');
    }

    /**
     * Include demo.less.
     *
     * @return void
     */
    protected function _incDemoLess()
    {
        $this->app->jbassets->less('jbassets:less/demo.less');
    }

}
