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
            'wrapper' : {},
            'range'   : {}
        }, {

            init: function () {
                this._prepareOptions();

                this.wrapper = this.$('.jsSliderWrapper');
                this.range   = this.$('.jsSliderValue');

                this.ui();
            },


            ui: function () {
                var options = this.options.ui,
                    $this   = this;

                this.wrapper.slider({
                    'range' : options.range,
                    'min'   : options.min,
                    'max'   : options.max,
                    'step'  : options.step,
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
                var range,
                    _values = this.wrapper.slider('values');

                value = JBZoo.toFloat(value);
                if (index == 0) {
                    value = this._validateMin(value);

                    _values[0] = value;
                    range = value + "/" + _values[1];

                } else {
                    value = this._validateMax(value);

                    _values[1] = value;
                    range = _values[0] + "/" + value;
                }
                this._getMoney(index).JBZooMoney('setValue', [value]);

                this.wrapper.slider('values', _values);
                this.$('.jsSlider-' + index).val(value);

                this.range.val(range);
            },

            _getSliderValue: function(index) {
                return this.wrapper.slider('values', index);
            },

            _getMoney: function (index) {
                return this.$('.jsSliderLabel-' + index + ' .jsMoney').JBZooMoney({
                    'rates': JBZoo.getVar('currencyList')
                });
            },

            /**
             * Cleanup option list
             * @private
             */
            _prepareOptions: function () {
                var $this = this;

                $.extend($this.options.ui, {
                    'step'    : JBZoo.toFloat($this.options.ui.step),
                    'min'     : JBZoo.toFloat($this.options.ui.min),
                    'max'     : JBZoo.toFloat($this.options.ui.max)
                });
            },

            _validate: function (values) {
                var $this = this;
                values = $.map(values, function (value, i) {

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
                var max     = JBZoo.toFloat(this._getSliderValue(1)),
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
                var min     = JBZoo.toFloat(this._getSliderValue(0)),
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
                $this._setValue($(this).val(), $(this).hasClass('jsSlider-0') ? 0 : 1);
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