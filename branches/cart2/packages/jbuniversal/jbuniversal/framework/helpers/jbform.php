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
        if (!($xmlPath = $this->app->path->path('jbconfig:forms/' . $formName . '.xml'))) {
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

        $options = array_merge(array(
            'action'         => $this->app->jbrouter->admin(),
            'method'         => 'post',
            'class'          => 'uk-form uk-form-horizontal jbadminform form-validate',
            'accept-charset' => "UTF-8",
            'enctype'        => 'multipart/form-data',
        ), $options);

        $submitLabel = isset($options['submit']) ? $options['submit'] : JText::_('JBZOO_FORM_SUBMIT');

        $html = '<form ' . $this->app->jbhtml->buildAttrs($options) . ' >'
            . implode(" \n", $html)
            . '<div class="submit-btn">'
            . '<input type="submit" class="uk-button uk-button-primary" name="send" value="' . $submitLabel . '" />'
            . '</div>'
            . '</form>'
            . '<div class="clr"></div>';

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
        $jform = JForm::getInstance($name, $xmlPath, array(
            'control' => 'jbzooform'
        ));

        return $jform;
    }

    /**
     * Render one form row
     * @param $field
     * @return string
     */
    protected function _renderFieldRow($field)
    {
        $className = strtolower(get_class($field));

        $html = array();

        if ($field->hidden) {
            $html[] = $field->input;
        } else {
            $html[] = '<div class="uk-form-row">';
            $html[] = '<div class="' . $className . '-label uk-form-label"> ' . $field->label . ' </div>';
            $html[] = '<div class="uk-form-controls">';
            $html[] = $field->input;
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(" \n", $html);
    }

}
