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
 * Class JBMigrateCartHelper
 */
class JBMigrateCartHelper extends AppHelper
{
    /**
     * @var array
     */
    protected $_elementMap = array(
        'text'     => 'text',
        'textarea' => 'textarea',
        'date'     => 'date',
        'email'    => 'email',
        'checkbox' => 'checkbox',
        'radio'    => 'radio',
        'select'   => 'select',
        'file'     => 'upload',
    );

    /**
     * @var JBMigrateHelper
     */
    protected $_migrate;

    /**
     * @var JBCartElementHelper
     */
    protected $_element;

    /**
     * @var JBModelConfig
     */
    protected $_confModel;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_migrate = $this->app->jbmigrate;
        $this->_element = $this->app->jbcartelement;

        $this->_confModel = JBModelConfig::model();
    }

    /**
     * Migrate cart config
     */
    public function basic()
    {
        $config     = $this->_migrate->getBasic();
        $cartConfig = $this->_confModel->getGroup('cart.config');

        if ($config->get('auth', 0)) {
            $cartConfig->set('access', 2);
        } else {
            $cartConfig->set('access', 1);
        }

        $cartConfig->set('default_currency', $config->get('currency', 'eur'));
        $cartConfig->set('enable', $config->get('enable', 1));
        $cartConfig->set('freeorder', $config->get('nopaid-order', 0));

        $this->_confModel->setGroup('cart.config', $cartConfig->getArrayCopy());
    }

    public function minimalsum()
    {
        $config = $this->_migrate->getMinimal();
        if ($config->get('minimal-summa') > 0) {

            $validator = $this->_element->create('minsum', JBCart::ELEMENT_TYPE_VALIDATOR, array(
                'value' => $config->get('minimal-summa') . ' ' . $this->_migrate->getCurrency(),
                'name'  => JText::_('JBZOO_MIGRATE_MINIMAL_NAME'),
            ));

            $validator->saveConfig();
        }
    }

    /**
     * Create payment elements
     */
    public function payments()
    {
        $robokassa = $this->_migrate->getRobokassa();
        if ($robokassa->get('robox-enabled')) {
            /** @var JBCartElementPaymentRobokassa $roboxElement */
            $roboxElement = $this->_element->create('robokassa', JBCart::ELEMENT_TYPE_PAYMENT, array(
                'modifytotal' => 0,
                'currency'    => 'rub',
                'login'       => $robokassa->get('robox-login'),
                'password1'   => $robokassa->get('robox-password1'),
                'password2'   => $robokassa->get('robox-password2'),
                'debug'       => $robokassa->get('robox-debug'),
            ));

            $roboxElement->saveConfig();
        }

        $ikassa = $this->_migrate->getInterkassa();
        if ($ikassa->get('ikassa-enabled')) {
            /** @var JBCartElementPaymentInterkassa $ikassaElement */
            $ikassaElement = $this->_element->create('interkassa', JBCart::ELEMENT_TYPE_PAYMENT, array(
                'modifytotal' => 0,
                'currency'    => 'rub',
                'shopid'      => $ikassa->get('ikassa-shopid'),
                'test_key'    => $ikassa->get('ikassa-key-test'),
                'key'         => $ikassa->get('ikassa-key'),
                'hash_method' => 'md5',
                'debug'       => 0,
            ));

            $ikassaElement->saveConfig();
        }

        $paypal = $this->_migrate->getPayPal();
        if ($paypal->get('paypal-enabled')) {
            /** @var JBCartElementPaymentPayPal $paypalElement */
            $paypalElement = $this->_element->create('paypal', JBCart::ELEMENT_TYPE_PAYMENT, array(
                'modifytotal' => 0,
                'currency'    => 'eur',
                'debug'       => $paypal->get('paypal-debug'),
                'email'       => $paypal->get('paypal-email'),
            ));

            $paypalElement->saveConfig();
        }

        $manual = $this->_migrate->getManual();
        if ($manual->get('manual-enabled')) {
            /** @var JBCartElementPaymentManual $manualElement */
            $manualElement = $this->_element->create('manual', JBCart::ELEMENT_TYPE_PAYMENT, array(
                'name'             => $manual->get('manual-title'),
                'redirect_url'     => $manual->get('page-success'),
                'redirect_message' => $manual->get('manual-message'),
            ));

            $manualElement->saveConfig();
        }
    }

    /**
     * Create emails
     */
    public function notificaction()
    {
        $config = $this->_migrate->getNotification();

        if ($config->get('notificaction-create')) {
            $emailCreate = $this->_element->create('sendemail', JBCart::ELEMENT_TYPE_NOTIFICATION, array(
                "name"         => JText::_('JBZOO_MIGRATE_NOTIFICATION_CREATE_NAME'),
                "layout_email" => "default",
                "subject"      => JText::_('JBZOO_MIGRATE_NOTIFICATION_CREATE_NAME'),
                "recipients"   => array(
                    "custom" => $config->get('admin-email'),
                ),
                "ishtml"       => "1",
                "issleep"      => "0",
            ));

            $emailCreate->saveConfig('order_saved');
        }

        if ($config->get('notificaction-payment')) {
            $emailCreated = $this->_element->create('sendemail', JBCart::ELEMENT_TYPE_NOTIFICATION, array(
                "name"         => JText::_('JBZOO_MIGRATE_NOTIFICATION_PAYMENT_NAME'),
                "layout_email" => "default",
                "subject"      => JText::_('JBZOO_MIGRATE_NOTIFICATION_PAYMENT_NAME'),
                "recipients"   => array(
                    "custom" => $config->get('admin-email'),
                ),
                "ishtml"       => "1",
                "issleep"      => "0",
            ));

            $emailCreated->saveConfig('order_paymentsuccess');
        }

        if ($config->get('notificaction-payment') || $config->get('notificaction-create')) {

            //$elem->saveConfig('default.title');
            $this->_confModel->setGroup('cart.email_tmpl.default', array(
                'title' => array(
                    $this->_element->create('textarea', JBCart::ELEMENT_TYPE_EMAIL, array(
                        'showlabel' => '0',
                        'text'      => '{order_name} - {site_name}',
                    ))->config,
                ),
                'body'  => array(
                    $this->_element->create('items', JBCart::ELEMENT_TYPE_EMAIL, array(
                        'name'              => JText::_('JBZOO_MIGRATE_EMAIL_ITEMS_NAME'),
                        'currency'          => $this->_migrate->getCurrency(),
                        'showlabel'         => '1',
                        'subtotal'          => '0',
                        'payment'           => '0',
                        'shipping'          => '0',
                        'modifiers'         => '0',
                        'total'             => '1',
                        'tmpl_item_link'    => '1',
                        'tmpl_image_show'   => '0',
                        'tmpl_image_link'   => '0',
                        'tmpl_image_width'  => '75',
                        'tmpl_image_height' => '75',
                        'tmpl_sku_show'     => '1',
                        'tmpl_price4one'    => '1',
                        'tmpl_quntity'      => '1',
                        'tmpl_subtotal'     => '1',
                    ))->config,
                    $this->_element->create('fields', JBCart::ELEMENT_TYPE_EMAIL, array(
                        'name'      => '',
                        'showlabel' => '1',
                        'fields'    => array(/* TODO ADD assigned fields */
                        ),
                    ))->config,
                ),
            ));
        }

    }

    /**
     * Create form fields
     */
    public function formFields()
    {
        $configModel  = JBModelConfig::model();
        $application  = $this->_migrate->getCartApp();
        $templatePath = null;
        $orderFields  = null;

        if ($application) {
            $templatePath = $application->getTemplate()->getPath();
            $orderFields  = $this->_migrate->getOrderFields();
        }

        if ($templatePath && $orderFields) {
            $posFile = $templatePath . '/renderer/item/positions.config';

            $renderParams = array();
            if (JFile::exists($posFile)) {
                $posFile = $this->app->data->create(json_decode(file_get_contents($posFile), true));
                foreach ($posFile->get('jbuniversal.order.order', array()) as $position) {
                    foreach ($position as $element) {
                        $renderParams[$element['element']] = $element;
                    }
                }
            }

            foreach ($renderParams as $elemId => $renderParam) {

                if (!isset($orderFields[$elemId])) {
                    continue;
                }

                /**
                 * @var AppData $config
                 * @var AppData $renderParam
                 */
                $config      = $this->app->data->create($orderFields[$elemId]);
                $renderParam = $this->app->data->create($renderParam);

                $elemType = isset($this->_elementMap[$config['type']]) ? $this->_elementMap[$config['type']] : 'text';

                $orderElement = $this->_element->create($elemType, JBCart::ELEMENT_TYPE_ORDER, array(
                    'name'        => $config->get('name'),
                    'description' => $config->get('description'),
                    'access'      => $config->get('access'),
                    'default'     => $config->get('default'),
                    'option'      => $config->get('option'),
                    'multiple'    => $config->get('multiple'),
                ));

                $list = $configModel->getGroup('cart.field');

                $list[JBCart::DEFAULT_POSITION][$orderElement->identifier] = (array)$orderElement->config;
                $configModel->setGroup('cart.field', $list);


                $renderElement = array(
                    'user_field' => '',
                    'showlabel'  => '0',
                    'altlabel'   => $renderParam->get('altlabel'),
                    'required'   => $renderParam->get('required', 0),
                    'type'       => $orderElement->getElementType(),
                    'group'      => $orderElement->getElementGroup(),
                    'identifier' => $orderElement->identifier,
                );

                $list = $configModel->getGroup('cart.field_tmpl.default');

                $list[JBCart::DEFAULT_POSITION][] = $renderElement;
                $configModel->setGroup('cart.field_tmpl.default', $list);
            }
        }

    }

}

