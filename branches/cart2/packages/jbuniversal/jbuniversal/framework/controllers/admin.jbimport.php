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
 * Class JBImportJBuniversalController
 * JBZoo import controller for back-end
 */
class JBImportJBuniversalController extends JBuniversalController
{
    /**
     * @var JBSessionHelper
     */
    protected $_jbsession = null;

    /**
     * @var JBUserHelper
     */
    protected $_jbuser = null;

    /**
     * @var JBImportHelper
     */
    protected $_jbimport = null;

    /**
     * @var array
     */
    private $_defaultParams = array(
        'header'    => 1,
        'separator' => ',',
        'enclosure' => '"',
        'step'      => '25',
    );

    /**
     * Constrictor
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        // get link to helpers
        $this->_jbsession = $this->app->jbsession;
        $this->_jbimport  = $this->app->jbimport;
        $this->_jbuser    = $this->app->jbuser;
    }

    /**
     * Index page
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     * Items import page
     */
    public function items()
    {
        $this->_jbsession->clearGroup('import');
        $this->importParams = $this->_config->getGroup('import.items', $this->_jbuser->getParam('import-items', $this->_defaultParams));

        $this->renderView();
    }

    /**
     * Categories import page
     */
    public function categories()
    {
        $this->_jbsession->clearGroup('import');
        $this->importParams = $this->_config->getGroup('import.categories', $this->_jbuser->getParam('import-categories', $this->_defaultParams));

        $this->renderView();
    }

    /**
     * Import dialog action
     */
    public function assign()
    {
        $csvfile    = $this->_jbrequest->getFile('csvfile', JBRequestHelper::ADMIN_FORM_KEY);
        $request    = $this->_jbrequest->getAdminForm();
        $importType = isset($request['import-type']) ? $request['import-type'] : null;

        if (empty($request['separator'])) {
            $request['separator'] = $this->_defaultParams['separator'];
        }

        if (empty($request['enclosure'])) {
            $request['enclosure'] = $this->_defaultParams['enclosure'];
        }

        if (empty($request['step'])) {
            $request['step'] = (int)$this->_defaultParams['step'];
        }

        // validate upload file
        try {
            $userfile = $this->app->validator->create('file', array('extension' => array('csv')))->clean($csvfile);
        } catch (AppException $e) {
            $this->app->jbnotify->notice(JText::_('JBZOO_UNABLE_TO_UPLOAD_FILE') . ' (' . $e . ')');
            $this->setRedirect($this->app->jbrouter->admin(array('task' => $importType)));
            return;
        }

        // upload file
        $file = $this->_jbimport->getTmpFilename();
        if (JFile::upload($userfile['tmp_name'], $file)) {

            // prepare session data
            $this->_config->setGroup('import.' . $importType, $request);

            $request['file'] = $file;

            // save to session
            $this->_jbsession->setGroup($request, 'import');

            // prepare data
            $this->info = $this->_jbimport->getInfo($file, $request);
            $this->_jbsession->set('count', $this->info['count'], 'import');

            // render pseudo template
            if ($importType == 'items') {

                // some vars for template
                $this->controls   = $this->_jbimport->itemsControls($this->info);
                $this->prevParams = $this->_config->getGroup('import.last.items', $this->_jbuser->getParam('lastImport-items'));

                $this->renderView('items');

            } else if ($importType == 'categories') {

                // some vars for template
                $this->controls   = $this->_jbimport->categoriesControls($this->info);
                $this->prevParams = $this->_config->getGroup('import.last.categories', $this->_jbuser->getParam('lastImport-categories'));

                $this->renderView('categories');

            } else {
                jexit('Unknown import type!'); // TODO replace to exception
            }

        } else {
            $this->app->error->raiseNotice(0, JText::_('JBZOO_CHECK_TEMP_PERMISIONS'));
            $this->setRedirect($this->baseurl . '&task=index');
            return;
        }
    }

