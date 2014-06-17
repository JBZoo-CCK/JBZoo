<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCSVCategoryTmpl_item
 */
class JBCSVCategoryTmpl_item extends JBCSVCategory
{
    /**
     * @return string
     */
    public function toCSV()
    {
        $settings = $this->app->jbuser->getParam('export-categories', array());

        if ($settings->category_item_settings) {

            $arrayParams = array();
            $params      = $this->_category->getParams()->get('template.');

            foreach ($params as $key => $value) {

                if (preg_match('/^item*/', $key) && $key !== 'lastmodified') {
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

        $this->_category->getParams()->set('template.', $params);
    }
}
