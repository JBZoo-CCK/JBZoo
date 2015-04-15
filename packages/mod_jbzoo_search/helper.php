<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
require_once(JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');

/**
 * Class JBZooFilterHelper
 */
class JBZooFilterHelper
{
    /**
     * @var JRegistry
     */
    protected $_params = null;

    /**
     * @var stdClass
     */
    protected $_module = null;

    /**
     * @var App
     */
    protected $app = null;

    /**
     * @var string
     */
    protected $_itemLayout = 'default';

    /**
     * @type string
     */
    protected $_moduleLayout = 'default';

    /**
     * @type JBHtmlHelper
     */
    protected $_jbhtml = null;

    /**
     * @type JBRequestHelper
     */
    protected $_jbrequest = null;

    /**
     * @param JRegistry $params
     * @param stdClass  $module
     */
    public function __construct(JRegistry $params, $module)
    {
        JBZoo::init();

        $this->app = App::getInstance('zoo');

        $this->_params    = $params;
        $this->_module    = $module;
        $this->_jbhtml    = $this->app->jbhtml;
        $this->_jbrequest = $this->app->jbrequest;

        if ($params->get('item_layout')) {
            $this->_itemLayout   = $params->get('item_layout', 'default');
            $this->_moduleLayout = $params->get('layout', 'default');
        } else {
            $this->_itemLayout   = $params->get('layout', 'default');
            $this->_moduleLayout = $params->get('module-layout', 'default');
        }
    }

    /**
     * @param bool $onlyName
     * @return string
     */
    public function getModuleLayout($onlyName = false)
    {
        $layout = $this->_moduleLayout;
        if ($onlyName && strpos($this->_moduleLayout, ':') !== false) {
            list($tmpl, $layout) = explode(':', $this->_moduleLayout);
            return $layout;
        }

        return $layout;

    }

    /**
     * @return string
     */
    public function getItemLayout()
    {
        return $this->_itemLayout;
    }

    /**
     * @return FilterRenderer
     */
    public function createRenderer()
    {
        // set renderer
        $renderer = $this->app->renderer->create('filter')
            ->addPath(array(
                $this->app->path->path('component.site:'),
                dirname(__FILE__),
                $this->app->path->path('applications:' . JBZOO_APP_GROUP . '/catalog/renderer')
            ))
            ->setModuleParams($this->_params);

        return $renderer;
    }

    /**
     * Get pages
     * @return mixed
     */
    public function renderPages()
    {
        $value = $this->_jbrequest->get('limit', $this->_params->get('pages', 20));

        if ((int)$this->_params->get('pages_show', 1)) {
            $values = array('5', '10', '15', '20', '25', '30', '50', '100', 'all');

            $options = array();
            foreach ($values as $option) {
                $options[$option] = 'JBZOO_NUMBERS_' . $option;
            }

            $html = $this->_jbhtml->select($options, 'limit', null, $value, 'jbfilter-id-limit', true);
        } else {
            $html = $this->_jbhtml->hidden('limit', $value);
        }

        return $html;
    }

    /**
     * Get logic
     * @return string|null
     */
    public function renderLogic()
    {
        $value = $this->_jbrequest->get('logic', $this->_params->get('logic', 'and'));

        if ((int)$this->_params->get('logic_show', 1)) {
            $options = array('and' => 'JBZOO_AND', 'or' => 'JBZOO_OR');
            $html    = $this->_jbhtml->radio($options, 'logic', null, $value, 'jbfilter-id-logic', true);
        } else {
            $html = $this->_jbhtml->hidden('logic', $value);
        }

        return $html;
    }

    /**
     * Get ordering
     * @return mixed
     */
    public function getOrderings()
    {
        $appId = $this->getAppId();
        $type  = $this->getType();

        $default = $this->app->data->create($this->_params->get('order_default', array()));
        $request = $this->_jbrequest->getArray('order');
        $values  = $this->app->data->create((!empty($request)) ? $request : $default);

        $orderList = $this->getOrderList();

        $html = array();
        if ((int)$this->_params->get('order_show', 1) && !empty($orderList)) {

            if (empty($request)) {
                $values->set('reverse', (int)($default->order == 'desc'));
            }

            $orderMode = $this->_params->get('order_mode');

            $options = array();
            foreach ($orderList as $fieldId) {
                $options[$fieldId] = $this->app->jborder->getNameById($fieldId, $type, $appId);
            }

            $html[] = $this->_jbhtml->select($options, 'order[field]', array(), $values->get('field'), 'jbfilter-id-order', true);
            $html[] = $this->_jbhtml->checkbox(array('1' => JText::_('JBZOO_ORDER_REVERSE')), 'order[reverse]', '', $values->get('reverse'));
            $html[] = $this->_jbhtml->hidden('order[mode]', $orderMode);

        } else {
            foreach ($default as $key => $value) {
                $html[] = $this->_jbhtml->hidden('order[' . $key . ']', $value);
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return array
     */
    public function getOrderList()
    {
        return $this->_params->get('order_list', array());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_params->get('type');
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return (int)$this->_params->get('application', 0);
    }

    /**
     * @return int
     */
    public function getMenuId()
    {
        return (int)$this->_params->get('menuitem', $this->_jbrequest->get('Itemid'));
    }

    /**
     * @return int
     */
    public function getFormId()
    {
        return 'jbfilter-' . $this->getItemLayout(true) . '-' . $this->_module->id;
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function renderHidden($fields)
    {
        return $this->app->jbhtml->hiddens($fields);
    }

    /**
     * @param string $layout
     * @param array  $vars
     * @return string
     */
    public function partial($layout, $vars = array())
    {
        $layout = !empty($layout) ? $layout : 'default';

        $__layout = JPath::clean((string)JModuleHelper::getLayoutPath($this->_module->module, $layout));

        if (JFile::exists($__layout)) {

            $vars['zoo']          = $this->app;
            $vars['params']       = $this->_params;
            $vars['module']       = $this->_module;
            $vars['filterHelper'] = $this;
            $vars['itemLayout']   = $this->_itemLayout;
            $vars['moduleLayout'] = $this->_moduleLayout;

            if (is_array($vars)) {
                foreach ($vars as $_var => $_value) {
                    $$_var = $_value;
                }
            }

            ob_start();
            include($__layout);
            $__html = ob_get_contents();
            ob_end_clean();

            return $__html;
        }

        return null;
    }

}