    /**
     * Show steps
     */
    public function itemsSteps()
    {
        $typeid       = $this->_jbrequest->get('typeid');
        $checkOptions = $this->_jbrequest->get('checkOptions');
        $lose         = $this->_jbrequest->get('lose');
        $key          = $this->_jbrequest->get('key');
        $create       = $this->_jbrequest->get('create');
        $createAlias  = $this->_jbrequest->get('createAlias', 0);
        $cleanPrice   = $this->_jbrequest->get('cleanPrice', 0);
        $assign       = $this->_jbrequest->getArray('assign');
        $appid        = (int)$this->_jbrequest->get('appid');

        if (empty($appid) || empty($typeid) || !isset($assign[$typeid]) || empty($assign[$typeid])) {
            $this->app->jbnotify->notice(JText::_('JBZOO_INCORRECT_DATA'));
            $this->setRedirect($this->app->jbrouter->admin(array('task' => 'index')));
        }

        $data = array(
            'appid'        => $appid,
            'typeid'       => $typeid,
            'lose'         => $lose,
            'key'          => $key,
            'create'       => $create,
            'assign'       => $assign[$typeid],
            'checkOptions' => $checkOptions,
            'createAlias'  => $createAlias,
            'cleanPrice'   => $cleanPrice,
        );

        $this->_jbsession->setBatch($data, 'import');

        // save to user params
        $oldParams = (array)$this->_jbuser->getParam('lastImport-items', array());
        $params    = $this->_config->getGroup('import.last.items', $oldParams);

        $params[$typeid] = $assign[$typeid];
        unset($data['assign']);
        $params['previousparams'] = $data;
        $this->_config->setGroup('import.last.items', $params);

        $this->renderView();
    }

    /**
     * Categories steps action
     */
    public function categoriesSteps()
    {
        $lose        = $this->_jbrequest->get('lose');
        $key         = $this->_jbrequest->get('key');
        $assign      = $this->_jbrequest->getArray('assign');
        $create      = $this->_jbrequest->get('create');
        $createAlias = $this->_jbrequest->get('createAlias', 0);
        $appid       = (int)$this->_jbrequest->get('appid');

        if (empty($appid) || empty($assign)) {
            $this->app->jbnotify->notice(JText::_('JBZOO_INCORRECT_DATA'));
            $this->setRedirect($this->app->jbrouter->admin(array('task' => 'index')));
        }

        $data = array(
            'appid'       => $appid,
            'lose'        => $lose,
            'key'         => $key,
            'create'      => $create,
            'createAlias' => $createAlias,
            'assign'      => $assign,
        );

        $this->_jbsession->setBatch($data, 'import');

        // save to user params
        $this->_config->setGroup('import.last.categories', $data);

        $this->renderView();
    }

    /**
     * One ajax step for items import
     */
    public function oneStep()
    {
        try {
            $page       = (int)$this->_jbrequest->get('page');
            $importType = $this->_jbrequest->get('import-type');
            $result     = array();

            if ($importType == 'items') {
                $result = $this->_jbimport->itemsProcess($page);

            } else if ($importType == 'categories') {
                $result = $this->_jbimport->categoriesProcess($page);
            }

            $this->app->jbajax->send($result);

        } catch (Exception $e) {
            jexit("Exception: " . $e->getMessage());
        }
    }

    /**
     * Call after all items loaded
     */
    public function postImport()
    {
        try {
            $importType = $this->_jbrequest->get('import-type');

            if ($importType == 'items') {
                $this->_jbimport->itemsPostProcess();

            } else if ($importType == 'categories') {
                $this->_jbimport->categoriesPostProcess();
            }

            // remove all csv files
            $files = (array)JFolder::files($this->app->jbpath->sysPath('tmp'), '\.csv');
            foreach ($files as $csvFile) {
                JFile::delete($csvFile);
            }

            $this->app->jbajax->send();

        } catch (Exception $e) {
            jexit("Exception: " . $e->getMessage());
        }
    }

    /**
     * Standard
     */
    public function standard()
    {
        $applications = $this->app->table->application->all();

        $this->appList = array();
        foreach ($applications as $application) {
            $this->appList[] = array(
                'name'     => $application->name,
                'iconPath' => $application->getIcon(),
                'link'     => array(
                    'controller' => 'configuration',
                    'task'       => 'importexport',
                    'changeapp'  => $application->id,
                ),
            );
        }

        $this->renderView();
    }

}
