<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
