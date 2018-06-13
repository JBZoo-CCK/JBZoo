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
 * Class JBCSVCategoryConfig_items_order
 */
class JBCSVCategoryConfig_items_order extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $settings = JBModelConfig::model()->getGroup('export.categories');

        if ($settings->config_items_order_settings) {
            $result      = '';
            $arrayParams = array();
            $params      = $this->_category->getParams()->get('config.');

            foreach ($params as $key => $value) {

                if ($key == 'item_order') {
                    $arrayParams = $value;
                }
            }

            $result = $this->_packToLine($arrayParams, true);

            return $result;
        } else {
            return parent::toCSV();
        }
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function fromCSV($value)
    {
        $params = array();
        if (!empty($value)) {
            $params = $this->_unpackFromLine($value);
        }
        $this->_category->getParams()->set('config.item_order', $params);
    }
}