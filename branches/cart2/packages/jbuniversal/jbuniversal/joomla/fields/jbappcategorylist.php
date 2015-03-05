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
 * Class JFormFieldJBAppCategoryList
 */
class JFormFieldJBAppCategoryList extends JFormField
{

    protected $type = 'jbappcategorylist';

    /**
     * @return null|string
     */
    public function getInput()
    {
        $app       = App::getInstance('zoo');
        $idElement = uniqid('element');

        $options      = array(0 => JText::_('JBZOO_FIELDS_APP'));
        $categoryList = array();
        $html         = array();
        $value        = array('appId' => 0, 'catId' => '');

        $appList = JBModelApp::model()->getList();

        if (!empty($appList)) {

            foreach ($appList as $application) {

                $options[$application->id] = $application->name;

                if ((int)$this->element->attributes()->show_categories !== 0) {

                    $allCategories = $application->getCategories(true);
                    $categories    = $application->app->tree->buildList(0, $application->app->tree->build($allCategories, 'Category'));

                    if ((int)$this->element->attributes()->showcategories_all == 1) {
                        $categoryList[$application->id]['-1'] = ' - ' . JText::_('JBZOO_ALL') . ' - ';
                    }
                    $categoryList[$application->id]['0'] = ' - ' . JText::_('JBZOO_FIELDS_FRONTPAGE') . ' - ';

                    foreach ($categories as $category) {
                        $categoryList[$category->application_id][$category->id] = $category->treename;
                    }
                }
            }

            $html[] = '<div id="' . $idElement . '" class="application">';
            $html[] = '<div class="jbapp-list zoo-application">';
            $html[] = $app->jbhtml->select($options, "", array('class' => 'application'), $this->value);
            $html[] = '</div>';

            foreach ($categoryList as $id => $category) {
                $html[] = '<div class="jbcategory-list app-' . $id . '" style="display:none;">';
                $html[] = $app->jbhtml->select($categoryList[$id], "", '', $this->value);
                $html[] = '</div>';

                if (!empty($this->value)) {
                    $arr            = explode(':', $this->value);
                    $value['catId'] = $arr[1];
                }
            }

            if (!empty($this->value)) {
                list($value['appId'], $value['catId']) = explode(':', $this->value);
            }

            $html[] = $app->jbhtml->hidden($this->getName($this->fieldname), $this->value, array('class' => 'hidden-value'));
            $html[] = '</div>';
            $html[] =  $app->jbassets->widget('#' . $idElement, 'JBCategoryList', $value, true);

            $app->jbassets->jQuery();
            $app->jbassets->js("jbapp:joomla/fields/jbappcategorylist.js");

            return implode(PHP_EOL, $html);
        }

        return JText::_('JBZOO_MODCATEGORY_EMPTY_APP');
    }
}
