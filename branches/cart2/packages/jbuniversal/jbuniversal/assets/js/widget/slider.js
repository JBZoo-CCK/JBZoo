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
            "UI"    : {},
            'range' : {},
            'inputs': [],

            init: function () {
                this._cleanupOptions();

                this.UI = this.$('.jsUI');
                this.range = this.$('.jsValue');
                this.inputs = this._initMoney();

                this._initUI();
            },

            _initUI: function () {
                var $this = this;

                this.UI.slider($.extend({}, $this.options, {
                    'range' : true,
                    'slide' : function (event, ui) {
                        $this.setValues(ui.values);
                    },
                    'stop'  : function (event, ui) {
                        $this.setValues(ui.values);
                    },
                    'change': function (event, ui) {

                        $this._delay(function () { // hack, multiple calls
                            $this._trigger('change', [ui.values])
                        }, 200, 'sliderChange');
                    }
                }));
            },

            _initMoney: function () {
                var $this = this;

                this.$('.jsMoney').JBZooMoney({
                    onBeforeUpdate: function (newValue) {
                        return this.el.is('.jsInput-min') ? $this._validateMin(newValue) : $this._validateMax(newValue);
                    },
                    onAfterUpdate : function (currentValue) {
                        return this.el.is('.jsInput-min') ? $this._setValue(currentValue, 0, false) : $this._setValue(currentValue, 1, false);
                    }
                });

                return [this.$('.jsInput-min'), this.$('.jsInput-max')]
            },

            setValues: function (values) {
                this._setValue(values[0], 0);
                this._setValue(values[1], 1);
            },

            reset: function () {
                this.setValues([
                    this.options.min,
                    this.options.max
                ]);
            },

            _setValue: function (value, index, updateMoney) {
                var sliderValues = this.UI.slider('values');

                sliderValues[index] = (index == 0) ? this._validateMin(value) : this._validateMax(value);

                if (this._def(updateMoney, true)) {
                    this.inputs[index].JBZooMoney('setInputValue', [value]);
                }

                this.UI.slider('values', sliderValues);
                this.range.val(sliderValues[0] + "/" + sliderValues[1]);
            },

            _getSliderValue: function (index) {
                return this.UI.slider('values', index);
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

            _changeInput: function ($input, direction) {
                var oldValue = JBZoo.toFloat($input.val());

                if (direction < 0) {
                    newValue = this._validateMin(oldValue - this.options.step);
                } else {
                    newValue = this._validateMax(oldValue + this.options.step);
                }

                if (oldValue != newValue) {
                    this._setValue(newValue, $input.is('.jsInput-min') ? 0 : 1);
                }
            },

            _increment: function ($input) {
                this._changeInput($input, 1);
            },

            _decrement: function ($input) {
                this._changeInput($input, -1);
            },

            'keydown .jsInput': function (e, $this) {

                if ($this._key(e, 'arrow-top')) {
                    $this._increment($(this), 1);
                    return false;
                }

                if ($this._key(e, 'arrow-down')) {
                    $this._decrement($(this), -1);
                    return false;
                }
            },

            'mouseenter .jsInput': function (e, $this) {
                $(this).select();
            },

            'mousewheel .jsInput': function (e, $this) {
                var $input = $(this);
                if ($input.is(':focus')) {
                    $this._changeInput($input, e.originalEvent.wheelDelta);
                }

                return false;
            }
        }
    );

})(jQuery, window, document);