<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JFormFieldJBTemplates
 */
class JFormFieldJBTemplates extends JFormField
{

    protected $type = 'jbtemplates';

    /**
     * @return null|string
     */
    public function getInput()
    {
        $app         = App::getInstance('zoo');
        $application = $app->zoo->getApplication();
        $templates   = $application->getTemplates();

        $options = array();

        foreach ($templates as $key => $template) {
            $options[$key] = $template->getMetaData()->name;
        }

        if (!$this->value) {
            $this->value = 'catalog';
        }

        return $app->jbhtml->select($options, $this->getName($this->fieldname), array(), $this->value);
    }
}