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
 * Class JBToolsJBuniversalController
 * JBZoo tools controller for back-end
 */
class JBToolsJBuniversalController extends JBUniversalController
{

    const INDEX_STEP = 200;

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

}
