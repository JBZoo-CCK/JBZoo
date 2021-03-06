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
 * Class JFormFieldJBStatus
 */
class JFormFieldJBStatus extends JFormField
{

    protected $type = 'jbstatus';

    /**
     * @return string
     */
    public function getInput()
    {
        // get app
        $app = App::getInstance('zoo');

        $type = strtoupper($this->element->attributes()->status_type);

        if (!$type || !defined('JBCart::' . $type)) {
            return 'attr "status_type" is undefined';
        }

        $elements = $app->jbcartstatus->getList(constant('JBCart::' . $type));

        // create select
        $options = array(
            '' => JText::_('JBZOO_NONE'),
        );

        foreach ($elements as $key => $element) {
            $options[] = $app->html->_('select.option', $element->getCode(), JText::_($element->getName()));
        }

        return $app->html->_(
            'select.genericlist',
            $options,
            $this->getName($this->fieldname),
            '',
            'value',
            'text',
            $this->value
        );
    }

}
