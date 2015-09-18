<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBMigrateHelper
 */
class JBMigrateHelper extends AppHelper
{

    const STEP_SIZE = 100;

    /**
     * @var array
     */
    protected $_elementMap = array(
        'text'     => '0.value',
        'textarea' => '0.value',
        'option'   => 'options',
        'file'     => 'file',
    );

    /**
     * @var AppData
     */
    protected $_cartConfig = null;

    /**
     * @var AppData
     */
    protected $_params = null;

    /**
     * @var JBSessionHelper
     */
    protected $_session = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_session = $this->app->jbsession;
    }

    /**
     * @param AppData $params
     */
    public function prepare($params)
    {
        $cartParameters = array();
        if ($application = $this->getCartApp()) {
            $cartParameters = $application->params->find('global.jbzoo_cart_config.', array());
        }

        $this->_session->clearGroup('migration');
        $this->_session->set('params', (array)$params, 'migration');
        $this->_session->set('oldConfig', (array)$cartParameters, 'migration');

        $this->setParams('steps', $this->getStepsInfo());
    }

    /**
     * @return AppData
     */
    public function getOldConfig()
    {
        return $this->app->data->create($this->_session->get('oldConfig', 'migration'));
    }

    /**
     * @return AppData
     */
    public function getParams()
    {
        return $this->app->data->create($this->_session->get('params', 'migration', array()));
    }

    /**
     * @return AppData
     */
    public function setParams($key, $value)
    {
        $params = $this->getParams();
        $params->set($key, $value);

        $params = $this->app->jbarray->mapRecursive(function ($value) {

            if (is_array($value) || $value instanceof AppData) {
                $value = (array)$value;
            }

            return $value;

        }, $params);

        $this->_session->set('params', (array)$params, 'migration');
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return strtolower($this->getOldConfig()->get('currency', 'eur'));
    }

    /**
     * @return AppData
     */
    public function getRobokassa()
    {
        return $this->app->data->create(array(
            'robox-enabled'   => $this->getOldConfig()->get('robox-enabled', 0),
            'robox-debug'     => $this->getOldConfig()->get('robox-debug'),
            'robox-login'     => $this->getOldConfig()->get('robox-login'),
            'robox-password1' => $this->getOldConfig()->get('robox-password1'),
            'robox-password2' => $this->getOldConfig()->get('robox-password2'),
        ));
    }

    /**
     * @return AppData
     */
    public function getInterkassa()
    {
        return $this->app->data->create(array(
            'ikassa-enabled'  => $this->getOldConfig()->get('ikassa-enabled', 0),
            'ikassa-new'      => $this->getOldConfig()->get('ikassa-new'),
            'ikassa-shopid'   => $this->getOldConfig()->get('ikassa-shopid'),
            'ikassa-key'      => $this->getOldConfig()->get('ikassa-key'),
            'ikassa-key-test' => $this->getOldConfig()->get('ikassa-key-test'),
        ));
    }

    /**
     * @return AppData
     */
    public function getManual()
    {
        return $this->app->data->create(array(
            'payment-enabled'   => $this->getOldConfig()->get('payment-enabled', 0),
            'manual-enabled'    => $this->getOldConfig()->get('manual-enabled'),
            'manual-title'      => $this->getOldConfig()->get('manual-title'),
            'manual-text'       => $this->getOldConfig()->get('manual-text'),
            'manual-message'    => $this->getOldConfig()->get('manual-message'),
            'page-success'      => $this->getOldConfig()->get('page-success'),
            'payment-page-fail' => $this->getOldConfig()->get('payment-page-fail'),
        ));
    }

    /**
     * @return AppData
     */
    public function getPayPal()
    {
        return $this->app->data->create(array(
            'paypal-enabled' => $this->getOldConfig()->get('paypal-enabled', 0),
            'paypal-debug'   => $this->getOldConfig()->get('paypal-debug'),
            'paypal-email'   => $this->getOldConfig()->get('paypal-email'),
        ));
    }

    /**
     * @return AppData
     */
    public function getNotification()
    {
        return $this->app->data->create(array(
            'notificaction-create'  => $this->getOldConfig()->get('notificaction-create', 0),
            'notificaction-payment' => $this->getOldConfig()->get('notificaction-payment'),
            'admin-email'           => $this->getOldConfig()->get('admin-email'),
        ));
    }

    /**
     * @return AppData
     */
    public function getBasic()
    {
        return $this->app->data->create(array(
            'enable'       => $this->getOldConfig()->get('enable', 0),
            'auth'         => $this->getOldConfig()->get('auth'),
            'nopaid-order' => $this->getOldConfig()->get('nopaid-order'),
            'currency'     => strtolower($this->getOldConfig()->get('currency', 'eur')),
        ));
    }

    /**
     * @return AppData
     */
    public function getMinimal()
    {
        return $this->app->data->create(array(
            'minimal-summa' => $this->getOldConfig()->get('minimal-summa'),
        ));
    }

    /**
     * @return Application|null
     */
    public function getCartApp()
    {
        return $this->app->table->application->get($this->getParams()->get('app'));
    }

    /**
     * @return Type|null
     */
    public function getOrderType()
    {
        if ($application = $this->getCartApp()) {
            return $application->getType($this->getParams()->get('orders_type'));
        }

        return null;
    }

    /**
     * @return array
     */
    public function getOrderFields()
    {
        $type        = $this->getOrderType();
        $elemsConfig = $type->config->get('elements');

        $fieldList = array();
        foreach ($elemsConfig as $elemId => $elemConfig) {

            if ($elemConfig['type'] != 'jbbasketitems' && strpos($elemId, '_') !== 0) {

                $map = isset($this->_elementMap[$elemConfig['type']]) ? $this->_elementMap[$elemConfig['type']] : null;

                $elemConfig['_value_map'] = $map;
                $fieldList[$elemId]       = $elemConfig;
            }
        }

        return $fieldList;
    }

    /**
     * @return int
     */
    public function getStepsInfo()
    {
        $result = array(
            'step'         => self::STEP_SIZE,
            'system_steps' => 1,
            'steps'        => 0,
            'orders'       => 0,
            'items'        => 0,
        );

        $params     = $this->getParams();
        $modelItems = JBModelItem::model();

        if ($params->get('prices_enable')) {
            $result['system_steps']++;
        }

        if ($params->get('orders_enable', 0)) {
            $result['orders']       = $modelItems->getTotal($params->get('app'), $params->get('orders_type'));
            $result['orders_steps'] = ceil($result['orders'] / $result['step']);
        }

        if ($params->get('prices_enable', 0)) {
            $result['items']       = $modelItems->getTotal($params->get('prices_app'), $params->get('prices_types'));
            $result['items_steps'] = ceil($result['items'] / $result['step']);
        }

        $result['total'] = $result['items'] + $result['orders'];
        $result['steps'] += ceil($result['total'] / $result['step']);
        $result['steps'] += $result['system_steps'];

        return $result;
    }

}