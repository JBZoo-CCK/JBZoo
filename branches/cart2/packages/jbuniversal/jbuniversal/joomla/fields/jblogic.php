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
 * Class JFormFieldJBLogic
 */
class JFormFieldJBLogic extends JFormField
{

    protected $type = 'jblogic';

    /**
     * @return string
     */
    public function getInput()
    {
        $app = App::getInstance('zoo');

        $options = array(
            JHtml::_('select.option', 'and', JText::_('JBZOO_AND')),
            JHtml::_('select.option', 'or', JText::_('JBZOO_OR'))
        );

        $html   = array();
        $html[] = '<div class="jbzoo-complex-field">';
        $html[] = '<fieldset class="radio jblogic">';
        $html[] = $app->jbhtml->radio($options, $this->getName($this->fieldname), '', $this->value, false, false, false);
        $html[] = '</fieldset>';
        $html[] = '</div>';

        return implode("\n ", $html);
    }
}