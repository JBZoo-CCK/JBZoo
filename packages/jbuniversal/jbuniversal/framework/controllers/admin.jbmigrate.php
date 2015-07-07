<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBMigrateJBUniversalController.
 *
 * @package      JBUniversal.JBMigrate
 * @author       Alexander Oganov <t_tapak@yahoo.com>
 * @version      1.0
 * @since        Release 2.2(Beta)
 */
class JBMigrateJBUniversalController extends JBUniversalController
{
    /**
     * @type AppData
     */
    public $data;

    /**
     * @type JBMigrateHelper
     */
    protected $_jbmigrate;

    /**
     * @type JBSessionHelper
     */
    protected $_jbsession;

    /**
     * @param AppHelper $app
     * @param array     $config
     * @throws AppException
     */
    public function __construct($app, $config)
    {
        parent::__construct($app, $config);

        // get link to helpers
        $this->_jbsession = $app->jbsession;
        $this->_jbmigrate = $app->jbmigrate;
    }

    /**
     * Default task.
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     *
     */
    public function lastStep()
    {
        $request = $this->_jbrequest->getAdminForm();
        $request = $this->app->data->create($request);

        $types  = $request->get('type_list', array());
        $prices = $this->_jbmigrate->getPriceList($types);

        $data['orderApp'] = $request->get('order_app', array());
        $data['template'] = $request->get('template', array());

        $priceData = $this->_jbmigrate->extractPriceData($prices);
        $data      = array_merge($data, (array)$priceData);

        $this->data    = $this->app->data->create($data);
        $data['price'] = (array)$data['price'];

        $this->_jbsession->setGroup((array)$data, 'migrate_config');

        $this->renderView();
    }

    /**
     *
     */
    public function doStep()
    {
        $request = $this->_jbrequest->getAdminForm();
        $request = $this->app->data->create($request);

        $config = $this->app->data->create($this->_jbsession->getGroup('migrate_config', array()));

        $price = $this->_jbmigrate->create('price', $config->get('price', array()));
        $config->set('price', $price);

        // Update session data for price.
        $this->_jbsession->setGroup((array)$config, 'migrate_config');

        $curList = array_filter((array)$request->get('currency_list', array()));
        if ($curList) {
            $this->_jbmigrate->create('currency', $curList);
        }

        $price = (array)$config->get('price', array());
        if ($price) {
            $this->_jbmigrate->create('priceElements', $config->get('price', array()));
        }


        die;
    }

    /**
     *
     */
    public function ajaxGetAppPrices()
    {
        $types = $this->_jbrequest->get('type_list');
    }
}
