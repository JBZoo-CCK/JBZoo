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
        'enclosure' => '"'
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
        $this->_jbexport = $this->app->jbexport;
        $this->_jbuser   = $this->app->jbuser;
    }

    /**
     * Standart export methods
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     * Standart
     */
    public function standart()
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


    /**
     * Export dialog action for items
     */
    public function items()
    {
        if (!$this->_jbrequest->isPost()) {
            $this->exportParams = $this->_config->getGroup('export.items', $this->_jbuser->getParam('export-items'));
            $this->_setExportParams();

            $this->renderView();

        } else {
            try {
                $request = $this->app->data->create($this->_jbrequest->getAdminForm());

                $data['separator'] = $request->get('separator');
                $data['enclosure'] = $request->get('enclosure');

                $data['separator'] = empty($data['separator']) ? $this->_defaultParams['separator'] : $data['separator'];
                $data['enclosure'] = empty($data['enclosure']) ? $this->_defaultParams['enclosure'] : $data['enclosure'];

                $request->remove('separator');
                $request->remove('enclosure');

                $this->_config->setGroup('export.items', $request);
                $this->_config->setGroup('export', $data);

                $this->_jbexport->clean();

                list($appId, $catId) = explode(':', $request->get('items_app_category', '0:0'));
                $files = $this->_jbexport->itemsToCSV($appId, $catId, $request->get('type', null), $request);

                if (!empty($files)) {
                    $tmpArch = $this->app->jbarch->compress($files, 'jbzoo-export-items-' . date('Y-m-d_H-i'));
                } else {
                    throw new AppException(JText::_('JBZOO_EXPORT_ITEMS_NOT_FOUND'));
                }

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->app->filesystem->output($tmpArch);
                    JFile::delete($tmpArch);
                    JFolder::delete($this->app->jbpath->sysPath('tmp', '/jbzoo-export'));
                    JExit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {
                $this->app->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
                $this->setRedirect($this->app->jbrouter->admin(array('task' => 'items')));
            }
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
                $request = $this->app->data->create($this->_jbrequest->getAdminForm());

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
                    $tmpArch = $this->app->jbarch->compress($files, 'jbzoo-export-categories-' . date('Y-m-d_H-i'));
                } else {
                    throw new AppException(JText::_('JBZOO_EXPORT_CATEGORIES_NOT_FOUND'));
                }

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->app->filesystem->output($tmpArch);
                    JFile::delete($tmpArch);
                    JFolder::delete($this->app->jbpath->sysPath('tmp', '/jbzoo-export'));
                    JExit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {
                $this->app->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
                $this->setRedirect($this->app->jbrouter->admin(array('task' => 'categories')));
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
                $files = JFolder::files($this->app->path->path('jbapp:types'), 'config', false, true);

                if (!empty($files)) {
                    $tmpArch = $this->app->jbarch->compress($files, 'jbzoo-export-types-' . date('Y-m-d_H-i'));
                } else {
                    throw new AppException(JText::_('JBZOO_EXPORT_TYPES_NOT_FOUND'));
                }

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->app->filesystem->output($tmpArch);
                    JFile::delete($tmpArch);
                    JFolder::delete($this->app->jbpath->sysPath('tmp', '/jbzoo-export'));
                    JExit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {
                $this->app->jbnotify->notice(JText::_('Error create export file') . ' (' . $e . ')');
                $this->setRedirect($this->app->jbrouter->admin(array('task' => 'types')));
            }
        }
    }

    /**
     * Create Zoo back
     */
    public function zooBackup()
    {
        $this->app->jbtables->checkSku();
        $this->app->jbtables->checkFavorite();

        $this->renderView();
    }

    /**
     * Export to yandex YML
     */
    public function yandexYml()
    {
        $this->app->jbyml->init();

        $this->indexStep = 25;
        $this->total     = $this->app->jbyml->getTotal();

        $this->renderView();
    }

    /**
     *
     */
    public function writeStep()
    {
        $limit  = 25;
        $page   = (int)$this->app->jbrequest->get('page', 0);
        $offset = $limit * $page;

        $this->app->jbyml->init();

        try {
            if ($page == 0) {
                $this->app->jbyml->renderStart();
            }

            $lines   = $this->app->jbyml->exportItems($offset, $limit);
            $total   = $this->app->jbyml->getTotal();
            $current = $limit * ($page + 1);

            if ($current > $total) {
                $current = $total;
            }

            $progress = round($current * 100 / $total, 2);

            if ($progress == 100) {
                $this->app->jbyml->renderFinish();
            }
        } catch (AppException $exception) {
            JExit($exception->getMessage());
        }

        $this->app->jbajax->send(array(
            'progress' => $progress,
            'current'  => $current,
            'total'    => $total,
            'lines'    => $lines,
            'step'     => $page + 1,
            'stepsize' => $limit,
            'ymlcount' => $this->app->jbsession->get('ymlCount', 'yml')
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
