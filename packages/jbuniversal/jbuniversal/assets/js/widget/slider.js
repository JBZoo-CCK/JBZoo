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

    JBZoo.widget('JBZoo.Slider',
        {
            'min'   : 0,
            'max'   : 10000,
            'step'  : 100,
            'values': [0, 10000]
        },
        {
            'wrapper': {},
            'range'  : {},

            init: function () {
                this.wrapper = this.$('.jsUI');
                this.range = this.$('.jsValue');

                this._cleanupOptions();
                this._initUI();
                this._initMoney();
            },

            _initUI: function () {
                var $this = this;

                this.wrapper.slider($.extend({}, $this.options, {
                    'range': true,
                    'slide': function (event, ui) {
                        $this.setValues(ui.values);
                    },
                    'stop' : function (event, ui) {
                        $this.setValues(ui.values);
                    }
                }));
            },

            _initMoney: function () {
                return this.$('.jsMoney').JBZooMoney();
            },

            setValues: function (values) {
                var $this = this;
                $.map(this._validate(values), function (value, i) {
                    $this._setValue(value, i);
                });
            },

            _setValue: function (value, index) {
                var sliderValues = this.wrapper.slider('values');

                if (index == 0) {
                    sliderValues[0] = this._validateMin(value);
                } else {
                    sliderValues[1] = this._validateMax(value);
                }

                this.$('.jsInput-' + index).JBZooMoney('setInputValue', [value]);
                this.wrapper.slider('values', sliderValues);
                this.range.val(sliderValues[0] + "/" + sliderValues[1]);
            },

            _getSliderValue: function (index) {
                return this.wrapper.slider('values', index);
            },

            /**
             * Cleanup option list
             */
            _cleanupOptions: function () {
                var $this = this;

                $this.options = $.extend(true, {}, $this.options, {
                    'step'  : JBZoo.toFloat($this.options.step),
                    'min'   : JBZoo.toFloat($this.options.min),
                    'max'   : JBZoo.toFloat($this.options.max),
                    'values': [
                        JBZoo.toFloat($this.options.values[0]),
                        JBZoo.toFloat($this.options.values[1])
                    ]
                });
            },

            /**
             * Validate all values
             * @param values
             * @returns {*}
             * @private
             */
            _validate: function (values) {
                values[0] = this._validateMin(values[0]);
                values[1] = this._validateMax(values[1]);
                return values;
            },

            _validateMin: function (min) {
                var max = JBZoo.toFloat(this._getSliderValue(1)),
                    opt = this.options;

                min = JBZoo.toFloat(min);
                min = (min > max) ? max : min;
                min = (min < opt.min) ? opt.min : min;
                min = (min > opt.max) ? opt.max : min;

                return min;
            },

            _validateMax: function (max) {
                var min = JBZoo.toFloat(this._getSliderValue(0)),
                    opt = this.options;

                max = JBZoo.toFloat(max);
                max = (max < min) ? min : max;
                max = (max < opt.min) ? opt.min : max;
                max = (max > opt.max) ? opt.max : max;

                return max;
            },


            'change .jsInput-0': function (e, $this) {
                //$(this).val($this._validateMin($(this).val()));return false; // TODO fix format an events order
            },

            'change .jsInput-1': function (e, $this) {
                //$(this).val($this._validateMax($(this).val()));return false; // TODO fix format an events order
            },

            'mouseenter .jsInput': function (e, $this) {
                $(this).select();
            },

            'mouseleave .jsInput': function (e, $this) {
                $(this).blur();
            },

            'mousewheel .jsInput': function (e, $this) {

                var $input = $(this);

                if ($input.is(':focus')) {

                    var oldValue = JBZoo.toFloat($input.val());
                    if (e.originalEvent.wheelDelta > 0) {
                        var newValue = $this._validateMax(oldValue + $this.options.step);
                    } else {
                        var newValue = $this._validateMin(oldValue - $this.options.step);
                    }

                    if (oldValue != newValue) {
                        $this._setValue(newValue, $input.is('.jsInput-0') ? 0 : 1);
                        $input.trigger('change');
                    }
                }

                return false;
            }
        }
    );

})(jQuery, window, document);