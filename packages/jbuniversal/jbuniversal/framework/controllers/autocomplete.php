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
 * Class AutocompleteJBUniversalController
 */
class AutocompleteJBUniversalController extends JBUniversalController
{
    const MAX_LENGTH = 20;

    /**
     * @throws AppException
     */
    public function index()
    {
        $this->app->jbdebug->mark('autocomplete::start');
        if (!$this->app->jbcache->start(null, 'autocomplete')) {

            $type    = $this->_jbrequest->get('type');
            $query   = $this->_jbrequest->get('value');
            $appId   = $this->_jbrequest->get('app_id');
            $element = $this->_jbrequest->get('name');

            if ($element && preg_match('/(?:\\[)([^]]*)(?:[]])(?(?<=\\[)|\\[?([^]]*))/i', $element, $element_id)) {

                $param_id   = isset($element_id[2]) ? $element_id[2] : null;
                $element_id = $element_id[1];

                $element     = $this->app->jbentity->getElement($element_id, $type, $appId);
                $elementType = $element->getElementType();

                $db = JBModelAutocomplete::model();
                if ($element_id == '_itemname') {
                    $rows = $db->name($query, $type, $appId);

                } elseif ($element_id == '_itemtag') {
                    $rows = $db->tag($query, $type, $appId);

                } elseif ($element_id == '_itemauthor') {
                    $rows = $db->author($query, $type, $appId);

                } else if ($element instanceof ElementJBPrice) {
                    $param_id = JBModelSku::model()->getId($param_id);
                    $rows     = $db->priceElement($query, $element_id, $param_id, $type, $appId);

                } else if ($elementType == 'textarea') {
                    $rows = $db->textarea($query, $element_id, $type, $appId);

                } else if ($elementType == 'jbcomments') {
                    $rows = $db->comments($query, $type, $appId);

                } else {
                    $rows = $db->field($query, $element_id, $type, $appId);
                }

                $data = array();
                if (!empty($rows)) {

                    foreach ($rows as $row) {

                        if (JString::strlen($row->value) > self::MAX_LENGTH) {
                            $value = $this->app->jbstring->smartSubstr($row->value, $query);
                        } else {
                            $value = $row->value;
                        }
                        $label  = isset($row->label) ? $row->label : $row->value;
                        $id     = isset($row->id) ? $row->id : $row->value;

                        $data[] = array(
                            'id'    => JString::str_ireplace("\n", " ", $id),
                            'label' => JString::str_ireplace("\n", " ", $label),
                            'value' => JString::str_ireplace("\n", " ", JString::trim($value, '.')),
                        );
                    }
                }

                echo json_encode($data);

            } else {
                throw new AppException('Unkown element name');
            }

            $this->app->jbcache->stop();
        }

        $this->app->jbdebug->mark('autocomplete::end');
        jexit();
    }

}
