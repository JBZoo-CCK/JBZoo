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

// include jbzoo init class
require_once __DIR__ . '/framework/jbzoo.php';

/**
 * Class JBUniversalApplication
 * JBZoo Application class
 */
final class JBUniversalApplication extends Application
{
    const JBZOO_VERSION = '4.0.2';

    /**
     * @var bool
     */
    protected $isSite;

    /**
     * @var JBRequestHelper
     */
    protected $jbrequest;

    /**
     * @var JBDebugHelper
     */
    protected $jbdebug;

    /**
     * Register controller path, only for frontend
     */
    private function initAssets()
    {
        if ($this->isSite) {
            $this->app->jbassets->setAppCSS($this->alias);
            $this->app->jbassets->setAppJS($this->alias);
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        JBZoo::init();
        $this->app = App::getInstance('zoo');

        $this->jbrequest = $this->app->jbrequest;

        $this->jbdebug = $this->app->jbdebug;

        $this->jbdebug->mark('application::init::start');
        $this->isSite = $this->app->jbenv->isSite();
    }

    /**
     * Dispatch
     */
    public function dispatch()
    {
        define('JBZOO_DISPATCHED', true);

        // check is event plugin enabled
        if ($this->isSite && !JPluginHelper::isEnabled('system', 'jbzoo')) {
            $pluginUrl = JUri::root() . 'administrator/index.php?option=com_plugins&filter_search=jbzoo';
            $this->app->jbnotify->notice(
                "\"System - JBZoo\" plugin is not install or not enabled, <a href=\"{$pluginUrl}\">find it</a>"
            );
        }

        $this->jbdebug->mark('application::dispatch::before');

        $this->initAssets();

        // dispatcher hack
        $ctrlRequest = $this->jbrequest->getCtrl();

        // try to get current controller
        if ($this->isSite) {
            $newCtrlPath = $this->app->path->path('jbzoo:/controllers/' . $ctrlRequest . '.php');

        } else {
            $this->app->jbtoolbar->toolbar();
            $newCtrlPath = $this->app->path->path('jbzoo:/controllers/admin.' . $ctrlRequest . '.php');

            // hack for index page in Joomla CP
            $task = $this->jbrequest->getWord('task');
            if ((float)strpos($ctrlRequest, 'jb') === (float)0 && empty($task)) {
                $this->app->jbrequest->set('task', 'index');
            }
        }

        // check is override controller exists
        if ($newCtrlPath) {
            $newCtrlPath = JPath::clean($newCtrlPath);
            require_once $newCtrlPath;

            $newCtrl = $ctrlRequest . $this->getGroup();

            $this->jbrequest->set('controller', $newCtrl);
            $this->app->dispatch($newCtrl);

        } else {
            parent::dispatch();
        }

        $this->jbdebug->mark('application::dispatch::after');
    }

    /**
     * Init form elements
     * @return null
     */
    final public function getParamsForm()
    {
        // get parameter xml file
        if ($xml = $this->app->path->path($this->getResource() . $this->metaxml_file)) {

            // get form
            $form = $this->app->parameterform->create($xml);

            // add own joomla elements
            $form->addElementPath($this->app->path->path('applications:' . $this->getGroup() . '/joomla/elements'));

            return $form;
        }

        return null;
    }

    /**
     * Check has application icon
     * @return bool
     */
    final public function hasAppIcon()
    {
        return (bool)$this->app->path->path($this->getResource() . 'assets/app_icons/' . $this->alias . '.png');
    }

    /**
     * Get application icon
     * @return string
     */
    final public function getIcon()
    {
        if ($this->hasAppIcon()) {
            return $this->app->path->url($this->getResource() . 'assets/app_icons/' . $this->alias . '.png');
        } else {
            if ($this->hasIcon()) {
                return $this->app->path->url($this->getResource() . 'application.png');
            } else {
                return $this->app->path->url('assets:images/zoo.png');
            }
        }
    }

    /**
     * Get JBZoo application group
     * @return string
     */
    final public function getGroup()
    {
        return JBZOO_APP_GROUP;
    }

    /**
     * Gat hash by var
     * @param $string
     * @return string
     */
    final public function getHash($string)
    {
        $as254234 = 'JBZoo';
        $y09783 = 'getHash';
        return call_user_func([$as254234, $y09783], $string);
    }

    /**
     * @param $order
     * @param $prevResult
     * @return array
     */
    public function setItemOrder($order, $prevResult)
    {
        $jbtables = $this->app->jbtables;
        $jborder = $this->app->jborder;

        $orders = $jborder->convert($order);

        $joinList = [];
        $ol = [];
        $columns = [];

        $ran = $this->app->jbarray->recursiveSearch('random', $orders);
        if ($ran !== false) {
            return ['', ' RAND() '];
        }

        foreach ($orders as $orderParams) {

            $order = $jborder->getOrderDirection($orderParams['order']);

            if ($orderParams['field'] === 'corename') {
                $ol[] = 'a.name ' . $order;

            } elseif ($orderParams['field'] === 'corealias') {
                $ol[] = 'a.alias ' . $order;

            } elseif ($orderParams['field'] === 'corecreated') {
                $ol[] = 'a.created ' . $order;

            } elseif ($orderParams['field'] === 'corehits') {
                $ol[] = 'a.hits ' . $order;

            } elseif ($orderParams['field'] === 'coremodified') {
                $ol[] = 'a.modified ' . $order;

            } elseif ($orderParams['field'] === 'corepublish_down') {
                $ol[] = 'a.publish_down ' . $order;

            } elseif ($orderParams['field'] === 'corepublish_up') {
                $ol[] = 'a.publish_up ' . $order;

            } elseif ($orderParams['field'] === 'coreauthor') {
                $ol[] = 'tJoomlaUsers.name ' . $order;
                $joinList['tJoomlaUsers'] = 'LEFT JOIN #__users AS tJoomlaUsers ON a.created_by = tJoomlaUsers.id';

            } elseif (strpos($orderParams['field'], '__')) {
                list ($elementId, $priceField) = explode('__', $orderParams['field']);

                if (in_array($priceField, ['sku', 'total', 'price'], true)) {

                    $ol[] = 'tItemSku.' . $priceField . ' ' . $order;
                    $joinList['tJoomlaUsers'] = 'LEFT JOIN ' . ZOO_TABLE_JBZOO_SKU . ' AS tItemSku ON a.id = tItemSku.item_id';
                }

            } else {
                $itemType = $this->app->jbentity->getItemTypeByElementId($orderParams['field']);

                if (!empty($itemType)) {

                    $tableName = $jbtables->getIndexTable($itemType);
                    $tableSqlName = 'tIndex' . str_replace('#__', '', $tableName);
                    $columns[$itemType] = $jbtables->getFields($tableName);

                    $elementId = $this->app->jbtables->getFieldName($orderParams['field'], $orderParams['mode']);
                    if (in_array($elementId, $columns[$itemType], true)) {
                        $joinList[$itemType] = 'LEFT JOIN ' . $tableName
                            . ' AS ' . $tableSqlName . ' ON a.id = ' . $tableSqlName . '.item_id'
                            . ' AND ' . $tableSqlName . '.' . $elementId . ' IS NOT NULL';

                        $ol[] = $tableSqlName . '.' . $elementId . ' ' . $order;
                    }
                }
            }
        }

        if (!empty($ol)) {
            return [' ' . implode(' ', $joinList) . ' ', implode(', ', $ol)];
        }

        if (!empty($prevResult)) {
            return $prevResult;
        }

        return ['', ' a.id ASC '];
    }
}
