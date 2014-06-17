<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBSpacer
 */
class JFormFieldJBDelimiter extends JFormField
{
    /**
     * @var string
     */
    protected $type = 'jbdelimiter';

    /**
     * Class constructor
     * @param null|JForm $form
     */
    public function __construct($form = null)
    {
        parent::__construct($form);

        $this->app     = App::getInstance('zoo');
        $this->uniq    = $this->app->jbstring->getId('delimiter-');
    }

    /**
     * Renders field HTML
     * @return string
     */
    public function getInput()
    {
        $this->app->jbassets->initJBDelimiter('#' . $this->uniq);
        $group = $this->element->attributes()->group;

        return '<div id="' . $this->uniq . '" data-group="' . $group . '">
                ' . $this->_getInput() . '
                </div>';
    }

    /**
     * Remove label from field
     * @return null
     */
    public function getLabel()
    {
        return null;
    }

    /**
     * Check if not empty default value
     * @return bool|string
     */
    protected function _getInput()
    {
        $value = JText::_($this->element->attributes()->default);
        if(!empty($value)) {
            return '<strong class="jbdelimiter"> - = ' . $value . ' = -</strong>';
        }

        return false;
    }

}