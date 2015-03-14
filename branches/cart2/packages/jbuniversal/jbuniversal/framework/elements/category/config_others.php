<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */
// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBCSVCategoryConfig_others
 */
class JBCSVCategoryConfig_others extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $settings = JBModelConfig::model()->getGroup('export.categories');

        if ($settings->config_others_settings) {

            $arrayParams    = array();
            $paramsConfig   = $this->_category->getParams()->get('config.');
            $paramsTemplate = $this->_category->getParams()->get('template.');
            $params         = array_merge($paramsConfig, $paramsTemplate);
            $others         = array('alpha_index', 'alpha_chars', 'show_alpha_index');

            foreach ($params as $key => $value) {

                if (in_array($key, $others)) {
                    $arrayParams[$key] = $value;
                }
            }

            $result = $this->_packToLine($arrayParams);

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

        foreach ($params as $key => $value) {
            if ($key == 'show_alpha_index') {
                $paramsTemplate[$key] = $value;
                $this->_category->getParams()->set('template.', $paramsTemplate);
            } else {
                $paramsConfig[$key] = $value;
                $this->_category->getParams()->set('config.', $paramsConfig);
            }
        }

    }
}