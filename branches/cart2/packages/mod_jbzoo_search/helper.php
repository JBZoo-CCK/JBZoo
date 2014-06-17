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
     * @var App
     */
    protected $app = null;

    /**
     * Init Zoo
     * @param JRegistry $params
     */
    public function __construct(JRegistry $params)
    {
        $this->app     = App::getInstance('zoo');
        $this->_params = $params;

        JBZoo::init();
    }

    /**
     * Get pages
     * @return mixed
     */
    public function getPages()
    {
        $value = $this->app->jbrequest->get('limit', $this->_params->get('pages', 20));

        if ((int)$this->_params->get('pages_show', 1)) {

            $values = array('5', '10', '15', '20', '25', '30', '50', '100', 'all');

            $options = array();
            foreach ($values as $option) {
                $options[] = $this->app->html->_('select.option', $option, JText::_('JBZOO_NUMBERS_' . $option));
            }

            $html = $this->app->html->_('zoo.genericlist', $options, 'limit', array(), 'value', 'text', $value, 'filterEl_limit');

        } else {
            $html = '<input type="hidden" name="limit" value="' . $value . '" />';
        }

        return $html;
    }

    /**
     * Get logic
     * @return string|null
     */
    public function getLogic()
    {
        $value = $this->app->jbrequest->get('logic', $this->_params->get('logic', 'and'));

        if ((int)$this->_params->get('logic_show', 1)) {

            $values = array('and', 'or');

            $options = array();
            foreach ($values as $option) {
                $options[] = $this->app->html->_('select.option', $option, JText::_('JBZOO_' . $option));
            }

            $html = $this->app->html->_('select.radiolist', $options, 'logic', array(), 'value', 'text', $value, 'filterEl_logic');

        } else {
            $html = '<input type="hidden" name="logic" value="' . $value . '" />';

        }

        return $html;
    }

    /**
     * Get ordering
     * @return mixed
     */
    public function getOrderings()
    {
        $default   = $this->_params->get('order_default', array());
        $default   = $this->app->data->create($default);
        $orderList = $this->getOrderList();

        $request = $this->app->jbrequest->getArray('order');
        $value   = (!empty($request)) ? $request : $default;
        $values  = $this->app->data->create($value);

        $html = array();

        if ((int)$this->_params->get('order_show', 1) && !empty($orderList)) {

            if (empty($request)) {
                $values->set('reverse', (int)($default->order == 'desc'));
            }

            $orderMode = $this->_params->get('order_mode');

            $options = array();
            foreach ($orderList as $fieldId) {
                $name      = $this->app->jborder->getNameById($fieldId);
                $options[] = $this->app->html->_('select.option', $fieldId, $name);
            }

            $html[] = $this->app->html->_('zoo.genericlist', $options, 'order[field]', array(), 'value', 'text', $values->get('field'));
            $html[] = '<input type="hidden" name="order[mode]" value="' . $orderMode . '" />';
            $html[] = $this->app->jbhtml->checkbox(array('1' => JText::_('JBZOO_ORDER_REVERSE')), 'order[reverse]', '', $values->get('reverse'));

        } else {

            foreach ($default as $key => $value) {
                $html[] = '<input type="hidden" name="order[' . $key . ']" value="' . $value . '" />';
            }

        }

        return implode("\n ", $html);
    }

    /**
     * @return array
     */
    public function getOrderList()
    {
        $orderList = $this->_params->get('order_list', array());

        return $orderList;
    }
}
