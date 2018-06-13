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
