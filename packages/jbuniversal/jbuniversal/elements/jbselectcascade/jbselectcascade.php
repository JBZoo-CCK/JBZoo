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


App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');


/**
 * Class ElementJBSelectCascade
 */
class ElementJBSelectCascade extends ElementRepeatable implements iRepeatSubmittable
{
    const VALIDATE_MODE_ANY = 'any';

    /**
     * @type string
     */
    protected $_selGroup = '';
    
    /**
     * @type array
     */
    protected $_selectInfo = null;

    /**
     * Element constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_selGroup = $this->app->jbstring->getId('group');
    }

    /**
     * @param array $params
     * @return bool|int
     */
    protected function _hasValue($params = array())
    {
        $values = $this->_getSearchData();
        return !empty($values);
    }

    /**
     * Get search data
     * @return null|string
     */
    protected function _getSearchData()
    {
        $result = $this->_getValuesList();
        if (!is_array($result)) {
            $result = array();
        }

        $result = array_reverse($result);

        return (empty($result) ? null : implode("\n", $result));
    }

    /**
     * @return string
     */
    protected function _edit()
    {
        if (is_null($this->_selectInfo)) {
            $this->_selectInfo = $this->_getSelectInfo();
        }

        $values      = $this->_getValuesList();
        $cascadeName = $this->getControlName('list-%s');

        return $this->app->jbhtml->selectCascade($this->_selectInfo, $cascadeName, $values, array(), $this->_selGroup);
    }

    /**
     * Render submission
     * @param array $params
     * @return string|void
     */
    public function _renderSubmission($params = array())
    {
        return $this->_edit();
    }

    /**
     * Validate submission
     * @param JSONData $value
     * @param array    $params
     * @return array
     * @throws AppValidatorException
     */
    public function _validateSubmission($value, $params)
    {
        $selectInfo = $this->_getSelectInfo();

        $result = array();

        if ($params->get('mode') == self::VALIDATE_MODE_ANY) {

            for ($i = 0; $i <= $selectInfo['maxLevel']; $i++) {
                $result['list-' . $i] = $value->get('list-' . $i);
            }

            $resultCheck = array_filter($result);
            if (empty($resultCheck) && $params->get('required')) {
                throw new AppValidatorException('This field is required', AppValidator::ERROR_CODE_REQUIRED);
            }

        } else {
            $val = $value->flattenRecursive();
            for ($i = 0; $i < count($val); $i++) {
                $result['list-' . $i] = $this->app->validator
                    ->create('string', array(
                        'required' => $params->get('required')
                    ))
                    ->clean($value->get('list-' . $i));
            }

        }

        return $result;
    }

    /**
     * Render one row of element
     * @param array $params
     * @return string
     */
    protected function _render($params = array())
    {
        $selectInfo = $this->_getSelectInfo();
        $valueList  = $this->_getValuesList();

        $template = $params->get('template', 'default');

        if ('last' == $template) {
            $result = array(end($valueList));

        } else if ('label' == $template) {

            $result = array();
            foreach ($selectInfo['names'] as $key => $title) {
                if (!empty($title) && !empty($valueList[$key])) {
                    $result[] =
                        '<span class="jbselect-label jbselect-label-' . $key . '">' . $title . ':<span> '
                        . '<span class="jbselect-value jbselect-value-' . $key . '">' . $valueList[$key] . '</span>';
                }
            }

        } else {
            $result = $valueList;
        }

        return $this->app->element->applySeparators($params->get('separated_values_by'), $result);
    }

    /**
     * Render
     * @param array $params
     */
    public function render($params = array())
    {
        $result = array();
        $params = $this->app->data->create($params);

        $display = $params->get('display', 'all');
        if ('all' == $display) {
            foreach ($this as $self) {
                $result[] = $this->_render($params);
            }

        } else if ('first' == $display) {
            $this->seek(0);
            $result[] = $this->_render($params);

        } else if ('all_without_first' == $display) {
            $this->seek(1);
            while ($this->valid()) {
                $result[] = $this->_render($params);
                $this->next();
            }
        }

        return $this->app->element->applySeparators($params->get('separated_by'), $result);
    }

    /**
     * Render element
     * @param array $params
     * @return string
     */
    public function edit($params = array())
    {
        $this->app->jbassets->selectCascade();
        $html = parent::edit($params);
        return '<div class="jbcascade-group jsCascadeGroup">' . $html . '</div>';
    }

    /**
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit($params);
    }

    /**
     * Get clear values list
     * @return array
     */
    protected function _getValuesList()
    {
        // get values
        $result = array();

        $i = 0;
        while (true) {
            $value = $this->get('list-' . $i, '');
            $value = JString::trim($value);

            if (empty($value)) {
                break;
            }

            $result[] = strtr($value, "\"", "'");
            $i++;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getSelectInfo()
    {
        return $this->app->jbselectcascade->getItemList(
            $this->config->get('select_names', ''),
            $this->config->get('items', '')
        );
    }

}
