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
 * Class JBHTMLHelper
 */
class JBHTMLHelper extends AppHelper
{
    /**
     * Render option list
     * @param        $data
     * @param        $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @param bool $isLabelWrap
     * @return string
     */
    public function radio(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false,
        $isLabelWrap = true
    )
    {
        if (empty($data)) {
            return null;
        }

        $attribs = $this->_buildAttrs($attribs);

        return $this->_list('radio', $data, $name, $attribs, $selected, $idtag, $translate, $isLabelWrap);
    }

    /**
     * Render checkbox list
     * @param        $data
     * @param        $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @param bool $isLabelWrap
     * @return string
     */
    public function checkbox(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false,
        $isLabelWrap = true
    )
    {
        if (empty($data)) {
            return null;
        }

        if ($idtag) {
            $attribs['id'] = $idtag;
        }

        $attribs = $this->_buildAttrs($attribs);

        return $this->_list('checkbox', $data, $name, $attribs, $selected, $idtag, $translate, $isLabelWrap);
    }

    /**
     * Render select list
     * @param      $data
     * @param      $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @return string
     */
    public function select(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false
    )
    {
        if (empty($data)) {
            return null;
        }

        if ($idtag) {
            $attribs['id'] = $idtag;
        }

        if (is_array($attribs) && isset($attribs['multiple'])) {
            $name = $name . '[]';
        }

        $name = preg_replace('#\[\]\[\]$#', '[]', $name); // hack for difference J2.5 and J3.x

        $attribs = $this->_buildAttrs($attribs);

        return $this->app->html->_('zoo.genericlist', $data, $name, $attribs, 'value', 'text', $selected, $idtag, $translate);
    }

    /**
     * Render text field
     * @param      $name
     * @param null $value
     * @param null $attribs
     * @param null $idtag
     * @return string
     */
    public function text($name, $value = null, $attribs = null, $idtag = null)
    {
        if ($idtag && is_array($attribs)) {
            $attribs['id'] = $idtag;
        }

        $attribs = $this->_buildAttrs($attribs);
        if (strpos($attribs, 'jsAutocomplete') !== false) {

            $this->app->jbassets->jqueryui();
            $this->app->jbassets->initAutocomplete();
        }

        return $this->app->html->_('control.text', $name, $value, $attribs);
    }

    /**
     * Render color field
     * @param string $inputType
     * @param array $data
     * @param string $name
     * @param null $selected
     * @return string
     */
    public function colors($inputType = 'checkbox', $data, $name, $selected = null)
    {
        $html = array();
        $uniq = $this->app->jbstring->getId();
        $i    = 0;

        $html[] = '<div id="' . $uniq . '" class="jbzoo-colors">';

        foreach ($data as $key => $value) {

            $isFile = false;

            if ($this->app->jbcolor->isFile($value)) {
                $isFile = $value;
            }

            $id = $this->app->jbstring->getId();

            $attribs = array(
                'type'  => $inputType,
                'name'  => $name . '[]',
                'id'    => $id,
                'title' => $key,
                'value' => $key,
                'class' => 'jbcolor-input'
            );

            $valueSlug = $this->app->string->sluggify(!$isFile ? $key : basename($value));

            $labelAttribs = array(
                'for'   => $id,
                'title' => $key,
                'class' => 'jbcolor-label hasTip ' . $inputType . ' value-' . $valueSlug,
            );

            $divAttribs = array(
                'style' => 'background-color: ' . (!$isFile ? '#' . $value : 'transparent')
            );

            if (is_array($selected)) {
                foreach ($selected as $val) {

                    if ($inputType == 'radio' && $i >= 1) {
                        continue;
                    }

                    if ($key == $val) {
                        $attribs['checked'] = 'checked';
                        $attribs['class'] .= ' checked';
                        $i++;
                        break;
                    }
                }
            } else {
                if ((string)$key == (string)$selected) {
                    $attribs['checked'] = 'checked';
                    $attribs['class'] .= ' checked';
                }
            }

            $html[] = ' <input ' . $this->_buildAttrs($attribs) . ' />'
                . '<label ' . $this->_buildAttrs($labelAttribs) . '>';

            $html[] = ($isFile ? '<div class="checkIn" style="background: url(\'' . $isFile . '\') center;" >' : '');
            $html[] = '<div ' . $this->_buildAttrs($divAttribs) . '></div>';

            $html[] = ($isFile ? '</div>' : '');
            $html[] = '</label>';
        }

        $html[] = '</div>';

        $multiple = $inputType == 'checkbox' ? 1 : 0;
        $this->app->jbassets->initJBColorHelper($uniq, $multiple);

        return implode("\n", $html);
    }

