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
 * Class JBToolsJBUniversalController
 * JBZoo tools controller for back-end
 */
class JBToolsJBUniversalController extends JBUniversalController
{
    const INDEX_STEP = 100;

    /**
     * Index page
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     * Database reindex page
     */
    public function reindex()
    {
        $this->indexStep = self::INDEX_STEP;
        $this->total     = JBModelSearchindex::model()->getTotal();

        $this->renderView();
    }

    /**
     * Index DB by ajax call
     */
    public function reindexStep()
    {
        $limit  = self::INDEX_STEP;
        $page   = (int)$this->app->jbrequest->get('page', 0);
        $offset = $limit * $page;

        $modelIndex = JBModelSearchindex::model();

        $lines = $modelIndex->reIndex($limit, $offset);
        $total = $modelIndex->getTotal();

        $current = $limit * ($page + 1);
        if ($current > $total) {
            $current = $total;
        }

        $progress = round($current * 100 / $total, 2);

        $this->app->jbajax->send(array(
            'progress' => $progress,
            'current'  => $current,
            'total'    => $total,
            'lines'    => $lines,
            'step'     => $page + 1,
            'stepsize' => $limit,
        ));
    }

    /**
     * Clean database from Zoo tools
     */
    public function cleandb()
    {
        if (!$this->_jbrequest->isPost()) {
            $this->renderView();
        } else {

            $this->item_count = $this->app->table->item->count();
            $this->steps      = (int)(11 + ($this->item_count / 10));

            $this->renderView('process');
        }
    }

    /**
     * Check files action
     */
    public function checkFiles()
    {
        if (!$this->_jbrequest->isAjax()) {
            $this->renderView();

        } else {

            $this->app->jbdoc->disableTmpl();

            try {
                $this->results = $this->app->jbcheckfiles->check();
                $this->renderView('result');
                jexit();

            } catch (JBCheckFilterException $e) {
                echo $e->getMessage();
            }

        }
    }

    /**
     * Check files action
     */
    public function removeUnversionFiles()
    {
        $files = $this->app->jbcheckfiles->check();

        $fileCount = 0;
        if (isset($files['unknown']) && !empty($files['unknown'])) {
            foreach ($files['unknown'] as $file) {
                $filepath = JPATH_ROOT . '/' . $file;
                if (JFile::exists($filepath) && JFile::delete($filepath)) {
                    $fileCount++;
                }
            }
        }

        jexit('<pre>Files deleted = ' . $fileCount . '</pre>');
    }

    /**
     * Check Zoo filesystem
     */
    public function checkFilesZoo()
    {
        if (!$this->_jbrequest->isAjax()) {
            $this->renderView();
        } else {

            $this->app->jbdoc->disableTmpl();

            try {
                $this->results = $this->app->modification->check();
                $this->renderView('result');
                jexit();

            } catch (AppModificationException $e) {
                echo $e->getMessage();
            }

        }
    }

    /**
     * Migrate page
     */
    public function migrate()
    {
        $this->renderView();
    }

    /**
     * Migrate steps page
     */
    public function migrateSteps()
    {
        $formData = $this->app->data->create($this->app->jbrequest->getAdminForm());
        $this->app->jbmigrate->prepare($formData);

        $this->renderView();
    }

    /**
     * Migrate steps page
     */
    public function migrateAjax()
    {
        /**
         * @var JBMigrateHelper      $migrate
         * @var JBMigrateOrderHelper $migrateorder
         * @var JBMigrateCartHelper  $migratecart
         * @var JBMigratePriceHelper $migrateprice
         */
        $migrate      = $this->app->jbmigrate;
        $migrateorder = $this->app->jbmigrateorder;
        $migratecart  = $this->app->jbmigratecart;
        $migrateprice = $this->app->jbmigrateprice;

        $isPost  = $this->app->jbrequest->isPost();
        $curStep = $this->app->jbrequest->get('page', 0) + 1;
        $params  = $migrate->getParams();

        $progress = $curStep / $params->find('steps.steps', 1) * 100;
        $progress = $progress > 100 ? 100 : round($progress, 1);

        if ($isPost) {

            if ($curStep == 1) {
                if ($params->get('cart_basic', 0)) {
                    $migratecart->basic();
                }

                if ($params->get('cart_form', 0)) {
                    $migratecart->formFields();
                }

                if ($params->get('cart_notificaction', 0)) {
                    $migratecart->notificaction();
                }

                if ($params->get('cart_minimalsum', 0)) {
                    $migratecart->minimalsum();
                }

                if ($params->get('cart_payments', 0)) {
                    $migratecart->payments();
                }

                $this->app->jbajax->send(array('nextStep' => '2', 'progress' => $progress));
            }

            if ($params->get('prices_enable') && $curStep == 2) {
                $prices       = $migrateprice->getPriceList($params->get('prices_types'));
                $priceConfigs = $migrateprice->extractPriceData($prices);

                $price = $migrateprice->createPrice($priceConfigs->get('price', array()));
                $priceConfigs->set('price', $price);

                $priceElements = $migrateprice->createPriceElements($priceConfigs->get('price', array()));
                $migrate->setParams('elements', $priceElements);

                $this->app->jbajax->send(array('nextStep' => '4', 'progress' => $progress));
            }

            if ($params->get('orders_enable') && $params->find('steps.orders_steps')) {
                if ($newStep = $migrateorder->convertItems($curStep)) {
                    $this->app->jbajax->send(array('nextStep' => $newStep, 'progress' => $progress));
                }
            }

            if ($params->get('prices_enable') && $params->find('steps.items_steps')) {
                if ($newStep = $migrateprice->convertItems($curStep)) {
                    $this->app->jbajax->send(array('nextStep' => $newStep, 'progress' => $progress));
                }
            }

            $this->app->jbajax->send(array('nextStep' => 'stop', 'progress' => 100));
        }

        $this->app->jbajax->send(array('nextStep' => 'stop', 'progress' => 100));
    }

}
