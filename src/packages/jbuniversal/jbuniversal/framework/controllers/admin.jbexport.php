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
 * Class JBExportJBuniversalController
 * JBZoo export controller for back-end
 */
class JBExportJBuniversalController extends JBuniversalController
{
    /**
     * @var JBExportHelper
     */
    protected $_jbexport = null;

    /**
     * @var JBUserHelper
     */
    protected $_jbuser = null;

    /**
     * @var array
     */
    private $_defaultParams = array(
        'separator' => ',',
        'enclosure' => '"',
        'step'      => 500,
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
        $this->_jbexport = $this->zoo->jbexport;
        $this->_jbuser   = $this->zoo->jbuser;
    }

    /**
     * Standard export methods
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     * Standard
     */
    public function standard()
    {
        $applications = $this->zoo->table->application->all();

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


    /**
     * Export dialog action for items
     */
    public function items()
    {
        $this->exportParams = $this->_config->getGroup('export.items', array());
        $this->renderView();
    }

    /**
     * Items steps
     */
    public function itemsSteps()
    {
        /** @var AppData $req */
        /** @var JBAjaxHelper $jbajax */
        /** @var JBSessionHelper $session */
        $req     = $this->zoo->data->create($this->_jbrequest->getAdminForm());
        $page    = $this->_jbrequest->get('page');
        $jbajax  = $this->zoo->jbajax;
        $session = $this->zoo->jbsession;

        try {
            $progress = 0;

            if ($page == -1) { // prepare

                if (!$req->get('type')) {
                    $this->zoo->jbnotify->notice('Please, select file type for export!');
                    $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'items')));
                    return;
                }

                $this->_config->setGroup('export.items', $req);
                $this->_config->setGroup('export', array( // for helpers
                    'separator' => $req->get('separator') ? $req->get('separator') : $this->_defaultParams['separator'],
                    'enclosure' => $req->get('enclosure') ? $req->get('enclosure') : $this->_defaultParams['enclosure'],
                    'step'      => $req->get('step') ? $req->get('step') : $this->_defaultParams['step'],
                ));

                $this->_jbexport->clean();

                // parse application and parent category
                list($appId, $categoryList) = explode(':', $req->get('items_app_category', '0:0'));
                $categoryList = (array)$categoryList;

                // get full category list
                if ((int)$req->get('category_nested') && !in_array('-1', $categoryList)) {
                    $categoryList += JBModelCategory::model()->getNestedCategories($categoryList); // сделать доп опцию
                }

                // Get total
                $total = JBModelItem::model()->getTotal($appId, $req->get('type'), $categoryList, $req->get('state', 0));

                // save to session
                $session->setGroup(array(
                    'steps' => ceil($total / $req->get('step')),
                    'appId' => $appId,
                    'catId' => $categoryList,
                    'total' => $total,
                    'files' => array(),
                    'req'   => (array)$req,
                ), 'export.items');

                $this->renderView();
                return; // render progress page
            }

            if ($page >= 0) { // process each export step

                $sesData = $this->zoo->data->create($session->getGroup('export.items'));
                $config  = $this->_config->getGroup('export.items');
                $config->set('limit', array($page * $config->get('step'), $config->get('step')));

                $this->_jbexport->itemsToCSV($sesData->get('appId'), $sesData->get('catId'), $sesData->find('req.type'), $config);

                $progress = (($page + 1) / $sesData->get('steps')) * 100;
            }

