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
 * Class JBLayoutHelper
 */
class JBLayoutHelper extends AppHelper
{

    /**
     * @var string
     */
    private $_layoutDefault = '_default';

    /**
     * @var Application
     */
    private $_application = null;

    /**
     * @var string
     */
    private $_rendererPath = null;

    /**
     * @var ParameterData
     */
    private $_params = null;

    /**
     * @var AppView
     */
    private $_view = null;

    /**
     * Constructor, set internal vars
     * @param $app
     */
    public function __construct($app)
    {
        // load libs 
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        // set internal vars
        $this->app           = $app;
        $this->_name         = strtolower(basename(get_class($this), 'Helper'));
        $this->_application  = $app->zoo->getApplication();
        $this->_rendererPath = $this->_application->getPath() .
            '/templates/' . $this->_application->params->get('template') .
            '/renderer';
    }

    /**
     * Set common Params for all template
     * @param $view
     * @return void
     */
    public function setView($view)
    {
        $this->_view = $view;

        if (isset($view->params)) {
            $this->_params = $view->params;

        } else {
            $this->_params = $this->_application->params;
        }

    }

    /**
     * Get view
     * @return AppView
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Set $vars
     * @param $layout
     * @param $templateVar
     * @param $vars
     * @return array
     */
    private function _setVars($layout, $templateVar, $vars)
    {
        $vars['application'] = $this->_application;
        $vars['view']        = $this->_view;
        $vars['layout']      = $layout;

        if (!isset($vars['params'])) {
            $vars['params'] = $this->_params;
        }

        if (isset($templateVar) && is_object($templateVar)) {
            $vars['object'] = $templateVar;

            // set alias
            if (isset($templateVar->alias)) {
                $vars['alias'] = $vars['object']->alias;
            }

            // set id
            if (isset($templateVar->id)) {
                $vars['id'] = $vars['object']->id;
            }

            // set params
            if (isset($templateVar->params)) {
                $vars['param'] = $vars['object']->params;
            }

        } else if (isset($templateVar) && is_array($templateVar)) {
            $vars['objects'] = $templateVar;
            $vars['count']   = count($templateVar);
        }

        return $vars;
    }

    /**
     * Load application layout
     * @param       $layout
     * @param null $templateVar
     * @param array $vars
     * @return string
     */
    public function render($layout, $templateVar = null, $vars = array())
    {
        //$this->app->jbdebug->mark('jblayout::render::' . $layout . '::start');

        $vars     = $this->_setVars($layout, $templateVar, $vars);
        $tplPaths = $this->_buildVariants($vars);

        $layoutPath = $this->_rendererPath . '/' . $layout;

        if (JFolder::exists($layoutPath)) {
            foreach ($tplPaths as $tpl) {
                $__partial = $layoutPath . '/' . $tpl . '.php';
                if (JFile::exists($__partial)) {
                    ob_start();
                    include($__partial);
                    $output = ob_get_contents();
                    ob_end_clean();

                    //$this->app->jbdebug->mark('jblayout::render::' . $layout . '::finish');

                    return $output;
                }
            }
        }

        // exception
        $errorText = '<p>Template not found: ' . $layout . '</p>';
        $errorText .= '<p>Layout path: ' . $layoutPath . '</p>';
        $errorText .= '<p><pre>Templates variants: ' . print_r($tplPaths, true) . '</pre></p>';

        return $this->app->error->raiseError(500, $errorText);
    }

    /**
     * Build layout variants
     * @param $vars
     * @return array
     */
    private function _buildVariants($vars)
    {
        $tmplFullPaths = array();

        if (isset($vars['params']) && $vars['layout'] != '__auto__') {
            if ($tmpl = $vars['params']->get('config.layout_' . $vars['layout'], null)) {
                $tmplFullPaths[] = $tmpl;
            }
        }

        if (isset($vars['alias'])) {
            $tmplFullPaths[] = $this->_application->alias . '.' . $vars['alias'];
        }

        if (isset($vars['id'])) {
            $tmplFullPaths[] = $this->_application->alias . '.' . $vars['id'];
        }

        if ($template = $this->_application->params->get('template')) {
            $tmplFullPaths[] = $this->_application->alias . '.' . $template;
        }

        $tmplFullPaths[] = $this->_application->alias;
        $tmplFullPaths[] = $this->_layoutDefault;

        return $tmplFullPaths;
    }

