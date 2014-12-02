<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/*
   Class: ElementDate
   The date element class
*/
class JBCartElementOrderDate extends JBCartElementOrder {

    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';


    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function edit($params = array()) {

        if ($value = $this->get('value', '')) {
            try {
                $value = $this->app->html->_(
                    'date',
                    $value,
                    $this->app->date->format(self::EDIT_DATE_FORMAT),
                    $this->app->date->getOffset()
                );
            } catch (Exception $e) {}

            return $value;
        }

        return ' - ';
    }


    /*
       Function: _edit
           Renders the repeatable edit form field.

       Returns:
           String - html
    */

    public function renderSubmission($params = array()) {
        $name = $this->getControlName('value');

        return $this->app->html->_('zoo.calendar', '', $name, $name, array('class' => 'calendar-element'), true);
    }


    /*
        Function: _validateSubmission
            Validates the submitted element

       Parameters:
            $value  - AppData value
            $params - AppData submission parameters

        Returns:
            Array - cleaned value
    */
    public function validateSubmission($value, $params) {

        $value = $value->get('value');
        if (!empty($value) && ($time = strtotime($value))) {
            $value = strftime(self::EDIT_DATE_FORMAT, $time);
        }

        return array('value' => $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
                ->addOption('date_format', self::EDIT_DATE_FORMAT)
                ->clean($value));
    }

    /*
        Function: bindData
            Set data through data array.

        Parameters:
            $data - array

        Returns:
            Void
    */
    public function bindData($data = array()) {
        parent::bindData($data);

        $value = $this->get('value', '');
        if (!empty($value) && ($value = strtotime($value)) && ($value = strftime(self::EDIT_DATE_FORMAT, $value))) {
            $tzoffset = $this->app->date->getOffset();
            $date     = $this->app->date->create($value, $tzoffset);
            $value      = $date->toSQL();
            $this->set('value', $value);
        }
    }

}