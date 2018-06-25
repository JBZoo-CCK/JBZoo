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
        $xmlFile = $this->app->path->path('jbapp:application.xml');
        $xml = simplexml_load_string(file_get_contents($xmlFile));

        $this->metadata = $data = $this->app->data->create([
            'name'         => (string)$xml->name,
            'creationdate' => $xml->creationDate ? (string)$xml->creationDate : 'Unknown',
            'author'       => $xml->author ? (string)$xml->author : 'Unknown',
            'copyright'    => (string)$xml->copyright,
            'authorEmail'  => (string)$xml->authorEmail,
            'authorUrl'    => (string)$xml->authorUrl,
            'version'      => (string)$xml->version,
            'description'  => (string)$xml->description,
            'license'      => (string)$xml->license,
        ]);

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
                $zip->create([$tmpPath], PCLZIP_OPT_REMOVE_ALL_PATH);

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

                $this->setRedirect($this->app->jbrouter->admin(['task' => 'systemReport']));

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
        $result = $this->app->jbperform->execTest($testName);

        $this->app->jbajax->send($result);
    }

    /**
     * Performance report
     */
    public function performanceReport()
    {
        $prevData = $this->app->jbsession->getGroup('benchmark');
        $tests = $this->app->jbperform->getStdValues();

        if (count($tests) != count($prevData)) {
            $this->app->jbnotify->notice(JText::_('JBZOO_PERFORMANCE_REPORT_NO_DATA'));
            $this->setRedirect($this->app->jbrouter->admin(['task' => 'performance']));
        }

        if ($this->_jbrequest->isPost()) {

            $sendData = [
                'data'   => [
                    'hosting' => $this->_jbrequest->getAdminForm(),
                    'tests'   => $prevData,
                    'host'    => JUri::root(),
                    'jbuser'  => JBZOO_USERNAME,
                ],
                'method' => 'add-hosting',
            ];

            $this->app->jbhttp->request('http://stats.jbzoo.com/api', $sendData);

            $this->setRedirect($this->app->jbrouter->admin(['task' => 'performance']),
                JText::_('JBZOO_PERFORMANCE_REPORT_THANK_YOU'));
        }

        $this->renderView();
    }
}