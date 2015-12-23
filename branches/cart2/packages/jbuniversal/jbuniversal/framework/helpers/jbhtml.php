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
 * Class JBHtmlHelper
 */
class JBHtmlHelper extends AppHelper
{
    /**
     * @type JBVarsHelper
     */
    protected $_vars;

    /**
     * @type JString|StringHelper
     */
    protected $_string;

    /**
     * @type JBStringHelper
     */
    protected $_jbstring;

    /**
     * @type JBAssetsHelper
     */
    protected $_assets;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_vars     = $this->app->jbvars;
        $this->_string   = $this->app->string;
        $this->_jbstring = $this->app->jbstring;
        $this->_assets   = $this->app->jbassets;
    }

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
     * @param string $type
     * @param string $name
     * @param array  $attribs
     * @param int    $selected
     * @param bool   $idtag
     * @param bool   $isLabelWrap
     * @return null|string
     */
    public function bool(
        $type,
        $name,
        $attribs = array(),
        $selected = 0,
        $idtag = false,
        $isLabelWrap = true
    )
    {
        $type         = $this->app->jbvars->lower($type);
        $dataRadio    = array(0 => JText::_('JBZOO_NO'), 1 => JText::_('JBZOO_YES'));
        $dataCheckbox = array(1 => JText::_('JBZOO_YES'));

        if ($type == 'radio') {
            return $this->_list('radio', $dataRadio, $name, $attribs, $selected, $idtag, $isLabelWrap);

        } else if ($type == 'checkbox') {
            return $this->_list('checkbox', $dataCheckbox, $name, $attribs, $selected, $idtag, $isLabelWrap);

        } elseif ($type == 'radio-ui') {
            return $this->buttonsJqueryUI($dataRadio, $name, $attribs, $selected, $idtag, $isLabelWrap);

        } else if ($type == 'checkbox-ui') {
            $attribs['multiple'] = 'multiple';
            return $this->buttonsJqueryUI($dataRadio, $name, $attribs, $selected, $idtag, $isLabelWrap);
        }
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
            if (is_array($attribs)) {
                $attribs['id'] = $idtag;
            } else {
                $attribs .= ' id="' . $idtag . '"';
            }
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
            $this->_assets->jqueryui();
            $this->_assets->initAutocomplete();
        }

        return $this->app->html->_('control.text', $name, $value, $attribs);
    }

    /**
     * Render textarea field
     * @param      $name
     * @param null $value
     * @param null $attribs
     * @param null $idtag
     * @return string
     */
    public function textarea($name, $value = null, $attribs = null, $idtag = null)
    {
        if ($idtag && is_array($attribs)) {
            $attribs['id'] = $idtag;
        }

        $attribs = $this->_buildAttrs($attribs);

        return $this->app->html->_('control.textarea', $name, $value, $attribs);
    }

    /**
     * Quantity widget
     * @param int    $default
     * @param array  $options
     * @param string $id
     * @param string $name
     * @param bool   $return
     * @return string
     */
    public function quantity($default = 1, $options = array(), $id = null, $name = 'quantity', $return = false)
    {
        $id = $id ? $id : $this->_jbstring->getId('quantity-');

        $params = $this->app->data->create($options);

        $step = $this->_vars->number($params->get('step', 1));
        $step = $step > 0 ? $step : 1;

        $decimals = $this->_vars->number($params->get('decimals', 0));
        $decimals = $decimals >= 0 ? $decimals : 0;

        $default = $this->_vars->number($default);

        $params = array(
            'min'      => $this->_vars->number($params->get('min', 1)),
            'max'      => $this->_vars->number($params->get('max', 999999)),
            'step'     => $step,
            'default'  => $default,
            'decimals' => $decimals,
        );

        $html = array(
            '<table cellpadding="0" cellspacing="0" border="0" class="quantity-wrapper jsQuantity" id="' . $id . '">',
            '  <tr>',
            '    <td rowspan="2">',
            '      <div class="jsCountBox item-count-wrapper">',
            '        <div class="item-count">',
            '          <dl class="item-count-digits">' . str_repeat('<dd></dd>', 5) . '</dl>',
            '          <input type="text" class="input-quantity jsInput" maxlength="6" name="' . $name . '" value="' . $default . '">',
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

        $html[] = $this->_assets->initQuantity($id, $params, $return);

        return implode(PHP_EOL, $html);
    }

    /**
     * Render color field
     * @param string $inputType
     * @param array  $data
     * @param string $name
     * @param null   $selected
     * @param array  $attrs
     * @param string $width
     * @param string $height
     * @param array  $titles
     * @return string
     */
    public function colors(
        $inputType = 'checkbox',
        $data,
        $name,
        $selected = null,
        $attrs = array(),
        $width = '26px',
        $height = '26px',
        $titles = array()
    )
    {
        $stringHelper = $this->_string;
        $jbstring     = $this->_jbstring;
        $jbcolor      = $this->app->jbcolor;

        $unique = $jbstring->getId('jbcolor-');

        $attrs['id']    = $unique;
        $attrs['class'] = 'jbzoo-colors';

        $html   = array();
        $html[] = '<div ' . $this->_buildAttrs($attrs) . '>';
        foreach ($data as $value => $color) {
            $isFile = false;
            if ($jbcolor->isFile($color)) {
                $isFile = $color;
            }

            $inputId   = $jbstring->getId('jbcolor-input-');
            $inputAttr = array(
                'type'  => $inputType,
                'name'  => $name,
                'id'    => $inputId,
                'title' => isset($titles[$value]) ? $titles[$value] : $value,
                'value' => $value,
                'class' => 'jbcolor-input',
            );

            $labelAttr = array(
                'for'   => $inputId,
                'title' => isset($titles[$value]) ? $titles[$value] : $value,
                'class' => array(
                    $inputType,
                    'jbcolor-label',
                    'value-' . $stringHelper->sluggify($value),
                    'hasTip',
                ),
                'style' => 'width:' . $width . ';height:' . $height . ';',
            );

            $attr = array(
                'style' => ' background-color: ' . (!$isFile ? '#' . $color . ';' : 'transparent; width:' . $width . ';height:' . $height . ';'),
            );

            if ($selected != null && ($selected == $value || is_array($selected) && in_array($value, $selected))) {
                $inputAttr['checked'] = 'checked';
                $inputAttr['class'] .= ' checked';
            }

            $html[] = '<input ' . $this->_buildAttrs($inputAttr) . '/>';
            $html[] = '<label ' . $this->_buildAttrs($labelAttr) . '>';
            $html[] = ($isFile ? '<div class="checkIn" style="background: url(\'' . $isFile . '\') center;">' : '');
            $html[] = '<div ' . $this->_buildAttrs($attr) . '></div>';
            $html[] = ($isFile ? '</div>' : '') . '</label>';
        }

        $html[] = '</div>';
        $html[] = $this->_assets->initJBColorHelper($unique, $inputType == 'checkbox' ? 1 : 0, true);

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string $defaultCur
     * @param array  $rates
     * @param array  $options
     * @param bool   $return
     * @return array|null|string
     */
    public function currencyToggle($defaultCur = 'eur', $rates = null, $options = array(), $return = false)
    {
        $rates = !empty($rates) ? $rates : $this->app->jbmoney->getData();

        $defaultCur    = $this->_vars->lower($defaultCur);
        $uniqId        = $this->_jbstring->getId();
        $systemDefault = JBModelConfig::model()->getCurrency();

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
            $id    = $this->_jbstring->getId('unique-');
            $title = JText::_('JBZOO_JBCURRENCY_' . $code);

            $moneyVal = JBCart::val('1000 ' . $code, $rates); // for calculating

            if ($systemDefault != JBCartValue::DEFAULT_CODE && !$moneyVal->isCur($systemDefault)) {
                $title .= '; ' . $moneyVal->text() . ' &asymp; ' . $moneyVal->text($systemDefault);
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
                    'jbtooltip',
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

            $id = $this->_jbstring->getId('currency-toggle-');

            $this->_assets->initTooltip();
            $html[] = $this->_assets->currencyToggle($id, (array)$options, $return);

            $widgetAttrs = array(
                'data-default' => $moneyVal->cur(),
                'id'           => $id,
                'class'        => array(
                    'jsCurrencyToggle',
                    'currency-toggle',
                ),

            );

            $html = '<div ' . $this->buildAttrs($widgetAttrs) . '>' . implode(PHP_EOL, $html) . '</div>';

            return $html;
        }

        return null;
    }

    /**
     * @param $data
     * @return string
     */
    public function dataList($data)
    {
        $html = array();
        $data = array_filter($data);

        if (!empty($data)) {

            $html[] = '<dl class="uk-description-list-horizontal param-list">';

            foreach ($data as $label => $text) {
                $label  = JText::_($label);
                $html[] = '<dt title="' . $this->cleanAttrValue($label) . '">' . $label . '</dt>';
                $html[] = '<dd>' . $text . '</dd>';
            }

            $html[] = '</dl>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Render hidden field
     * @param       $name
     * @param null  $value
     * @param array $attribs
     * @param null  $idtag
     * @return string
     */
    public function hidden($name, $value = null, $attribs = array(), $idtag = null)
    {
        if ($idtag) {
            $attribs['id'] = $idtag;
        }

        $attribs['type']  = 'hidden';
        $attribs['name']  = $name;
        $attribs['value'] = $value;

        return '<input ' . $this->_buildAttrs($attribs) . ' />';
    }

    /**
     * @param array $fields
     * @return string
     */
    public function hiddens(array $fields)
    {
        $html = array();

        foreach ($fields as $name => $data) {
            if (is_array($data)) {

                $value = '';
                if (isset($data['value'])) {
                    $value = $data['value'];
                    unset($data['value']);
                }

                $html[] = $this->hidden($name, $value, $data);

            } else if ($name == '_token' && $data == '_token') {
                $html[] = $this->app->html->_('form.token');

            } else {
                $html[] = $this->hidden($name, $data);
            }
        }

        return implode(PHP_EOL, $html);
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

        $this->_assets->jqueryui();
        $this->_assets->addScript('$("#' . $idtag . '").datepicker(' . $this->_assets->toJSON($params) . ')');

        return $this->text($name, $value, $attribs, $idtag);
    }

    /**
     * Render jQueryUI slider
     * @param array        $params
     * @param string|array $value
     * @param string       $name
     * @param string       $idTag
     * @param string       $currency
     * @return string
     */
    public function sliderInput($params, $value = '', $name = '', $idTag = '', $currency = '')
    {
        $value = ($value !== '' && is_string($value) ? explode('/', $value) : array($params['min'], $params['max']));

        // prepare vars
        $idTag          = ($idTag !== '' && $idTag !== null ? $idTag : $this->_jbstring->getId('jsSlider-'));
        $params['min']  = $this->_vars->number($params['min']);
        $params['max']  = $this->_vars->number($params['max']);
        $params['step'] = $this->_vars->number($params['step']);

        $paramMin = $this->_vars->money($params['min'] ?: 0, 2);
        $paramMax = $this->_vars->money($params['max'] ?: 10000, 2);

        $valueMin = JBCart::val($value[0], $currency)->setFormat(array('round_type' => 'floor', 'round_value' => '0'), $currency);
        $valueMax = JBCart::val($value[1], $currency)->setFormat(array('round_type' => 'ceil', 'round_value' => '0'), $currency);

        $html   = array();
        $html[] = '<div class="jbslider-ui jsUI"></div>';

        // min box
        $html[] = '<div class="jbslider-input-box">';
        $html[] = $valueMin->htmlInput($currency, array('class' => 'jsInput jsNoSubmit jsInput-min jbslider-input jbslider-input-min'));
        $html[] = '</div>';

        // max box
        $html[] = '<div class="jbslider-input-box">';
        $html[] = $valueMax->htmlInput($currency, array('class' => 'jsInput jsNoSubmit jsInput-max jbslider-input jbslider-input-max'));
        $html[] = '</div>';

        $html[] = $this->hidden($name, $valueMin->val() . '/' . $valueMax->val(), array('class' => 'jsValue'));

        $html[] = $this->_assets->slider($idTag, array(
            'min'    => $paramMin,
            'max'    => $paramMax,
            'step'   => $params['step'],
            'values' => array($valueMin->val(), $valueMax->val()),
        ), true);

        $html[] = JBZOO_CLR;

        return '<div id="' . $idTag . '" class="jbslider jsSlider jsNoCurrencyToggle">' . implode(PHP_EOL, $html) . '</div>';
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

        $params['min']  = floor($this->_vars->number($params['min'], 2));
        $params['max']  = ceil($this->_vars->number($params['max'], 2));
        $params['step'] = $this->_vars->number($params['step']);

        $valueMin = floor($this->_vars->money($value['0'], 2));
        $valueMax = ceil($this->_vars->money($value['1'], 2));

        $this->_assets->jqueryui();
        $this->_assets->less('jbassets:less/widget/slider.less');
        $this->_assets->addScript('
        $("#' . $idtag . '-wrapper").removeAttr("slide");
            $("#' . $idtag . '-wrapper")[0].slide = null;
            $("#' . $idtag . '-wrapper").slider({
                "range" : true,
                "min"   : ' . ($params['min'] ? $params['min'] : 0) . ',
                "max"   : ' . ($params['max'] ? $params['max'] : 10000) . ',
                "step"  : ' . ($params['step'] ? round($params['step'], 2) : 100) . ',
                "values": [' . $valueMin . ', ' . $valueMax . '],
                "slide" : function(event,ui) {
                    $("#' . $idtag . '-value").val(ui.values[0] + "/" + ui.values[1]);
                    $("#' . $idtag . '-value-0").html(JBZoo.numberFormat(ui.values[0], 0, ".", " "));
                    $("#' . $idtag . '-value-1").html(JBZoo.numberFormat(ui.values[1], 0, ".", " "));
                    $("#' . $idtag . '-wrapper").closest(".jsSlider").trigger("change");
                }
            });
            $("#' . $idtag . '-value").val("' . $valueMin . '/' . $valueMax . '");
        ');

        $html = array(
            '<div class="jsSlider jbslider">',
            '<div id="' . $idtag . '-wrapper"> </div>',
            '<span id="' . $idtag . '-value-0" class="slider-value-0">' . number_format($valueMin, 0, '.', ' ') . '</span>',
            '<span id="' . $idtag . '-value-1" class="slider-value-1">' . number_format($valueMax, 0, '.', ' ') . '</span>',
            '<input type="hidden" id="' . $idtag . '-value" name="' . $name . '" />',
            JBZOO_CLR,
            '</div>',
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * Render option list
     * @param        $data
     * @param        $name
     * @param null   $attribs
     * @param null   $selected
     * @param bool   $idtag
     * @param bool   $translate
     * @param bool   $return
     * @return string
     */
    public function buttonsJqueryUI(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false,
        $return = false
    )
    {
        $html = array();

        if (isset($attribs['multiple'])) {
            $html[] = $this->checkbox($data, $name, $attribs, $selected, $idtag, $translate, false);
        } else {
            $html[] = $this->radio($data, $name, $attribs, $selected, $idtag, $translate, false);
        }

        $idtag = $this->_jbstring->getId('jbuttonset-');

        $this->_assets->jqueryui();
        $html[] = $this->_assets->widget('#' . $idtag, 'buttonset', array(), $return);

        return '<div id="' . $idtag . '">' . implode(PHP_EOL, $html) . JBZOO_CLR . '</div>';
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
     * @param bool  $return
     * @return string
     */
    public function selectChosen(
        $data,
        $name,
        $attribs = null,
        $selected = null,
        $idtag = false,
        $translate = false,
        $params = array(),
        $return = true
    )
    {
        $this->_assets->chosen();
        if (empty($idtag)) {
            $idtag = $this->_jbstring->getId('chosen-');
        }

        $attribs['data-no_results_text'] = JText::_('JBZOO_CHOSEN_NORESULT');
        $attribs['data-placeholder']     = (isset($params['placeholder'])) ? $params['placeholder'] : JText::_('JBZOO_CHOSEN_SELECT');

        $html   = array();
        $html[] = $this->select($data, $name, $attribs, $selected, $idtag, $translate);
        $html[] = $this->_assets->widget('#' . $idtag, 'chosen', array(), $return);

        return implode(PHP_EOL, $html);
    }

    /**
     * Select cascade
     * @param mixed  $selectInfo
     * @param string $name
     * @param array  $selected
     * @param array  $attribs
     * @param string $group
     * @return string
     */
    public function selectCascade(
        $selectInfo,
        $name,
        $selected = array(),
        $attribs = array(),
        $group = ''
    )
    {
        if (is_string($selectInfo['items']) && is_string($selectInfo['names'])) {
            $selectInfo = $this->app->jbselectcascade->getItemList($selectInfo['names'], $selectInfo['items']);
        }

        $itemList = $selectInfo['items'];
        $lvlCheck = $curLvl = 0;
        $allText  = ' - ' . JText::_('JBZOO_ALL') . ' - ';

        $html = array();
        for ($i = 0; $i <= $selectInfo['maxLevel']; $i++) {
            $listValue = isset($selected[$i]) ? $selected[$i] : null;
            $listLabel = isset($selectInfo['names'][$i]) ? $selectInfo['names'][$i] : ' ';
            $listName  = sprintf($name, $i);
            $listId    = $this->_jbstring->getId('jbselect');

            $listAttrs = array(
                'data-rowindex' => $i,
                'class'         => array(
                    'jsSelect',
                    'jsSelect-' . $i,
                ),
            );

            // create option list
            $options = array();
            if ($lvlCheck == $curLvl) {
                $lvlCheck++;
                $keys = array_keys($itemList);
                if (!empty($keys)) {
                    $options = array_combine($keys, $keys);
                } else {
                    $options = array();
                }
            }
            $options = $this->app->jbarray->unshiftAssoc($options, '', $allText);

            $html[] = '<div class="jbcascade-row">';
            $html[] = '<label class="jbcascade-label" for="' . $listId . '">' . $listLabel . '</label>';
            $html[] = $this->select($options, $listName, $listAttrs, $listValue, $listId);
            $html[] = '</div>';

            if (isset($itemList[$listValue])) {
                $itemList = $itemList[$listValue];
                $curLvl++;
            }
        }

        $attribs = array_merge($attribs, array(
            'id'    => $this->_jbstring->getId('jbcascade'),
            'class' => array(
                'jbcascade',
                'jsCascade',
            ),
        ));

        $widgetSelector = '#' . $attribs['id'];
        if ($group) {
            $groupClass         = 'jsCascade-' . $group;
            $widgetSelector     = '.' . $groupClass;
            $attribs['class'][] = $groupClass;
        }

        // init widget
        $this->_assets->selectCascade();
        $html[] = $this->_assets->widget($widgetSelector, 'JBZoo.CascadeSelect', array(
            'text_all' => $allText,
            'group'    => $widgetSelector,
            'items'    => $selectInfo['items'],
        ), true);

        return '<div class="jbzoo"><div ' . $this->buildAttrs($attribs) . '>' . implode(PHP_EOL, $html) . '</div></div>';
    }

    /**
     * Generates an HTML checkbox/radio list.
     * @param   string  $inputType   Type of html input element
     * @param   array   $data        An array of objects
     * @param   string  $name        The value of the HTML name attribute
     * @param   array   $attribs     Additional HTML attributes for the <select> tag
     * @param   string  $selected    The name of the object variable for the option text
     * @param   boolean $idtag       Value of the field id or null by default TODO kill me
     * @param   boolean $translate   True if options will be translated
     * @param   boolean $isLabelWrap True if options wrappeed label tag
     * @return  string HTML for the select list
     */
    private function _list(
        $inputType,
        $data,
        $name,
        $attribs = array(),
        $selected = null,
        $idtag = false,
        $translate = false,
        $isLabelWrap = false
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
            } else {
                $value = $keyObj;
                $text  = $translate ? JText::_($obj) : $obj;
            }

            $valueSlug = $this->_string->sluggify($value);
            $idTag     = $this->_jbstring->getId('id-' . $valueSlug);

            $extra = array_merge(array(
                'value' => $value,
                'name'  => $name,
                'type'  => $inputType,
                'class' => 'value-' . $valueSlug,
            ), $attribs, array('id' => $idTag,));

            if (is_array($selected)) {
                foreach ($selected as $val) {
                    if ($value == $val) {
                        $extra['checked'] = 'checked';
                        break;
                    }
                }

            } else {
                $value = JString::trim($value);
                if ($value == $selected) {
                    $extra['checked'] = 'checked';
                }
            }

            $extraLabel = array(
                'for'   => $idTag,
                'class' => array(
                    $inputType . '-lbl',
                    'lbl-' . $valueSlug,
                ),
            );

            if ($isLabelWrap) {
                $html[] = '<label ' . $this->_buildAttrs($extraLabel) . '>'
                    . ' <input ' . $this->_buildAttrs($extra) . ' /> ' . $text . '</label>';

            } else {
                $html[] = ' <input ' . $this->_buildAttrs($extra) . ' />'
                    . '<label ' . $this->_buildAttrs($extraLabel) . '> ' . $text . '</label>';

            }

        }

        return implode(PHP_EOL, $html);
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
            $value = trim($value);
        }

        return $value;
    }

}
