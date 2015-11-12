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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBKeyvalue
 */
class JFormFieldJBKeyvalue extends JFormField
{

    /**
     * @var string
     */
    protected $type = 'jbkeyvalue';


    /**
     * @return string
     */
    public function getInput()
    {
        JBModelSku::model();
        $elements     = array();
        $app          = App::getInstance('zoo');
        $application  = $app->zoo->getApplication();
        $i            = 0;
        $elementsList = $app->jbentity->getItemTypesData(1);
        $typesList    = $app->jbtype->getSimpleList();
        $exclude      = JBModelSearchindex::model()->getExcludeTypes();
        $textHeadType = JText::_('JBZOO_FIELDS_CORE');
        $stdFields    = array(
            ''                  => JText::_('JBZOO_MODITEM_SELECT_OPTION'),
            '_itemauthor'       => 'Item Author',
            '_itemcategory'     => 'Item Category',
            '_itemcreated'      => 'Item Created',
            '_itemfrontpage'    => 'Item Frontpage',
            '_itemmodified'     => 'Item Modified',
            '_itemname'         => 'Item Name',
            '_itempublish_up'   => 'Item Publish Up',
            '_itempublish_down' => 'Item Publish Down',
            '_itemtag'          => 'Item Tag'
        );

        $elements[$textHeadType] = array('items' => $stdFields);

        foreach ($exclude as $key => $value) {
            if ($value == 'textarea') {
                unset ($exclude[$key]);
            }
        }

        foreach ($elementsList as $type => $tmpElements) {
            if (array_key_exists($type, $typesList)) {

                $appType = $application->getType($type);
                if (!$appType) {
                    continue;
                }

                foreach ($tmpElements as $key => $element) {

                    if (!in_array($element['type'], $exclude) && strpos($key, '_') === false) {

                        $instance = $appType->getElement($key);

                        if ($instance instanceof ElementJBPrice) {
                            $name     = $instance->config->get('name');
                            $newEls[] = array(
                                'value'   => $key,
                                'text'    => '- ' . $name,
                                'disable' => true
                            );

                            if ($params = $instance->getElements()) {
                                foreach ($params as $id => $param) {
                                    if ($param->hasFilterValue()) {
                                        $newEls[] = array(
                                            'value' => $key . '__' . $id,
                                            'text'  => '-- ' . $name . ' - ' . $param->getName()
                                        );
                                    }
                                }
                            }
                        } else {
                            $newEls[$element['name']] = array(
                                'value' => $key,
                                'text'  => $element['name'],
                            );
                        }
                    }
                }
            }
            if (!empty($newEls)) {
                $elements[$typesList[$type]] = array('items' => $newEls);
            }
            unset($newEls);
        }

        if (empty($this->value)) {
            $this->value = array(array('key' => '', 'value' => ''));
        }

        foreach ($this->value as $value) {
            $value = (array)$value;
            if ($i != 0 && (!isset($value['key']) || empty($value['key']))) {
                continue;
            }

            $html[] = '<div class="jbjkeyvalue-row">';
            $html[] = JHtml::_('select.groupedlist', $elements, $this->getName($this->fieldname) . '[' . $i . '][key]', array(
                'list.select' => isset($value['key']) ? $value['key'] : '',
            ));

            $html[] = '<input ' . $app->jbhtml->buildAttrs(array(
                    'placeholder' => JText::_('JBZOO_JBKEYVALUE_VALUE'),
                    'type'        => 'text',
                    'name'        => $this->getName($this->fieldname) . '[' . $i . '][value]',
                    'value'       => isset($value['value']) ? $value['value'] : '',
                    'class'       => isset($class) ? $class : ''
                )) . ' />';

            if ($i !== 0) {
                $html[] = '<a href="#jbjkeyvalue-rem" class="jsJKeyValueRemove" title="' . JText::_('JBZOO_JBKEYVALUE_ADD') . '"></a>';
            }

            $html[] = JBZOO_CLR;
            $html[] = '</div>';

            $i++;
        }

        $html[] = JBZOO_CLR;
        $html[] = '<a href="#jbjkeyvalue-add" class="jsJKeyValueAdd">' . JText::_('JBZOO_JBKEYVALUE_ADD') . '</a>';
        $html[] = JBZOO_CLR;

        return '<div class="jsJKeyValue">' . implode(PHP_EOL, $html) . '</div>' . JBZOO_CLR;
    }
}