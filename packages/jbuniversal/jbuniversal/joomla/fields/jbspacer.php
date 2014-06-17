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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBSpacer
 */
class JFormFieldJBSpacer extends JFormField
{

    /**
     * @var string
     */
    protected $type = 'jbspacer';


    /**
     *
     */
    public function getInput()
    {
        $value = JText::_($this->element->attributes()->default);
        return '<strong style="width: 100%;float: left;color:#a00;font-size:1.1em"> - = ' . $value . ' = -</strong>';
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return null;
    }

}