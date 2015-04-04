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

    JBZoo.widget('JBZoo.Quantity', {
            'default' : 1,
            'step'    : 1,
            'min'     : 1,
            'max'     : 999999,
            'decimals': 0,
            'speed'   : 200,
            'onChange': $.noop
        }, {
            value: 0,

            isAnimate: false,

            $input : false,
            $box   : false,
            $digits: false,

            init: function ($this) {
                // force validate numeric options
                $this._cleanupOptions();

                // get links to DOM
                $this.$input = $this.$('.jsInput');
                $this.$box = $this.$('.item-count-digits');
                $this.$digits = $this.$('.item-count-digits dd');

                // set starting state
                $this._setValue($this.$input.val());
                $this._updateView();
            },

            /**
             * External method for set new value
             * @param newValue
             */
            setValue: function (newValue) {
                this._setValue(newValue);
                this._updateView();
            },

            /**
             * Get current value
             * @returns {number}
             */
            getValue: function () {
                return this._validate(this.value);
            },

            /**
             * Set new value to input
             * @param newValue
             * @private
             */
            _setValue: function (newValue) {
                var $this = this,
                    $input = $this.$input,
                    oldValue = $this.value;

                // set value
                newValue = $this._validate(newValue);
                $input.val($this._toFormat(newValue));
                $this.value = newValue;

                // change callback
                if (newValue != oldValue) {
                    var args = [oldValue, newValue];

                    $this._trigger('change', args);

                    if ($.isFunction($this.options.onChange)) {
                        $this.options.onChange.apply($this, args);
                    }
                }
            },

            /**
             * Cleanup option list
             * @private
             */
            _cleanupOptions: function () {
                var $this = this;

                $.extend(true, {}, $this.options, {
                    'default' : JBZoo.toFloat($this.options['default']),
                    'step'    : JBZoo.toFloat($this.options.step),
                    'min'     : JBZoo.toFloat($this.options.min),
                    'max'     : JBZoo.toFloat($this.options.max),
                    'decimals': JBZoo.toInt($this.options.decimals)
                });
            },

            /**
             * Cleanup and validate value
             * @param value
             * @returns {*|Number}
             * @private
             */
            _validate: function (value) {
                var $this = this;

                value = JBZoo.toFloat(value);
                value = JBZoo.round(value, $this.options.decimals);

                if (value < $this.options.min) {
                    value = $this.options.min;
                }

                if (value > $this.options.max) {
                    value = $this.options.max;
                }

                return value;
            },

            /**
             * Formted output
             * @param value
             * @returns {string}
             * @private
             */
            _toFormat: function (value) {
                value = JBZoo.round(value, this.options.decimals);
                return value;
            },

            /**
             * Update
             * @private
             */
            _updateView: function () {

                var $this = this,
                    max = this._validate($this.value) + 3 * JBZoo.toFloat($this.options.step);

                for (var i = 0; i < 5; i++) {
                    max = max - $this.options.step;
                    $this.$digits.eq(i).html($this._toFormat(max));
                }

                this.$box.css({
                    top      : 0,
                    marginTop: -$this.$digits.height() * 2 + 'px'
                });
            },

            /**
             * Check, is currecnt value is valid for current config
             * @param value
             * @returns {boolean}
             * @private
             */
            _isValid: function (value) {
                value = JBZoo.toFloat(value);

                if (value < this.options.min) {
                    return false;

                } else if (value > this.options.max) {
                    return false;
                }

                return true;
            },

            /**
             * No scroll animations for no valid values
             * @param newValue
             * @private
             */
            _noScroll: function (newValue) {

                var $this = this,
                    top = JBZoo.toInt(this.$box.css('top')),
                    dir = newValue > $this.value > 0 ? 1 : -1;

                $this._setValue(newValue);

                if ($this.isAnimate) {
                    return;
                }

                $this.isAnimate = true;
                $this.$box
                    .stop()
                    .animate({
                        top: (top + ($this.$digits.height() / 2 * dir)) + 'px'
                    }, {
                        duration: $this.options.speed / 2,
                        complete: function () {
                            $this.$box
                                .stop()
                                .animate({
                                    top: top + 'px'
                                }, {
                                    duration: $this.options.speed / 2,
                                    complete: function () {
                                        $this.isAnimate = false;
                                    }
                                });
                        }
                    });
            },

            /**
             * Set new value and start animate
             * @param newValue
             * @private
             */
            _change: function (newValue) {

                var $this = this,
                    dir = newValue > $this.value ? 1 : -1;

                if (!$this._isValid(newValue)) {
                    $this._noScroll(newValue);
                    return;
                }

                if ($this.isAnimate) {
                    return;
                }

                $this._updateView();
                $this._setValue(newValue);

                $this.isAnimate = true;
                $this.$box
                    .stop()
                    .animate({top: dir * $this.$digits.height() + 'px'}, {
                        duration: $this.options.speed,
                        complete: function () {
                            $this.isAnimate = false;
                        }
                    });
            },

            'click .jsAdd': function (e, $this) {
                var newValue = $this.value + $this.options.step;
                $this._change(newValue, true);
            },

            'click .jsRemove': function (e, $this) {
                var newValue = $this.value - $this.options.step;
                $this._change(newValue, true);
            },

            'focus .jsInput': function (e, $this) {
                $this.$input.css('opacity', '1');
                $this.$box.hide();
            },

            'blur .jsInput': function (e, $this) {
                $this.$input.css('opacity', '0');
                $this.$box.show();
            },

            'change .jsInput': function (e, $this) {
                $this._setValue($(this).val());
                $this._updateView();
            },

            'keyup .jsInput': function (e, $this) {
                $this._updateView();
            },

            'keydown .jsInput': function (e, $this) {

                if ($this._key(e, 'arrow-top')) {
                    $this._setValue($this.value + $this.options.step);
                    $this._updateView();
                    return false;
                }

                if ($this._key(e, 'arrow-down')) {
                    $this._setValue($this.value - $this.options.step);
                    $this._updateView();
                    return false;
                }
            },

            'mouseenter .jsCountBox': function (e, $this) {
                $this.$input.focus();
            },

            'mousewheel .jsInput': function (e, $this) {

                if ($this.$input.is(':focus')) {
                    var value = $this.value;
                    if (e.originalEvent.wheelDelta > 0) {
                        value += $this.options.step;
                    } else {
                        value -= $this.options.step;
                    }

                    $this._setValue(value);
                    $this._updateView();
                }

                return false;
            }

        }
    );

})(jQuery, window, document);