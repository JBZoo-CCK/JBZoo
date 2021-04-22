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

require_once(JPATH_BASE . '/modules/mod_jbzoo_item/types/rules.php');

/**
 * Class JBZooModItemCategory
 */
class JBZooModItemConditions extends JBZooItemType
{
    /**
     * @var array
     */
    protected $_elements = array();

    /**
     * @type int
     */
    protected $_app_id = 0;

    /**
     * @type string
     */
    protected $_type = null;

    /**
     * Init vars
     */
    public function init()
    {
        $this->_elements = $this->app->jbentity->getItemTypesData(false);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $this->init();
        $searchElements = array();
        $this->_app_id  = $this->_params->get('condition_app', '0');
        $this->_type    = $this->_params->get('condition_type', 'product');
        $conditions     = (array)$this->_params->get('conditions', array());
        $logic          = $this->_params->get('logic', 'AND');
        $order          = (array)$this->_params->get('order_default');
        $exact          = $this->_params->get('type_search', 0);
        $limit          = $this->_params->get('pages', 20);
        $elements       = $this->app->jbconditions->getValue($conditions);

        if (!empty($elements)) {
            foreach ($elements as $fieldKey => $value) {

                if (empty($value)) {
                    continue;
                }

                if (strpos($fieldKey, '_') === false) {

                    $table      = $this->app->jbtables;
                    $tableIndex = $table->getIndexTable($this->_type);
                    $fields     = $table->getFields($tableIndex);
                    $myFiled    = $table->getFieldName($fieldKey);
                    $elements   = $this->_elements;
                    $element    = $elements[$fieldKey];
                    unset($elements);

                    if (in_array($myFiled, $fields) || $element['type'] == 'textarea') {
                        $searchElements[$fieldKey] = $value;
                    }

                } else {

                    $searchElements[$fieldKey] = $value;
                }
            }
        }

        $items = JBModelFilter::model()->search(
            $searchElements, strtoupper($logic), $this->_type, $this->_app_id,
            $exact, 0, $limit, $order);

        return $items;
    }
}