            $jbajax->send(array(
                'page'     => ++$page,
                'progress' => round($progress, 2),
            ));

        } catch (AppException $e) {
            $this->zoo->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
            $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'items')));
        }
    }

    /**
     * Last export step
     * @throws AppException
     */
    public function itemsDownload()
    {
        $tmpArch = null;

        if ($compressFiles = $this->_jbexport->splitFiles()) {
            // $tmpArch = $this->zoo->jbarch->compress($compressFiles, 'jbzoo-export-items-' . date('Y-m-d_H-i')); todofixj4
            $tmpArch = $compressFiles[0];
        } else {
            $this->zoo->jbnotify->notice(JText::_('JBZOO_EXPORT_ITEMS_NOT_FOUND'));
            $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'items')));
        }

        if ($tmpArch && is_readable($tmpArch) && JFile::exists($tmpArch)) {
            $this->zoo->filesystem->output($tmpArch);
            // JFile::delete($tmpArch);
            $this->_jbexport->clean();
            JExit();

        } else {
            $this->zoo->jbnotify->notice(JText::sprintf('Unable to create file %s', $tmpArch));
            $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'items')));
        }
    }

    /**
     * Export dialog action for categoris
     */
    public function categories()
    {
        if (!$this->_jbrequest->isPost()) {
            $this->exportParams = $this->_config->getGroup('export.categories', $this->_jbuser->getParam('export-categories'));
            $this->_setExportParams();

            $this->renderView();

        } else {

            try {
                $request = $this->zoo->data->create($this->_jbrequest->getAdminForm());

                $data['separator'] = $request->get('separator');
                $data['enclosure'] = $request->get('enclosure');

                $data['separator'] = empty($data['separator']) ? $this->_defaultParams['separator'] : $data['separator'];
                $data['enclosure'] = empty($data['enclosure']) ? $this->_defaultParams['enclosure'] : $data['enclosure'];

                $request->remove('separator');
                $request->remove('enclosure');

                $this->_config->setGroup('export.categories', $request);
                $this->_config->setGroup('export', $data);

                list($appId) = explode(':', $request->get('category_app', '0:'));
                $files = $this->_jbexport->categoriesToCSV($appId, $request);

                if (!empty($files)) {
                    // $tmpArch = $this->zoo->jbarch->compress($files, 'jbzoo-export-categories-' . date('Y-m-d_H-i')); //todofixj4
                    $tmpArch = $files[0];
                } else {
                    throw new AppException(JText::_('JBZOO_EXPORT_CATEGORIES_NOT_FOUND'));
                }

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->zoo->filesystem->output($tmpArch);
                    // JFile::delete($tmpArch);
                    $this->_jbexport->clean();
                    JExit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {
                $this->zoo->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
                $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'categories')));
            }
        }
    }

    /**
     * Export types
     */
    public function types()
    {
        if (!$this->_jbrequest->isPost()) {

            $this->renderView();
        } else {

            try {
                $files = JFolder::files($this->zoo->path->path('jbapp:types'), 'config', false, true);

                if (!empty($files)) {
                    // $tmpArch = $this->zoo->jbarch->compress($files, 'jbzoo-export-types-' . date('Y-m-d_H-i')); todofixj4
                    $tmpArch = $files[0];
                } else {
                    throw new AppException(JText::_('JBZOO_EXPORT_TYPES_NOT_FOUND'));
                }

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->zoo->filesystem->output($tmpArch);
                    // JFile::delete($tmpArch);
                    $this->_jbexport->clean();
                    JExit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {
                $this->zoo->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
                $this->setRedirect($this->zoo->jbrouter->admin(array('task' => 'types')));
            }
        }
    }

    /**
     * Create Zoo back
     */
    public function zooBackup()
    {
        $this->zoo->jbtables->checkSku();
        $this->zoo->jbtables->checkFavorite();

        $this->renderView();
    }

    /**
     * Export to yandex YML
     */
    public function yandexYml()
    {
        $this->zoo->jbyml->init();

        $this->indexStep = 25;
        $this->total     = $this->zoo->jbyml->getTotal();

        $this->renderView();
    }

    /**
     *
     */
    public function writeStep()
    {
        $limit  = 25;
        $page   = (int)$this->zoo->jbrequest->get('page', 0);
        $offset = $limit * $page;

        $this->zoo->jbyml->init();

        try {
            if ($page == 0) {
                $this->zoo->jbyml->renderStart();
            }

            $lines   = $this->zoo->jbyml->exportItems($offset, $limit);
            $total   = $this->zoo->jbyml->getTotal();
            $current = $limit * ($page + 1);

            if ($current > $total) {
                $current = $total;
            }

            $progress = round($current * 100 / $total, 2);

            if ($progress == 100) {
                $this->zoo->jbyml->renderFinish();
            }
        } catch (AppException $exception) {
            JExit($exception->getMessage());
        }

        $this->zoo->jbajax->send(array(
            'progress' => $progress,
            'current'  => $current,
            'total'    => $total,
            'lines'    => $lines,
            'step'     => $page + 1,
            'stepsize' => $limit,
            'ymlcount' => $this->zoo->jbsession->get('ymlCount', 'yml'),
        ));
    }

    /**
     * Set export params for Items & Categories
     */
    protected function _setExportParams()
    {
        $export = $this->_config->getGroup('export');

        $this->exportParams->set('separator', $export->get('separator', $this->_defaultParams['separator']));
        $this->exportParams->set('enclosure', $export->get('enclosure', $this->_defaultParams['enclosure']));
    }
}
