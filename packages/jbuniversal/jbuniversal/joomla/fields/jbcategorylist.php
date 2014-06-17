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
 * Class JFormFieldJBCategoryList
 */
class JFormFieldJBCategoryList extends JFormField
{

    protected $type = 'jbcategorylist';

    public function getInput()
    {
        // get app
        $app          = App::getInstance('zoo');
        $categoryName = array();
        $attr         = array();
        $options      = array();

        $appList = JBModelApp::model()->getList();

        if ((int)$this->element->attributes()->multiple) {
            $attr['multiple'] = 'multiple';
        }

        if ((int)$this->element->attributes()->required) {
            $attr['required'] = 'required';
        }

        if ((int)$this->element->attributes()->size) {
            $attr['size'] = (int)$this->element->attributes()->size;
        }else{
            $attr['size'] = 10;
        }

        if (!empty($appList)) {

            foreach ($appList as $application) {

                $allCategories = $application->getCategories(true);

                if(!empty($allCategories)){

                    foreach ($allCategories as $category) {
                        $categoryName[$category->id] = $category->name;
                    }
                    $options[$application->name] = $categoryName;
                }
            }

            return JHtml::_('select.groupedlist', $options, $this->getName($this->fieldname), array(
                'list.attr'   => $app->jbhtml->buildAttrs($attr),
                'list.select' => $this->value,
                'group.items' => null,
            ));
        }
    }
}
