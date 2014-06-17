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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBAppList
 */
class JFormFieldJBAppList extends JFormField
{

    protected $type = 'jbapplist';

    public function getInput()
    {
        // get app
        $app  = App::getInstance('zoo');
        $attr = array();

        $applications = $app->table->application->all();

        // create select
        $options = array(0 => JText::_('JBZOO_FIELDS_SELECT'));

        foreach ($applications as $application) {
            if ($application->application_group == JBZOO_APP_GROUP) {
                $options[$application->id] = $application->name;
            }
        }

        if ((int)$this->element->attributes()->headhide) {
            unset($options[0]);
        }

        if ((int)$this->element->attributes()->multiple) {
            $attr['multiple'] = 'multiple';
        }

        if ((int)$this->element->attributes()->required) {
            $attr['required'] = 'required';
        }

        return $app->jbhtml->select($options, $this->getName($this->fieldname), $attr, $this->value);
    }

}
