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


require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
require_once(JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');

/**
 * Class JBZooCurrencyModuleHelper
 */
class JBZooCurrencyModuleHelper
{
    const LIST_DIRECT  = "direct";
    const LIST_REVERSE = "reverse";

    /**
     * @var App
     */
    public $app = null;

    /**
     * @var JRegistry
     */
    protected $_params = null;

    /**
     * @var Object
     */
    protected $_module = null;

    /**
     * @type array
     */
    protected $_curList = array();

    /**
     * Init Zoo
     * @param JRegistry $params
     * @param object    $module
     */
    public function __construct($params, $module)
    {
        $this->app     = App::getInstance('zoo');
        $this->_params = $params;
        $this->_module = $module;

        JBZoo::init();

        $this->_curList = $this->_getList($this->app->jbmoney->getData());
    }

    /**
     * @return mixed
     */
    public function renderToggleSwitcher()
    {
        return $this->app->jbhtml->currencyToggle(
            $this->_curList,
            $this->_defaultCur(true),
            array(
                'target'      => $this->_params->get('switcher_target', '.jbzoo'),
                'showDefault' => (int)$this->_params->get('currency_defaultcode', 1),
                'setOnInit'   => (int)$this->_params->get('set_on_init', 1),
                'rates'       => $this->app->jbmoney->getData(),
            )
        );
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        $result = array();
        if (!empty($this->_curList)) {

            $defaultCur = $this->_defaultCur(false);
            $multiply   = $this->app->jbvars->number($this->_params->get('list_multiply', 1));
            $moneyVal   = JBCart::val(1, $defaultCur, $this->_curList)->multiply($multiply);

            $direction = $this->_params->get('list_direction', self::LIST_DIRECT);

            foreach ($this->_curList as $code => $currency) {

                if ($moneyVal->isCur($code) || $code == JBCartValue::DEFAULT_CODE) {
                    continue;
                }

                if ($direction == self::LIST_DIRECT) {
                    $result[$code] = array(
                        'from' => $moneyVal->html(),
                        'to'   => $moneyVal->html($code),
                        'name' => $currency['name'],
                    );

                } else if ($direction == self::LIST_REVERSE) {
                    $result[$code] = array(
                        'from' => $moneyVal->html($code),
                        'to'   => $moneyVal->html(),
                        'name' => $currency['name'],
                    );
                }

            }
        }

        return $result;

    }

    /**
     * @param $list
     * @return array
     */
    protected function _getList($list)
    {
        $curList = $this->_params->get('currency_list', array());

        $result = array();
        foreach ($curList as $code) {
            $result[$code] = $list->get($code);
        }

        $result = $this->app->data->create($result);

        return $result;
    }

    /**
     * @return string
     */
    protected function _defaultCur($availableDefault = false)
    {
        $currentCur = $this->_params->get('currency_default', 'eur');

        if (!$this->_curList->get($currentCur)) {
            $currentCur = null;
            if ($availableDefault) {
                $currentCur = JBCartValue::DEFAULT_CODE;
            }
        }

        return $currentCur;
    }

}

