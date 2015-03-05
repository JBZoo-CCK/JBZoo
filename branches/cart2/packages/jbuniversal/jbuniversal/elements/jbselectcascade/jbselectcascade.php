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


App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');


/**
 * Class ElementJBSelectCascade
 */
class ElementJBSelectCascade extends ElementRepeatable implements iRepeatSubmittable
{
    const VALIDATE_MODE_ANY = 'any';

    protected $_maxLevel = null;
    protected $_uniqid = '';
    protected $_itemList = array();
    protected $_listNames = array();

    /**
     * Element constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_uniqid = uniqid();

        $this->registerCallback('ajaxGetList');
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

        return (empty($result) ? null : implode(PHP_EOL, $result));
    }

    /**
     * @return string
     */
    protected function _edit()
    {
        $values = $this->_getValuesList();

        $itemList  = $this->_itemList;
        $deepLevel = $deepLevelCheck = 0;

        $html = array();
        for ($i = 0; $i <= $this->_maxLevel; $i++) {

            $value = isset($values[$i]) ? $values[$i] : null;

            $attrs = array(
                'class'      => 'jbselect-' . $i,
                'name'       => $this->getControlName('list-' . $i),
                'list-order' => $i,
                'disabled'   => 'disabled',
                'id'         => 'jbselect-' . $i . '-' . $this->_uniqid,
            );

            $html[] = '<div>';
            $label  = isset($this->_listNames[$i]) ? $this->_listNames[$i] : '';
            $html[] = '<label for="' . $attrs['id'] . '">' . $label . '</label><br/>';
            $html[] = '<select ' . $this->app->jbhtml->buildAttrs($attrs) . '>';
            $html[] = '<option value=""> - ' . JText::_('JBZOO_ALL') . ' - </option>';

            if ($deepLevelCheck == $deepLevel) {
                $deepLevelCheck++;
                foreach ($itemList as $key => $item) {
                    if ($value == $key) {
                        $html[] = '<option value="' . $key . '" selected="selected">' . $key . '</option>';
                    } else {
                        $html[] = '<option value="' . $key . '">' . $key . '</option>';
                    }
                }
            }

            if (isset($itemList[$value])) {
                $itemList = $itemList[$value];
                $deepLevel++;
            }

            if (isset($this->_itemList[$value]) && !empty($this->_itemList[$value])) {
                $tmpItems = $this->_itemList[$value];
            }

            $html[] = '</select></div>';
        }

        $wrapperAtts = array(
            'uniqid' => $this->_uniqid,
            'class'  => 'jbcascadeselect',
        );

        return '<div ' . $this->app->jbhtml->buildAttrs($wrapperAtts) . '>'
        . implode(" ", $html)
        . '</div>';
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
     * Render submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        $html = parent::renderSubmission($params);
        $this->app->jbassets->initSelectCascade();
        $this->app->jbassets->initJBCascadeSelect($this->_uniqid, $this->_itemList);

        return '<div class="jbcascadeselect-wrapper jbcascadeselect-' . $this->_uniqid . '">' . $html . '</div>';
    }

    /**
     * Validate submission
     * @param JSONData $value
     * @param array $params
     * @return array
     * @throws AppValidatorException
     */
    public function _validateSubmission($value, $params)
    {
        $this->_getValuesList();

        $result = array();

        if ($params->get('mode') == self::VALIDATE_MODE_ANY) {

            for ($i = 0; $i <= $this->_maxLevel; $i++) {
                $result['list-' . $i] = $value->get('list-' . $i);
            }

            $resultCheck = array_filter($result);
            if (empty($resultCheck) && $params->get('required')) {
                throw new AppValidatorException('This field is required', AppValidator::ERROR_CODE_REQUIRED);
            }

        } else {

            $val = $value->flattenRecursive();

            for ($i = 0; $i < count($val); $i++) {
                $validator = $this->app->validator->create('string', array('required' => $params->get('required')));

                $result['list-' . $i] = $validator->clean($value->get('list-' . $i));
            }

        }

        return $result;
    }

    /**
     * Load item list from ajax request
     * @param array $params
     * @return int
     */
    public function ajaxGetList(array $params = array())
    {
        $this->_getValuesList();
        jexit(json_encode($this->_itemList));
    }

    /**
     * Render one row of element
     * @param array $params
     * @return string
     */
    protected function _render($params = array())
    {
        $valueList = $this->_getValuesList();
        $result    = $valueList;

        $template = $params->get('template', 'default');

        if ('last' == $template) {
            $result = array(end($valueList));

        } else if ('label' == $template) {

            $result = array();
            foreach ($this->_listNames as $key => $title) {
                if (!empty($title) && !empty($valueList[$key])) {
                    $result[] = '<span class="jbselect-label jbselect-label-' . $key . '">' . $title . ':<span> '
                        . '<span class="jbselect-value jbselect-value-' . $key . '">' . $valueList[$key] . '</span>';
                }
            }

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
        $html = parent::edit($params);
        $this->app->jbassets->initSelectCascade();
        $this->app->jbassets->initJBCascadeSelect($this->_uniqid, $this->_itemList);

        return '<div class="jbcascadeselect-wrapper jbcascadeselect-' . $this->_uniqid . '">' . $html . '</div>';
    }

    /**
     * Get clear values list
     * @return array
     */
    protected function _getValuesList()
    {
        // init internal vars
        if (is_null($this->_maxLevel)) {

            $itemList = $this->app->jbselectcascade->getItemList(
                $this->config->get('select_names', ''),
                $this->config->get('items', '')
            );

            $this->_itemList  = $itemList['items'];
            $this->_maxLevel  = $itemList['maxLevel'];
            $this->_listNames = $itemList['names'];

        }

        // get values
        $result = array();
        for ($i = 0; $i <= $this->_maxLevel; $i++) {
            $value = JString::trim($this->get('list-' . $i, ''));
            if (!empty($value)) {
                $result[] = $value;
            }
        }

        return $result;
    }

}
