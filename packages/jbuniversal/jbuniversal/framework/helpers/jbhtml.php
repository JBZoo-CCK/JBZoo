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
 * Class JBHTMLHelper
 */
class JBHTMLHelper extends AppHelper
{
    /**
     * Render option list
     * @param        $data
     * @param        $name
     * @param null   $attribs
     * @param null   $selected
     * @param bool   $idtag
     * @param bool   $translate
     * @param bool   $isLabelWrap
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

        return $this->_list('radio', $data, $name, $attribs, $selected, $idtag, $translate, $isLabelWrap);
    }

    /**
     * Render checkbox list
     * @param        $data
     * @param        $name
     * @param null   $attribs
     * @param null   $selected
     * @param bool   $idtag
     * @param bool   $translate
     * @param bool   $isLabelWrap
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
     * Render select list
     * @param      $data
     * @param      $name
     * @param null $attribs
     * @param null $selected
     * @param bool $idtag
     * @param bool $translate
     * @return string
     */
    public function selectGrouped(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false
    )
    {
        jimport('joomla.html.html.select');

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

        $list = array();

        $i = 0;
        foreach ($data as $group => $options) {

            $list[$i] = array('value' => '', 'text' => $group, 'items' => array());
            foreach ($options as $key => $value) {
                $list[$i]['items'][] = array('value' => $key, 'text' => $value);
            }

            $i++;
        }

        return JHtml::_('select.groupedlist', $list, $name, array(
            'list.attr'      => $this->_buildAttrs($attribs),
            'list.select'    => $selected,
            'list.translate' => $translate,
            'option.key'     => 'value',
            'option.text'    => 'text',
            'group.items'    => 'items',
            'group.label'    => 'text',
        ));
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
     * Quantity widget
     * @param int    $default
     * @param array  $options
     * @param string $id
     * @param string $name
     * @return string
     */
    public function quantity($default = 1, $options = array(), $id = null, $name = 'quantity')
    {
        if (!$id) {
            $id = $this->app->jbstring->getId('quantity');
        }

        $options['default'] = (float)$default;

        $html = array(
            '<table cellpadding="0" cellspacing="0" border="0" class="quantity-wrapper jsQuantity" id="' . $id . '">',
            '  <tr>',
            '    <td rowspan="2">',
            '      <div class="jsCountBox item-count-wrapper">',
            '        <div class="item-count">',
            '          <dl class="item-count-digits">' . str_repeat('<dd></dd>', 5) . '</dl>',
            '          <input type="text" class="input-quantity jsInput" maxlength="6" name="' . $name . '" value="' . $options['default'] . '">',
            '        </div>',
            '      </div>',
            '    </td>',
            '    <td class="plus"><span class="jsAdd jbbutton micro">+</span></td>',
            '  </tr>',
            '  <tr>',
            '    <td class="minus"><span class="jsRemove jbbutton micro">-</span></td>',
            '  </tr>',
            '</table>',
        );

        $this->app->jbassets->initQuantity($id, $options);

        return implode("\n", $html);
    }

    /**
     * Render color field
     * @param string $inputType
     * @param array  $data
     * @param string $name
     * @param null   $selected
     * @param array  $input_attr
     * @param array  $label_attr
     * @param array  $div_attr
     * @param array  $titles
     * @return string
     */
    public function colors(
        $inputType = 'checkbox',
        $data,
        $name,
        $selected = null,
        $input_attr = array(),
        $label_attr = array(),
        $div_attr = array(),
        $titles = array()
    )
    {
        $html   = array();
        $unique = $this->app->jbstring->getId('jbcolor-');
        $i      = 0;

        $html[] = '<div id="' . $unique . '" class="jbzoo-colors">';

        foreach ($data as $key => $value) {

            $isFile = false;

            if ($this->app->jbcolor->isFile($value)) {
                $isFile = $value;
            }

            $id = $this->app->jbstring->getId('jbcolor-input-');

            $input_attr = array_merge((array)$input_attr, array(
                'type'    => $inputType,
                'name'    => $name,
                'id'      => $id,
                'title'   => isset($titles[$key]) ? $titles[$key] : $key,
                'value'   => $key,
                'class'   => 'jbcolor-input',
                'checked' => ''
            ));

            $valueSlug = $this->app->string->sluggify(!$isFile ? $key : basename($value));

            $label_attr = array_merge($label_attr, array(
                'for'   => $id,
                'title' => isset($titles[$key]) ? $titles[$key] : $key,
                'class' => 'jbcolor-label hasTip ' . $inputType . ' value-' . $valueSlug
            ));


            $attr = array(
                'style' => ' background-color: ' . (!$isFile ? '#' . $value . ';' : 'transparent;') . (isset($div_attr['style']) ? $div_attr['style'] : null)
            );

            if (is_array($selected)) {
                foreach ($selected as $val) {

                    if ($inputType == 'radio' && $i >= 1) {
                        continue;
                    }

                    if ($key == $val) {
                        $input_attr['checked'] = 'checked';
                        $input_attr['class'] .= ' checked';
                        $i++;
                        break;
                    }
                }
            } else {

                if ((string)$key == (string)$selected) {

                    $input_attr['checked'] = 'checked';
                    $input_attr['class'] .= ' checked';
                }
            }

            $html[] = ' <input ' . $this->_buildAttrs($input_attr) . ' />'
                . '<label ' . $this->_buildAttrs($label_attr) . '>';

            $html[] = ($isFile ? '<div class="checkIn" style="background: url(\'' . $isFile . '\') center; ' .
                (isset($div_attr['style']) ? $div_attr['style'] : null) . '" >' : '');
            $html[] = '<div ' . $this->_buildAttrs($attr) . '></div>';

            $html[] = ($isFile ? '</div>' : '');
            $html[] = '</label>';
        }

        $html[] = '</div>';

        $multiple = $inputType == 'checkbox' ? 1 : 0;
        $this->app->jbassets->initJBColorHelper($unique, $multiple);

        return implode("\n", $html);
    }

    /**
     * @param string $defaultCur
     * @param array  $rates
     * @param array  $options
     * @return array|null|string
     */
    public function currencyToggle($defaultCur = 'eur', $rates = null, $options = array())
    {
        $rates = !empty($rates) ? $rates : $this->app->jbmoney->getData();

        $defaultCur = $this->app->jbvars->lower($defaultCur);
        $moneyVal   = JBCart::val(1, $defaultCur, $rates); // for calculating
        $uniqId     = $this->app->jbstring->getId();

        if (isset($rates['%'])) {
            unset($rates['%']);
        }

        $options = $this->app->data->create(array_merge(array(
            'showDefault' => true,
        ), $options));

        $i     = 0;
        $count = count($rates);
        $html  = array();
        foreach ($rates as $code => $currency) {
            $i++;
            $id    = $this->app->jbstring->getId('unique-');
            $title = JText::_('JBZOO_JBCURRENCY_' . $code);

            if ($code != JBCartValue::DEFAULT_CODE && !$moneyVal->isCur($code)) {
                $title .= '; ' . $moneyVal->text() . ' = ' . $moneyVal->text($code);
            }

            $inputAttrs = array(
                'type'          => 'radio',
                'name'          => 'currency[' . $uniqId . '][]',
                'id'            => $id,
                'data-currency' => $code,
                'class'         => array(
                    'jbcurrency-input',
                    'jbcurrency-' . $code,
                ),
            );

            if ($code == $defaultCur) {
                $inputAttrs['checked'] = 'checked';
            }

            $labelAttrs = array(
                'for'   => $id,
                'title' => $title,
                'class' => array(
                    'jbcurrency-label',
                    'hasTip',
                ),
            );

            if ($i == $count) {
                $inputAttrs['class'][] = 'isLast';
                $labelAttrs['class'][] = 'isLast';
            }

            if ($i == 1) {
                $inputAttrs['class'][] = 'isFirst';
                $labelAttrs['class'][] = 'isFirst';
            }

            $flag = ($code == JBCartValue::DEFAULT_CODE)
                ? '<span class="jbflag jbflag-' . $code . '">&curren;</span>'
                : '<span class="jbflag jbflag-' . $code . '"></span>';

            $html[] = '<input ' . $this->buildAttrs($inputAttrs) . ' />';
            $html[] = '<label  ' . $this->buildAttrs($labelAttrs) . '>' . $flag . '</label>';
        }

        if (!empty($html)) {

            $id = $this->app->jbstring->getId('currency-toggle-');

            $this->app->jbassets->currencyToggle($id, (array)$options);

            $widgetAttrs = array(
                'data-default' => $moneyVal->cur(),
                'id'           => $id,
                'class'        => array(
                    'jsCurrencyToggle',
                    'currency-toggle'
                ),

            );

            $html = '<div ' . $this->buildAttrs($widgetAttrs) . '>' . implode("\n ", $html) . '</div>';

            return $html;
        }

        return null;
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
     * @param null  $value
     * @param null  $attribs
     * @param null  $idtag
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
     * @param array  $params
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
                    $("#' . $idtag . '-value-0").html(JBZoo.numberFormat(ui.values[0], 0, ".", " "));
                    $("#' . $idtag . '-value-1").html(JBZoo.numberFormat(ui.values[1], 0, ".", " "));
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
     * @param null   $attribs
     * @param null   $selected
     * @param bool   $idtag
     * @param bool   $translate
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
     * @param       $data
     * @param       $name
     * @param null  $attribs
     * @param null  $selected
     * @param bool  $idtag
     * @param bool  $translate
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
     * @param array  $selectInfo
     * @param string $name
     * @param array  $selected
     * @param array  $attribs
     * @param bool   $idtag
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

        $this->app->jbassets->initJBCascadeSelect($uniqId, $selectInfo['items']);

        $attribs['class'][] = 'jbcascadeselect';

        return '<div class="jbcascadeselect-wrapper jbcascadeselect-' . $uniqId . '">'
        . '<div ' . $this->app->jbhtml->buildAttrs($attribs) . '>'
        . implode(" ", $html)
        . '</div></div>';
    }

    /**
     * Generates an HTML checkbox/radio list.
     * @param   string  $inputType   Type of html input element
     * @param   array   $data        An array of objects
     * @param   string  $name        The value of the HTML name attribute
     * @param   array   $attribs     Additional HTML attributes for the <select> tag
     * @param   string  $selected    The name of the object variable for the option text
     * @param   boolean $idtag       Value of the field id or null by default
     * @param   boolean $translate   True if options will be translated
     * @param   boolean $isLabelWrap True if options wrappeed label tag
     * @return  string HTML for the select list
     */
    private function _list($inputType, $data, $name, $attribs = array(), $selected = null, $idtag = false,
                           $translate = false, $isLabelWrap = false
    )
    {
        reset($data);

        if (!is_array($attribs)) {
            $attribs = array();
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
                $id    = isset($obj['id']) ? $obj['id'] : null;
            }

            $valueSlug = $this->app->string->sluggify($value);

            $extra = array_merge($attribs, array(
                'value' => $value,
                'name'  => $name,
                'type'  => $inputType,
                'id'    => 'id' . $valueSlug . '-' . $this->app->jbstring->getId(),
                'class' => 'value-' . $valueSlug
            ));

            if (is_array($selected)) {

                foreach ($selected as $val) {

                    if ($value == $val) {
                        $extra['checked'] = 'checked';
                        break;
                    }
                }

            } else {
                $value = JString::trim($value);
                if (isset($value)) {
                    if ((string)$value == (string)$selected) {
                        $extra['checked'] = 'checked';
                    }

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
     * @param array   $attrs
     * @param boolean $clean
     * @return null|string
     */
    public function buildAttrs($attrs, $clean = true)
    {
        return $this->_buildAttrs($attrs, $clean);
    }

    /**
     * Build attrs
     * TODO: Remove method, replace to public
     * @param $attrs
     * @param $clean
     * @return null|string
     */
    protected function _buildAttrs($attrs, $clean = true)
    {
        $result = ' ';

        if (is_string($attrs)) {
            $result .= $attrs;

        } elseif (!empty($attrs)) {
            foreach ($attrs as $key => $param) {

                $param = (array)$param;
                $value = implode(' ', $param);
                if ($clean) {
                    $value = $this->cleanAttrValue($value, true);
                    if (!empty($value) || $value == '0' || $key == 'value') {
                        $result .= ' ' . $key . '="' . $value . '"';
                    }
                } else {
                    $value = $this->cleanAttrValue($value, false);
                    $result .= ' ' . $key . '="' . $value . '"';
                }
            }
        }

        return JString::trim($result);
    }

    /**
     * Clear attribute value
     * @param string $value
     * @param bool   $isTrim
     * @return string
     */
    public function cleanAttrValue($value, $isTrim = true)
    {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        if ($isTrim) {
            $value = JString::trim($value);
        }

        return $value;
    }

}
