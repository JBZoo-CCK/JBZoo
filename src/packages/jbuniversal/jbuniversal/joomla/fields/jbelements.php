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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBSpacer
 */

class JFormFieldJBElements extends JFormField {
     
     /**
     * @var string
     */
    protected $type = 'jbelements';
     
     /**
     * Renders field HTML
     * @return string
     */
    public function getInput() {

        // get app
        $zoo  = App::getInstance('zoo');
        $attribs = '';
        $options = $html = array();
        $control_name = $this->getName($this->fieldname);

        foreach ($zoo->application->getApplications() as $application) {
            $types = $application->getTypes();

            // add core elements
            $core = $zoo->object->create('Type', array('_core', $application));
            $core->name = JText::_('Core');
            array_unshift($types, $core);

            $options = array();

            foreach ($types as $type) {

                if ($type->identifier == '_core') {
                    $elements = $type->getCoreElements();
                } else {
                    $elements = $type->getElements();
                }

                // filter orderable elements plus category and tags
                $elements = array_filter($elements, function($element) {return $element->getMetaData('group') != 'Core' && $element->getMetaData("orderable") == "true";});

                $value = false;
                foreach ($elements as $element) {
                    $options[$type->name][] = $zoo->html->_('select.option', $element->identifier, ($element->config->name ? $element->config->name : $element->getMetaData('name')));
                }

                $id = $control_name.$type->identifier;

            }

            // break after first application
            break;
        }

        return JHtml::_('select.groupedlist',  $options, $this->name, array(
                'list.select' => $this->value,
                'group.items' => null,
            ));
    }
}