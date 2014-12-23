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


/**
 * Class JBFormHelper
 */
class JBFormHelper extends AppHelper
{
    /**
     * Render Joomla form
     * @param $formName
     * @param array $options
     * @param array $data
     * @throws AppException
     * @return null|string
     */
    public function render($formName, $options = array(), $data = array())
    {
        $formPath = null;
        $xmlPaths = $this->_getXmlFormPaths();

        foreach ($xmlPaths as $path) {
            $xmlForm = $path . DS . $formName . '.xml';

            if (file_exists($xmlForm)) {
                $formPath = $xmlForm;
                break;
            }
        }
         if (!($xmlPath = $formPath)) {
             throw new AppException('Form ' . $formName . ' not found!');
             return null;
         }

        $form = $this->_createJoomlaForm($formName, $xmlPath, $options);
        $form->bind($data);

        $html = array();

        $fields = $form->getFieldsets();
        if (!empty($fields)) {
            $html[] = '<div>';
            foreach ($fields as $fieldSet) {

                $fieldSetName = $fieldSet->name;

                $html[] = '<fieldset class="' . $fieldSetName . '">';

                if ($fieldSet->label) {
                    $html[] = '<legend>' . JText::_($fieldSet->label) . '</legend>';
                }

                if ($fieldSet->description) {
                    $html[] = '<p>' . JText::_($fieldSet->description) . '</p>';
                }

                foreach ($form->getFieldset($fieldSetName) as $field) {
                    $html[] = $this->_renderFieldRow($field);
                }

                $html[] = '</fieldset>';
            }

            $html[] = '</div>';
        }

        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        $this->app->jbtoolbar->save();

        $options = array_merge($this->_getDefaultFormOptions(), $options);

        $submitLabel = isset($options['submit']) ? $options['submit'] : JText::_('JBZOO_FORM_SAVE');
        unset($options['submit']);

        $html = '<form ' . $this->app->jbhtml->buildAttrs($options) . ' >' . implode(" \n", $html);

        if ($submitLabel) {
            $html .= '<div class="jbzoo-submit">'
                . '<input type="submit" class="jbzoo-button" name="send" value="' . $submitLabel .'" />'
                . '</div>';
        }

        $html .= '</form>' . '<div class="clr"></div>';

        return $html;
    }

    /**
     * Render only one form field row
     * @param $controlHtml
     * @param $label
     * @param null $id
     * @return string
     */
    public function renderRow($controlHtml, $label, $id = null)
    {
        $html = array();

        $html[] = '<div class="uk-form-row">';
        $html[] = '<div class="uk-form-label">';
        $html[] = '<label ' . ($id ? 'for="' . $id . '"' : '') . '>' . JText::_($label) . '</label>';
        $html[] = '</div>';
        $html[] = '<div class="uk-form-controls">' . $controlHtml . '</div>';
        $html[] = '</div>';

        return implode(" \n", $html);
    }

    /**
     * Create Joomla Form
     * @param $name
     * @param $xmlPath
     * @param array $options
     * @return JForm
     */
    protected function _createJoomlaForm($name, $xmlPath, array $options = array())
    {
        jimport('joomla.form.form');
        $options = $this->app->data->create($options);
        $jform   = JForm::getInstance($name, $xmlPath, array(
            'control' => $options->get('control', JBRequestHelper::ADMIN_FORM_KEY),
        ));

        return $jform;
    }

    /**
     * Render one form row
     * @param JFormField $field
     * @return string
     */
    protected function _renderFieldRow($field)
    {
        $className = strtolower(get_class($field));

        $html = array();

        if ($field->hidden) {
            $html[] = $field->input;

        } else {

            $label = preg_replace("#title=\".*?\"#ius", '', $field->label);

            $html[] = '<div class="uk-form-row">';
            $html[] = '<div class="' . $className . '-label uk-form-label">';
            $html[] = $label;
            $html[] = '<span class="description-label">' . JText::_($field->description) . '</span>';
            $html[] = '</div>';
            $html[] = '<div class="uk-form-controls">' . $field->input . '</div>';
            $html[] = '</div>';
        }

        return implode(" \n", $html);
    }

    /**
     * @return array
     */
    protected function _getDefaultFormOptions()
    {
        $_options = array(
            'action'         => $this->app->jbrouter->admin(),
            'method'         => 'post',
            'class'          => 'uk-form uk-form-horizontal jbadminform form-validate',
            'accept-charset' => "UTF-8",
            'enctype'        => 'multipart/form-data',
            'name'           => 'jbzooForm',
            'id'             => 'jbzooForm'
        );

        $isSite = $this->app->jbenv->isSite();

        if ($isSite) {
            $_options = array(
                'method'         => 'post',
                'class'          => 'jbform form-validate',
                'accept-charset' => "UTF-8",
                'enctype'        => 'multipart/form-data',
                'name'           => 'jbzooForm',
                'id'             => 'jbzooForm',
            );
        }

        return $_options;
    }

    /**
     * @return array
     */
    protected function _getXmlFormPaths()
    {
        $paths = array();
        $path  = $this->app->path->path('cart-elements:' . 'payment/');

        if ($path) {
            jimport('joomla.filesystem.folders');
            $folders = JFolder::folders($path);

            foreach ($folders as $folder) {
                $formDir = $path . $folder . '/forms/';

                if (JFolder::exists($formDir)) {
                    $paths[] = $formDir;
                    continue;
                }
            }
        }

        $paths[] = $this->app->path->path('jbconfig:forms/');

        return $paths;
    }

}
