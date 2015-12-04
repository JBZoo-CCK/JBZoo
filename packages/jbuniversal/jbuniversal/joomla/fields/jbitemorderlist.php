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
 * Class JFormFieldJBItemOrder
 */
class JFormFieldJBItemOrderList extends JFormField
{

    protected $type = 'jbitemorderlist';

    /**
     * Get input HTML
     * @return string
     */
    public function getInput()
    {
        $app  = App::getInstance('zoo');
        $list = $app->jborder->getListAdv(true);

        unset($list[JText::_('JBZOO_FIELDS_CORE')]['_none']);

        return JHtml::_('select.groupedlist', $list, $this->getName($this->fieldname) . '[]', array(
            'list.attr'   => $app->jbhtml->buildAttrs(array(
                'multiple' => 'multiple',
                'size'     => 10,
            )),
            'list.select' => $this->value,
            'group.items' => null,
        ));
    }
}
