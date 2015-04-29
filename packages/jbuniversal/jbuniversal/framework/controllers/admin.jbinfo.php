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


/**
 * Class JBInfoJBuniversalController
 */
class JBInfoJBUniversalController extends JBUniversalController
{

    /**
     * Index action
     */
    public function index()
    {
        $this->image = $this->app->path->url('jbapp:application_info.png');
        $xmlFile     = $this->app->path->path('jbapp:application.xml');
        $xml         = simplexml_load_file($xmlFile);

        $this->metadata = $data = $this->app->data->create(array(
            'name'         => (string)$xml->name,
            'creationdate' => $xml->creationDate ? (string)$xml->creationDate : 'Unknown',
            'author'       => $xml->author ? (string)$xml->author : 'Unknown',
            'copyright'    => (string)$xml->copyright,
            'authorEmail'  => (string)$xml->authorEmail,
            'authorUrl'    => (string)$xml->authorUrl,
            'version'      => (string)$xml->version,
            'description'  => (string)$xml->description,
            'license'      => (string)$xml->license,
        ));

        $this->renderView();
    }

    /**
     * System report action
     */
    public function systemReport()
    {
        if (!$this->_jbrequest->isPost()) {
            $this->renderView();

        } else {
            $this->app->jbdoc->disableTmpl();

            try {

                ob_start();
                $this->renderView('result');
                $content = ob_get_contents();
                $content .= '<!--' . $this->app->zoo->getApplication()->getHash($content) . '-->';
                ob_end_clean();

                $tmpPath = $this->app->jbpath->sysPath('tmp', '/jbzoo-system-report-' . time() . '.html');
                $tmpArch = $this->app->jbpath->sysPath('tmp', '/jbzoo-system-report-' . time() . '.zip');

                if (JFile::exists($tmpPath)) {
                    JFile::delete($tmpPath);
                }

                if (JFile::exists($tmpArch)) {
                    JFile::delete($tmpArch);
                }

                JFile::write($tmpPath, $content);

                $zip = $this->app->archive->open($tmpArch, 'zip');
                $zip->create(array($tmpPath), PCLZIP_OPT_REMOVE_ALL_PATH);

                if (is_readable($tmpArch) && JFile::exists($tmpArch)) {
                    $this->app->filesystem->output($tmpArch);
                    JFile::delete($tmpPath);
                    JFile::delete($tmpArch);
                    jexit();
                } else {
                    throw new AppException(JText::sprintf('Unable to create file %s', $tmpArch));
                }

            } catch (AppException $e) {

                // raise error on exception
                $this->app->error->raiseNotice(0, JText::_('Error create report') . ' (' . $e . ')');

                $this->setRedirect($this->app->jbrouter->admin(array('task' => 'systemReport')));

                return;
            }
        }
    }

    /**
     * Check requirements
     */
    public function requirements()
    {
        $this->app->loader->register('AppRequirements', 'installation:requirements.php');

        $this->requirements = $this->app->object->create('AppRequirements');
        $this->requirements->checkRequirements();

        $this->renderView();
    }

    /**
     * Performance test
     */
    public function performance()
    {
        $this->testList = $this->app->jbperform->getTestData();

        $this->renderView();
    }

    /**
     * Performance test execute action
     */
    public function performanceStep()
    {
        $testName = $this->_jbrequest->getWord('testname');
        $result   = $this->app->jbperform->execTest($testName);

        $this->app->jbajax->send($result);
    }

    /**
     * Performance report
     */
    public function performanceReport()
    {
        $prevData = $this->app->jbsession->getGroup('benchmark');
        $tests    = $this->app->jbperform->getStdValues();

        if (count($tests) != count($prevData)) {
            $this->app->jbnotify->notice(JText::_('JBZOO_PERFORMANCE_REPORT_NO_DATA'));
            $this->setRedirect($this->app->jbrouter->admin(array('task' => 'performance')));
        }

        if ($this->_jbrequest->isPost()) {

            $sendData = array(
                'data'   => array(
                    'hosting' => $this->_jbrequest->getAdminForm(),
                    'tests'   => $prevData,
                    'host'    => JUri::root(),
                    'jbuser'  => JBZOO_USERNAME,
                ),
                'method' => 'add-hosting',
            );

            $this->app->jbhttp->request('http://stats.jbzoo.com/api', $sendData);

            $this->setRedirect($this->app->jbrouter->admin(array('task' => 'performance')), JText::_('JBZOO_PERFORMANCE_REPORT_THANK_YOU'));
        }

        $this->renderView();
    }

    /**
     * Show licence form
     */
    public function licence()
    {
        $this->renderView();
    }

    /**
     * Save licence data
     */
    public function licenceSave()
    {
        // define vars
        $app     = $this->app->zoo->getApplication();
        $licData = $app->clearLData($_POST['jbzooform']);
        $host    = $app->getDomain(true);

        // save new lic data
        $domainPath = $this->app->path->path('jbapp:config') . '/licence.' . $host . '.php';
        $this->app->jbconfig->saveToFile($licData, $domainPath);

        // cleanup cache
        $this->app->jbcache->clear('data-' . $host);
        $tmpPath = $this->app->path->path('jbapp:tmp') . '/data-' . $host;
        if (JFile::exists($tmpPath)) {
            JFile::delete($tmpPath);
        }

        $redirectUrl = $this->app->jbrouter->admin(array('task' => 'index'));
        if (!empty($_POST['jbzooform']['redirect'])) {
            $redirectUrl = base64_decode($_POST['jbzooform']['redirect']);
        }

        $this->setRedirect($redirectUrl, JText::_('JBZOO_LICENCE_SAVED'));
    }

}