    /**
     * Columns ordering
     * @param $layoutName
     * @param $objects
     * @return bool|string
     */
    public function columns($layoutName, $objects)
    {
        $cols_num   = $this->_params->get('template.' . $layoutName . '_cols', 1);
        $cols_order = $this->_params->get('template.' . $layoutName . '_order', 1);

        $vars = array(
            'cols_num'   => $cols_num,
            'cols_order' => $cols_order
        );

        // init vars
        $i            = 0;
        $columns      = array();
        $column       = 0;
        $row          = 0;
        $countObjects = count($objects);
        $rows         = ceil($countObjects / $cols_num);

        if ($countObjects > 0) {
            foreach ($objects as $object) {

                if ($cols_order) {
                    // order down
                    if ($row >= $rows) {
                        $column++;
                        $row  = 0;
                        $rows = ceil(($countObjects - $i) / ($cols_num - $column));
                    }
                    $row++;
                    $i++;
                } else {
                    // order across
                    $column = $i++ % $cols_num;
                    $column = $i;
                }

                if (!isset($columns[$column])) {
                    $columns[$column] = '';
                }

                if ($object instanceof Item) {
                    $columns[$column] .= $this->renderItem($object);
                } else {
                    $columns[$column] .= $this->render($layoutName, $object, $vars);
                }
            }

            return $this->render($layoutName . '_columns', $columns, $vars);
        }

        return false;
    }

    /**
     * Get path for item render
     * @param $item
     * @param $layout
     * @return string
     */
    private function _getItemLayout($item, $layout)
    {

        if ($this->_params) {
            if (!isset($this->_params['template.layout_' . $layout])) {
                $layout = $this->_params->get('template.layout_' . $layout, $layout);
            } else {
                $layout = $this->_params->get('global.template.layout_' . $layout, $layout);
            }
        }

        if ($item && $this->_view) {
            if ($this->_view->renderer->pathExists('item/' . $item->type)
                && JFile::exists($this->_rendererPath . '/' . 'item' . '/' . $item->type . '/' . $layout . '.php')
            ) {
                return 'item.' . $item->type . '.' . $layout;

            } else {
                if (JFile::exists($this->_rendererPath . '/item/' . $layout . '.php')) {
                    return 'item.' . $layout;

                } else {
                    return 'item.teaser';
                }
            }
        }

        return 'item.teaser';
    }

    /**
     * @param Item $item
     * @param string $defaultLayout
     * @param ItemRenderer|null $renderer
     * @return null|string
     */
    public function renderItem(Item $item, $defaultLayout = 'teaser', ItemRenderer $renderer = null)
    {
        $this->app->jbdebug->mark('jblayout::renderItem (' . $item->id . ')::start');

        //$this->app->event->dispatcher->notify($this->app->event->create($item, 'item:beforeRenderLayout', array('layout' => &$defaultLayout, 'render' => &$renderer)));

        $itemLayout = $this->_getItemLayout($item, $defaultLayout);

        $htmlItem = null;

        if (!$renderer && $this->_view) {
            $renderer = $this->_view->renderer;
        }

        if ($renderer) {
            $this->app->jbassets->itemStyle($item->type);
            $htmlItem = $renderer->render($itemLayout, compact('item'));
        }

        // add item wrapper if enabled
        $wrapperTag = 'none';
        if ($this->_application) {
            $wrapperTag = $this->_application->params->get('global.config.wrap_item_style', 'none');
        }

        if ($wrapperTag != 'none') {
            $class = array(
                'jbzoo-item',
                'jbzoo-item-' . $item->type,
                'jbzoo-item-' . $defaultLayout,
                'jbzoo-item-' . $item->id
            );

            $htmlItem = '<' . $wrapperTag . ' class="' . implode(' ', $class) . '">' . $htmlItem . '</' . $wrapperTag . '>';
        }

        $this->app->event->dispatcher->notify($this->app->event->create($item, 'item:afterRenderLayout', array(
            'layout'   => &$defaultLayout,
            'render'   => &$renderer,
            'htmlItem' => &$htmlItem,
        )));

        $this->app->jbdebug->mark('jblayout::itemRender (' . $item->id . ')::finish');

        return $htmlItem;
    }

    /**
     * @param $item
     * @param $layout
     * @return bool
     */
    public function checkLayout($item, $layout)
    {

        $layout = $this->_getItemLayout($item, $layout);

        if ($layout == 'item.teaser') {
            return false;
        }
        return true;
    }

}
