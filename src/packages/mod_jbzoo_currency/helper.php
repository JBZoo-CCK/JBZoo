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

require_once JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/classes/jbmodulehelper.php'; // TODO move to bootstrap


/**
 * Class JBModuleHelperCurrency
 */
class JBModuleHelperCurrency extends JBModuleHelper
{
    /**
     * @type array
     */
    protected $_curList = [];

    /**
     * @param JRegistry $params
     * @param stdClass  $module
     */
    public function __construct(JRegistry $params, $module)
    {
        parent::__construct($params, $module);
        $this->_curList = $this->_getList();
    }

    /**
     * Load assets for switcher
     */
    protected function _loadAssets()
    {
        parent::_loadAssets();

        $this->_jbassets->js('modules:mod_jbzoo_currency/assets/js/switcher.js');
        $this->_jbassets->less('modules:mod_jbzoo_currency/assets/less/rates.less');
    }

    /**
     * Init switcher
     */
    protected function _initWidget()
    {
        $this->app->jbassets->widget('.jsCurrencyModuleSwitcher', 'JBZoo.CurrencyModuleSwitcher', [
            'target' => $this->_params->get('switcher_target', '.jbzoo'),
        ]);
    }

    /**
     * @return mixed
     */
    public function renderButtons()
    {
        $curList = $this->_params->get('currency_list', []);

        return $this->app->jbhtml->currencyToggle(
            $this->_defaultCur(true),
            $this->_curList,
            [
                'target'      => $this->_params->get('switcher_target', '.jbzoo'),
                'showDefault' => isset($curList[JBCartValue::DEFAULT_CODE]),
                'setOnInit'   => 1,
                'isMain'      => 1,
            ]
        );
    }

    /**
     * @return string
     */
    public function renderSelect()
    {
        $list = $this->_getOptionList();
        $current = $this->_defaultCur(true);

        $html = [
            '<div class="jsCurrencyModuleSwitcher jbcurrency-list-select">',
            $this->app->jbhtml->select($list, 'cur-' . $this->_module->id, '', $current),
            '</div>'
        ];

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderRadio()
    {
        $list = $this->_getOptionList();
        $current = $this->_defaultCur(true);

        $html = [
            '<div class="jsCurrencyModuleSwitcher jbcurrency-list-radio">',
            $this->app->jbhtml->radio($list, 'cur-' . $this->_module->id, '', $current),
            '</div>'
        ];

        return implode(PHP_EOL, $html);
    }

    /**
     * @return array
     */
    protected function _getOptionList()
    {
        $options = [];

        if (!empty($this->_curList)) {
            foreach ($this->_curList as $code => $currency) {
                $options[$code] = $currency['name'];
            }
        }

        return $options;
    }

    /**
     * @param bool $addDefault
     * @return array
     */
    public function getCurrencyList($addDefault = false)
    {
        $result = [
            'orig' => null,
            'list' => [],
        ];

        if (!empty($this->_curList)) {

            $defaultCur = $this->_params->get('currency_default', 'eur');
            $multiply = $this->app->jbvars->number($this->_params->get('list_multiply', 1));
            $moneyVal = JBCart::val(1, $defaultCur)->multiply($multiply);

            foreach ($this->_curList as $code => $currency) {

                if ($moneyVal->isCur($code)) {
                    continue;
                }

                if (!($addDefault && $code == JBCartValue::DEFAULT_CODE)) {
                    //continue;
                }

                $result['list'][$code] = [
                    'from' => $moneyVal->html(),
                    'to'   => $moneyVal->html($code),
                    'name' => $currency['name'],
                ];

            }

            $result['orig'] = $moneyVal;
        }

        return $result;

    }

    /**
     * @return array
     */
    protected function _getList()
    {
        $curList = (array)$this->_params->get('currency_list', []);
        $list = $this->app->jbmoney->getData();

        $result = [];
        foreach ($curList as $code) {
            $result[$code] = $list->get($code);
        }

        $result = $this->app->data->create($result);

        return $result;
    }

    /**
     * @param bool $availableDefault
     * @return null|string
     */
    protected function _defaultCur($availableDefault = false)
    {
        $defaultCur = $this->_params->get('currency_default', 'eur');
        $currentCur = $this->app->jbrequest->getCurrency($defaultCur);

        if (!$this->_curList->get($currentCur)) {
            $currentCur = null;

            if ($availableDefault) {
                $currentCur = JBCartValue::DEFAULT_CODE;
            }

        }

        return $currentCur;
    }


}
