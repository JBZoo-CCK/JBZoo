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

            if ($element && preg_match('#^e\[(.*?)\]\[(.*?)\]#i', $element, $elementName)) {

                $paramID     = isset($elementName[2]) ? str_replace('_', '', $elementName[2]) : null;
                $elementName = $elementName[1];

                $autocomleteDb = JBModelAutocomplete::model();

                $element     = $this->app->jbentity->getElement($elementName, $type, $appId);
                $elementType = $element->getElementType();

                if ($elementName == '_itemname') {
                    $rows = $autocomleteDb->name($query, $type, $appId);

                } elseif ($elementName == '_itemtag') {
                    $rows = $autocomleteDb->tag($query, $type, $appId);

                } elseif ($elementName == '_itemauthor') {
                    $rows = $autocomleteDb->author($query, $type, $appId);

                } else if ($elementType == 'jbpriceadvance') {
                    $rows = $autocomleteDb->sku($query, $elementName, $paramID, $type, $appId);

                } else if ($elementType == 'textarea') {
                    $rows = $autocomleteDb->textarea($query, $elementName, $type, $appId);

                } else if ($elementType == 'jbcomments') {
                    $rows = $autocomleteDb->comments($query, $type, $appId);

                } else {
                    $rows = $autocomleteDb->field($query, $elementName, $type, $appId);

                }

                $data = array();
                if (!empty($rows)) {

                    foreach ($rows as $row) {

                        if (JString::strlen($row->value) > self::MAX_LENGTH) {
                            $value = $this->app->jbstring->smartSubstr($row->value, $query);
                        } else {
                            $value = $row->value;
                        }

                        $data[] = array(
                            'id'    => JString::str_ireplace("\n", " ", $value),
                            'label' => JString::str_ireplace("\n", " ", $value),
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
