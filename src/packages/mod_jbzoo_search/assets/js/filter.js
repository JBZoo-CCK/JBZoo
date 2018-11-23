/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 */

;
(function ($, window, document, undefined) {


    var autoSubmitExclude = [
            '.jsMoney',
            '.jsNoSubmit'
        ].join(', '),

        // field list for auto submit
        autoSubmitFileds  = [
            'select',
            'input',
            'input[type=text]',
            'input[type=radio]',
            'input[type=checkbox]',
            '.jsSlider'
        ].join(', '),

        // filed list that mustn't reset
        resetExclude      = [
            ':reset',
            ':submit',
            ':button',
            'input[type="hidden"]',
            '.jsMoney'
        ].join(', ');

    // global hack for reset button in filter forms
    jQuery(function ($) {
        $('.jsFilter .jsReset').unbind();
    });

    /**
     * Filter form handler
     */
    JBZoo.widget('JBZoo.Filter', {
            'autosubmit'   : 0,
            'submitTimeOut': 100
        },
        {
            init: function ($this) {
                $this._initAutoSubmit();
            },

            /**
             * Listen change event
             * @returns {boolean}
             * @private
             */
            _initAutoSubmit: function () {
                var $this   = this,
                    $fields = $this.$(autoSubmitFileds).not(autoSubmitExclude);

                if (!$this.options.autosubmit) {
                    return false;
                }

                $this.on('change', $fields, function () {
                    $this._submitForm();
                });

                $this.on('change.JBZooSlider', $fields, function () {
                    $this._delay(function () {
                        $this._submitForm();
                    }, 1000, 'submitForm');
                });
            },

            /**
             * Hack for submit (widgets, browsers, etc)
             * @returns {boolean}
             * @private
             */
            _submitForm: function () {
                var $this = this,
                    $form = $this.el.is('form') ? $this.el : $this.$('form');

                if (!$this.options.autosubmit) {
                    return false;
                }

                $this._delay(function () {
                    $form.trigger('submit').submit();
                }, $this.options.submitTimeOut, 'submitForm');

                return true;
            },

            /**
             * Reset button
             * @param e
             * @param $this
             * @returns {boolean}
             */
            'click .jsReset': function (e, $this) {

                var $inputList = $this.el.find(':input, .jsSlider').not(resetExclude);

                $inputList.each(function (n, input) {

                    var $input = $(input);

                    if ($input.is('select')) {
                        // any selects
                        $input.JBZooSelect().JBZooSelect('reset');

                    } else if ($input.is('.jbcolor-input')) {
                        // JBColor Widget
                        var $colors = $input.closest('.jbzoo-colors');
                        $colors.JBZooColors('reset');


                    } else if ($input.is('[type=radio]')) {
                        // radio buttons
                        var $group = $input.closest('.jbfilter-row');
                        $('input[type=radio]:eq(0)', $group).attr('checked', 'checked');

                    } else if ($input.is('[type=checkbox]')) {
                        // checkbox buttons
                        $input.removeAttr('checked');

                    } else if ($input.is('.jsSlider') && $input.data('JBZooSlider')) {
                        // advanced slider
                        $input.JBZooSlider('reset');


                    } else if ($input.is('.jsSlider')) {
                        // simple slider
                        var slider = $input.find('.ui-slider').data('slider');
                        slider.values([
                            slider.options.min,
                            slider.options.max
                        ]);

                        $('.slider-value-0', $input).html(JBZoo.numberFormat(slider.options.min, 0, ".", " "));
                        $('.slider-value-1', $input).html(JBZoo.numberFormat(slider.options.max, 0, ".", " "));
                        $('[type=hidden][name*="range"]', $input).val(slider.options.min + '/' + slider.options.max);

                    } else {
                        // default like text input
                        $input.val('');
                    }

                });

                $this._submitForm();

                return false;
            }

        });

})(jQuery, window, document);
