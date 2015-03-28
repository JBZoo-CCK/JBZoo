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
            ui: {
                'range' : true,
                'min'   : 1,
                'max'   : 10000,
                'step'  : 100,
                'values': []
            },
            'currency'  : ''
        }, {

            init: function ($this) {

                this.ui();
                this._getMoney('.jsSliderLabel ');
            },

            ui: function () {

                var options  = this.options.ui,
                    $this    = this;

                this.$('.jsSliderWrapper').slider({
                    'range' : options.range,
                    'min'   : options.min,
                    'max'   : options.max,
                    'step'  : options.step,
                    'values': [options.values[0], options.values[1]],
                    'slide' : function (event, ui) {
                        $this.setHidden(ui.values);
                        $this.setLabelValue(ui.values);
                    },
                    'stop': function(event, ui) {
                        $this.setHidden(ui.values);
                        $this.setLabelValue(ui.values);
                    }
                });
            },

            'change .jsSliderInput': function(e, $this) {

                var values = $this.$('.jsSliderWrapper').slider('values'),
                    _index = $(this).hasClass('jsSlider-0') ? 0 : 1;

                values[_index] = $(this).val();
                values = $this._validate(values);

                $this.setLabelValue(values);
                $this.setHidden(values);
                $this.$('.jsSliderWrapper').slider('values', values);
            },

            setHidden: function(values) {
                this.$('.jsSliderValue').val(values[0] + "/" + values[1]);
                this.$('.jsSlider-0').val(values[0]);
                this.$('.jsSlider-1').val(values[1]);
            },

            getValue: function () {
                this.$('.jsSliderValue').val();
            },

            setLabelValue: function(values) {
                this._getMoney('.jsSliderLabel-0 ').JBZooMoney('setValue', [values[0]]);
                this._getMoney('.jsSliderLabel-1 ').JBZooMoney('setValue', [values[1]]);
            },

            hideInputs: function() {
                this.$('.jsSliderInput').hide();
            },

            showInputs: function() {
                this.$('.jsSliderInput').show();
            },

            'mouseenter .jsSliderBox': function (e, $this) {
                $('.jsSliderInput', $(this)).focus();
            },

            'mouseleave .jsSliderBox': function (e, $this) {
                $('.jsSliderInput', $(this)).blur();
            },

            'focus .jsSliderInput': function (e, $this) {

                $(this).css('opacity', '1');
                $(this).closest('.jsSliderLabel').css('opacity', '0');
            },

            'blur .jsSliderInput': function (e, $this) {
                $(this).css('opacity', '0');
                $(this).closest('.jsSliderLabel').css('opacity', '1');
            },

            'mousewheel .jsSliderInput': function (e, $this) {

                var $input = $(this);
                if ($input.is(':focus')) {
                    var value = $input.val();
                    if (e.originalEvent.wheelDelta > 0) {
                        value += $this.ui.options.step;
                    } else {
                        value -= $this.ui.options.step;
                    }

                    $this._setValue(value);
                }

                return false;
            },

            _setValue: function (value) {
                this.$('.jsSliderValue').val(value);
            },

            _getMoney: function (filter) {

                return this.$(filter + '.jsMoney').JBZooMoney({
                    'rates': JBZoo.getVar('currencyList')
                });
            },

            _validate: function (values) {
                var options = this.options.ui;

                values[0] = JBZoo.toFloat(values[0]);
                values[1] = JBZoo.toFloat(values[1]);

                if(values[0] > values[1]) {
                    values[0] = values[1];
                }

                if(values[1] < values[0]) {
                    values[1] = values[0];
                }

                if (values[0] < options.min) {
                    values[0] = options.min;
                }

                if (values[1] > options.max) {
                    values[1] = options.max;
                }

                return values;
            }

        }
    );

})(jQuery, window, document);