    /**
     * Render hidden field
     * @param      $name
     * @param null $value
     * @param null $attribs
     * @param null $idtag
     * @return string
     */
    public function hidden($name, $value = null, $attribs = null, $idtag = null)
    {
        if ($idtag) {
            $attribs['id'] = $idtag;
        }

        $attribs = $this->_buildAttrs($attribs);
        $value   = $this->cleanAttrValue($value);

        return '<input type="hidden" name="' . $name . '" ' . $attribs . ' value="' . $value . '" />';
    }

    /**
     * Render calendar element
     * @param       $name
     * @param null $value
     * @param null $attribs
     * @param null $idtag
     * @param array $params
     * @return string
     */
    public function calendar($name, $value = null, $attribs = null, $idtag = null, $params = array())
    {
        if ($idtag) {
            $attribs['id'] = $idtag;
        }

        $params['dateFormat'] = trim($params['dateFormat']);

        $this->app->jbassets->jqueryui();
        $this->app->jbassets->addScript('jQuery(function($){
            $("#' . $idtag . '").datepicker(' . json_encode($params) . ');
        });');

        return $this->text($name, $value, $attribs, $idtag);
    }

    /**
     * Render jQueryUI slider
     * @param array $params
     * @param string $value
     * @param string $name
     * @param string $idtag
     * @return string
     */
    public function slider($params, $value = '', $name = '', $idtag = '')
    {
        if (!empty($value) && is_string($value)) {
            $value = explode('/', $value);
        } else {
            $value = array($params['min'], $params['max']);
        }

        $this->app->jbassets->jqueryui();
        $this->app->jbassets->addScript('jQuery(function($){
            $("#' . $idtag . '-wrapper").removeAttr("slide");
            $("#' . $idtag . '-wrapper")[0].slide = null;
            $("#' . $idtag . '-wrapper").slider({
                "range" : true,
                "min"   : ' . ((float)$params['min'] ? floor((float)$params['min']) : 0) . ',
                "max"   : ' . ((float)$params['max'] ? ceil((float)$params['max']) : 10000) . ',
                "step"  : ' . ((float)$params['step'] ? round((float)$params['step'], 2) : 100) . ',
                "values": [' . round((float)$value['0'], 2) . ', ' . round((float)$value['1'], 2) . '],
                "slide" : function(event,ui) {
                    $("#' . $idtag . '-value").val(ui.values[0] + "/" + ui.values[1]);
                    $("#' . $idtag . '-value-0").html(numberFormat(ui.values[0], 0, ".", " "));
                    $("#' . $idtag . '-value-1").html(numberFormat(ui.values[1], 0, ".", " "));
                }
            });
		    $("#' . $idtag . '-value").val(' . (float)$value['0'] . '+ "/" +' . (float)$value['1'] . ');
        });');

        return '<div id="' . $idtag . '-wrapper"> </div>' . "\n"
        . '<span id="' . $idtag . '-value-0" class="slider-value-0">' . $value['0'] . '</span>' . "\n"
        . '<span id="' . $idtag . '-value-1" class="slider-value-1">' . $value['1'] . '</span>' . "\n"
        . '<input type="hidden" id="' . $idtag . '-value" name="' . $name . '" />' . "\n";
    }

    /**
     * Render option list
     * @param        $data
     * @param        $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @return string
     */
    public function buttonsJqueryUI(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false
    )
    {
        if (isset($attribs['multiple'])) {
            $html = $this->checkbox($data, $name, $attribs, $selected, $idtag, $translate, false);

        } else {
            $html = $this->radio($data, $name, $attribs, $selected, $idtag, $translate, false);
        }

        $this->app->jbassets->jqueryui();
        $this->app->jbassets->addScript('jQuery(function($){
            $("#' . $idtag . '-wrapper' . '").buttonset();
        });');

        return '<div id="' . $idtag . '-wrapper">' . $html . '</div>';
    }

    /**
     * Render chosen
     * @param      $data
     * @param      $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @param array $params
     * @return string
     */
    public function selectChosen(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false,
        $params = array()
    )
    {
        $this->app->jbassets->chosen();

        $this->app->jbassets->addScript('jQuery(function($){
            $("#' . $idtag . '").chosen();
        });');

        $attribs['data-no_results_text'] = JText::_('JBZOO_CHOSEN_NORESULT');
        $attribs['data-placeholder']     = (isset($params['placeholder'])) ? $params['placeholder'] : JText::_('JBZOO_CHOSEN_SELECT');

        return $this->select($data, $name, $attribs, $selected, $idtag, $translate);
    }

    /**
     * Select cascade
     * @param array $selectInfo
     * @param string $name
     * @param array $selected
     * @param array $attribs
     * @param bool $idtag
     * @return string
     */
    public function selectCascade(
        $selectInfo,
        $name,
        $selected = array(),
        $attribs = null,
        $idtag = false)
    {
        $itemList  = $selectInfo['items'];
        $maxLevel  = $selectInfo['maxLevel'];
        $listNames = $selectInfo['names'];

        $uniqId         = $this->app->jbstring->getId();
        $deepLevelCheck = $deepLevel = 0;

        $html = array();
        for ($i = 0; $i <= $maxLevel; $i++) {

            $value = isset($selected[$i]) ? $selected[$i] : null;

            $attrs = array(
                'class'      => 'jbselect-' . $i,
                'name'       => $name . '[]',
                'list-order' => $i,
                'disabled'   => 'disabled',
                'id'         => 'jbselect-' . $i . '-' . $uniqId,
            );

            $listName = isset($listNames[$i]) ? $listNames[$i] : ' ';

            $html[] = '<div>';
            $html[] = '<label for="' . $attrs['id'] . '">' . $listName . '</label>';
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

            if (isset($selectInfo['items'][$value]) && !empty($selectInfo['items'][$value])) {
                $tmpItems = $selectInfo['items'][$value];
            }

            $html[] = '</select></div>';
        }

        $this->app->jbassets->initSelectCascade();
        $this->app->jbassets->initJBCascadeSelect($uniqId, $selectInfo['items']);

        $attribs['class'][] = 'jbcascadeselect';

        return '<div class="jbcascadeselect-wrapper jbcascadeselect-' . $uniqId . '">'
        . '<div ' . $this->app->jbhtml->buildAttrs($attribs) . '>'
        . implode(" ", $html)
        . '</div></div>';
    }

    /**
     * Generates an HTML checkbox/radio list.
     * @param   string $inputType    Type of html input element
     * @param   array $data         An array of objects
     * @param   string $name         The value of the HTML name attribute
     * @param   string $attribs      Additional HTML attributes for the <select> tag
     * @param   string $selected     The name of the object variable for the option text
     * @param   boolean $idtag        Value of the field id or null by default
     * @param   boolean $translate    True if options will be translated
     * @param   boolean $isLabelWrap  True if options wrappeed label tag
     * @return  string HTML for the select list
     */
    private function _list($inputType, $data, $name, $attribs = null, $selected = null, $idtag = false,
                           $translate = false, $isLabelWrap = false
    )
    {
        reset($data);

        if (is_array($attribs)) {
            $attribs = $this->_buildAttrs($attribs);
        }

        if ($inputType == 'checkbox') {
            $name = $name . '[]';
        }

        $html = array();
        foreach ($data as $keyObj => $obj) {

            if (is_object($obj)) {
                $value = $obj->value;
                $text  = $translate ? JText::_($obj->text) : $obj->text;
                $id    = (isset($obj->id) ? $obj->id : null);
            } else {
                $value = $keyObj;
                $text  = $translate ? JText::_($obj) : $obj;
                $id    = null;
            }

            $valueSlug = $this->app->string->sluggify($value);

            $extra = array(
                'value' => $value,
                'name'  => $name,
                'type'  => $inputType,
                'id'    => 'id' . $valueSlug . '-' . $this->app->jbstring->getId(),
                'class' => 'value-' . $valueSlug
            );

            if (is_array($selected)) {

                foreach ($selected as $val) {

                    if ($value == $val) {
                        $extra['checked'] = 'checked';
                        break;
                    }
                }

            } else {
                if ((string)$value == (string)$selected) {
                    $extra['checked'] = 'checked';
                }
            }

            $extraLabel = array(
                'for'   => $extra['id'],
                'class' => array(
                    $inputType . '-lbl',
                    'lbl-' . $this->app->string->sluggify($value),
                ),
            );

            if ($isLabelWrap) {
                $html[] = '<label ' . $this->_buildAttrs($extraLabel) . '>'
                    . ' <input ' . $this->_buildAttrs($extra) . ' /> '
                    . $text . '</label>';

            } else {
                $html[] = ' <input ' . $this->_buildAttrs($extra) . ' />'
                    . '<label ' . $this->_buildAttrs($extraLabel) . '> ' . $text . '</label>';

            }

        }

        return implode("\n\t", $html);
    }

    /**
     * Build attrs
     * @param $attrs
     * @return null|string
     */
    public function buildAttrs($attrs)
    {
        return $this->_buildAttrs($attrs);
    }

    /**
     * Build attrs
     * TODO: Remove method, replace to public
     * @param $attrs
     * @return null|string
     */
    protected function _buildAttrs($attrs)
    {
        $result = ' ';

        if (is_string($attrs)) {
            $result .= $attrs;

        } elseif (!empty($attrs)) {
            foreach ($attrs as $key => $param) {

                $param = (array)$param;
                $value = $this->cleanAttrValue(implode(' ', $param));

                if (!empty($value) || $value == '0' || $key == 'value') {
                    $result .= ' ' . $key . '="' . $value . '"';
                }
            }
        }

        return JString::trim($result);
    }

    /**
     * Clear attribute value
     * @param string $value
     * @return string
     */
    public function cleanAttrValue($value)
    {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value = JString::trim($value);

        return $value;
    }

}
