<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
     * @param       $formName
     * @param array $options
     * @param array $data
     * @throws AppException
     * @return null|string
     */
    public function render($formName, $options = array(), $data = array())
    {
        $options = array_merge($this->getDefaultFormOptions(), $options);
        $options = $this->app->data->create($options);

        $formPath = null;
        $xmlPaths = $this->_getXmlFormPaths();

        foreach ($xmlPaths as $path) {
            $xmlForm = JPath::clean($path . '/' . $formName . '.xml');

            if (JFile::exists($xmlForm)) {
                $formPath = $xmlForm;
                break;
            }
        }

        if (!$formPath) {
            throw new AppException('Form ' . $formName . ' not found!');
        }

        $form = $this->_createJoomlaForm($formName, $formPath, $options);
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

        $html = '<form ' . $this->app->jbhtml->buildAttrs($options) . ' >' . implode(PHP_EOL, $html);

        if ($this->app->jbenv->isSite() || $options->get('showSubmit', 0)) {

            $submitLabel = $options->get('submit', JText::_('JBZOO_FORM_SAVE'));
            $html .= '<div class="jbzoo-submit">';
            $html .= '<input type="submit" class="jbzoo-button uk-button uk-button-success" name="send" value="' . $submitLabel . '" />';
            $html .= '</div>';
        } else {

            if (!$options->get('hideSubmit', 0)) {
                $this->app->jbtoolbar->save();
            }
        }

        $html .= '</form>' . JBZOO_CLR;

        return $html;
    }

    /**
     * Render only one form field row
     * @param      $controlHtml
     * @param      $label
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
     * @param       $name
     * @param       $xmlPath
     * @param array $options
     * @return JForm
     */
    protected function _createJoomlaForm($name, $xmlPath, $options = array())
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
            $label = str_ireplace('hasTip', '', $label);

            $html[] = '<div class="uk-form-row">';
            $html[] = '<div class="' . $className . '-label uk-form-label">';
            $html[] = $label;
            $html[] = '<div class="description-label">' . JText::_($field->description) . '</div>';
            $html[] = '</div>';
            $html[] = '<div class="uk-form-controls">' . $field->input . '</div>';
            $html[] = '</div>';
        }

        return implode(" \n", $html);
    }

    /**
     * @return array
     */
    public function getDefaultFormOptions()
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
                $formDir = $path . '/' . $folder . '/forms/';

                if (JFolder::exists($formDir)) {
                    $paths[] = $formDir;
                    continue;
                }
            }
        }

        $paths[] = $this->app->path->path('jbconfig:forms/');

        return $paths;
    }

    /**
     * @param string $formName
     * @param array  $data
     * @return string
     * @throws AppException
     */
    public function renderFields($formName, $data = array())
    {
        $formPath = null;
        $xmlPaths = $this->_getXmlFormPaths();

        foreach ($xmlPaths as $path) {
            $xmlForm = JPath::clean($path . '/' . $formName . '.xml');

            if (JFile::exists($xmlForm)) {
                $formPath = $xmlForm;
                break;
            }
        }

        if (!$formPath) {
            throw new AppException('Form ' . $formName . ' not found!');
        }

        $form = $this->_createJoomlaForm($formName, $formPath, array());
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

        $html[] = JBZOO_CLR;

        return implode(PHP_EOL, $html);
    }

}
