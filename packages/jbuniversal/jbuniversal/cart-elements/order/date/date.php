<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBCartElementOrderDate
 */
class JBCartElementOrderDate extends JBCartElementOrder
{

    const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function edit($params = array())
    {
        if ($value = $this->get('value', '')) {
            try {
                $value = $this->app->html->_(
                    'date',
                    $value,
                    $this->app->date->format(self::EDIT_DATE_FORMAT),
                    $this->app->date->getOffset()
                );
            } catch (Exception $e) {
            }

            return $value;
        }

        return ' - ';
    }

    /**
     * Renders the repeatable edit form field
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $default = $this->getUserState($params->get('user_field'));
        $name  = $this->getControlName('value');
        $attrs = array('class' => 'calendar-element');
        $id    = $this->htmlId();
        $value = $this->get('value', $default);

        $this->app->jbassets->calendar();
        return $this->app->html->_('zoo.calendar', $value, $name, $id, $attrs, true);
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $value = $value->get('value');

        if (!empty($value) && ($time = strtotime($value))) {
            $value = strftime(self::EDIT_DATE_FORMAT, $time);
        }

        return array('value' => $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
            ->addOption('date_format', self::EDIT_DATE_FORMAT)
            ->clean($value));
    }

    /**
     * Set data through data array
     * @param array $data
     * @return $this|void
     */
    public function bindData($data = array())
    {
        parent::bindData($data);

        $value = $this->get('value', '');
        if (!empty($value) && ($value = strtotime($value)) && ($value = strftime(self::EDIT_DATE_FORMAT, $value))) {
            $tzoffset = $this->app->date->getOffset();
            $date     = $this->app->date->create($value, $tzoffset);
            $value    = $date->toSQL();
            $this->set('value', $value);
        }
    }

}
