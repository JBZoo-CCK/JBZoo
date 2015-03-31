/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.Slider', {
            ui        : {
                'range' : true,
                'min'   : 1,
                'max'   : 10000,
                'step'  : 100,
                'values': []
            },
            'currency': '',
            'wrapper'  : {}
        }, {

            init: function () {
                this.wrapper    = this.$('.jsSliderWrapper');
                this.rangeInput = this.$('.jsSliderValue');

                this.ui();
            },

            ui: function () {
                var options = this.options.ui,
                    $this   = this;

                this.wrapper.slider({
                    'range' : options.range,
                    'min'   : JBZoo.toFloat(options.min),
                    'max'   : JBZoo.toFloat(options.max),
                    'step'  : JBZoo.toFloat(options.step),
                    'values': [options.values[0], options.values[1]],
                    'slide' : function (event, ui) {
                        $this.setValues(ui.values);
                    },
                    'stop'  : function (event, ui) {
                        $this.setValues(ui.values);
                    }
                });
            },

            setValues: function (values) {
                var $this = this;
                $.map(this._validate(values), function (value, i) {
                    $this._setValue(value, i);
                });
            },

            _setValue: function(value, index) {
                var range;

                if (index == 0) {
                    value = this._validateMin(value);
                    range = value + "/" + this._getSliderValue[1];

                } else {
                    value = this._validateMax(value);
                    range = this._getSliderValue[0] + "/" + value;
                }
                this._getMoney(index).JBZooMoney('setValue', [value]);

                this.wrapper.slider('values', index, value);
                this.rangeInput.val(range);

                this.$('.jsSlider-' + index).val(value);
            },

            _getSliderValue: function(index) {
                return this.wrapper.slider('values', index);
            },

            _getMoney: function (index) {
                return this.$('.jsSliderLabel-' + index + ' .jsMoney').JBZooMoney({
                    'rates': JBZoo.getVar('currencyList')
                });
            },

            _validate: function (values) {
                var $this = this;
                values = $.map(values, function (value, i) {

                    value = JBZoo.toFloat(value);
                    if (i == 0) {
                        value = $this._validateMin(value);

                    } else {
                        value = $this._validateMax(value);
                    }

                    return value;
                });

                return values;
            },

            _validateMin: function(min) {
                var max     = this._getSliderValue(1),
                    options = this.options.ui;

                min = JBZoo.toFloat(min);
                if (min > max) {
                    min = max;
                }
                if (min < options.min) {
                    min = options.min;
                }
                if (min > options.max) {
                    min = options.max;
                }

                return min;
            },

            _validateMax: function(max) {
                var min     = this._getSliderValue(0),
                    options = this.options.ui;

                max = JBZoo.toFloat(max);
                if (max < min) {
                    max = min;
                }
                if (max < options.min) {
                    max = options.min;
                }
                if (max > options.max) {
                    max = options.max;
                }

                return max;
            },

            'change .jsSliderInput': function (e, $this) {
                var _index = $(this).hasClass('jsSlider-0') ? 0 : 1;
                $this._setValue($(this).val(), _index);
            },

            'focus .jsSliderInput': function (e, $this) {
                $(this).css('opacity', '1');
                $(this).closest('.jsSliderLabel').css('opacity', '0');
            },

            'blur .jsSliderInput': function (e, $this) {
                $(this).css('opacity', '0');
                $(this).closest('.jsSliderLabel').css('opacity', '1');
            },

            'mouseenter .jsSliderBox': function (e, $this) {
                $('.jsSliderInput', $(this)).focus();
            },

            'mouseleave .jsSliderBox': function (e, $this) {
                $('.jsSliderInput', $(this)).blur();
            },

            'mousewheel .jsSliderInput': function (e, $this) {

                var $input = $(this);
                if ($input.is(':focus')) {

                    var value = JBZoo.toFloat($input.val());
                    if (e.originalEvent.wheelDelta > 0) {
                        value += $this.options.ui.step;
                    } else {
                        value -= $this.options.ui.step;
                    }

                    $this._setValue(value, $input.hasClass('jsSlider-0') ? 0 : 1);
                }

                return false;
            }
        }
    );

})(jQuery, window, document);