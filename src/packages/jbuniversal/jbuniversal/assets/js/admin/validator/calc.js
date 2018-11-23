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

    JBZoo.widget('JBZoo.PriceValidator.Calc', {
            'message_duplicate_values': 'Values duplicates in other variant',
            'message_variant_invalid' : 'The variant is invalid',
            'message_choice_limiting' : 'In this mode you can choose only one parameter'
        },
        {
            errors: {},

            init: function ($this) {

                this.constructor.parent.init.apply($this);
                this.fill();
            },

            'change .simple-param .jsElement input, .simple-param .jsElement select, .simple-param .jsElement textarea': function (e, $this) {

                var $param = $(this).closest('.simple-param'),
                    $row = $param.closest('.jbprice-variation-row');
                $this
                    .clear()
                    .disableRow($row)
                    .addOptions($row)
                    .getErrors();
            },

            clear: function () {

                var $this = this;
                this.$('.jbprice-variation-row').each(function (i, row) {
                    $this.clearRow(row);
                });

                return this;
            },

            clearRow: function (row) {
                var $row = $(row);
                this.enableRow($row)
                    .clearMessages($row);

                return this;
            },

            fill: function () {

                var $this = this;

                this.$('.jbprice-variation-row').each(function (i, row) {
                    var $row = $(row);
                    $this
                        .addOptions($row)
                        .disableRow($row);
                });

                return this;
            },

            enableRow: function (row) {

                var $this = this;
                $('.simple-param', row).each(function (i, param) {
                    var $param = $(param);
                    $this.enable($param);
                });

                return this;
            },

            disableRow: function ($row) {

                var $this = this;
                $('.simple-param', $row).each(function (index, param) {

                    param = $(param);
                    if ($this.hasValue(param)) {

                        $this.enable(param);
                        $this.disable(param.siblings('.simple-param'));

                        return $this;
                    }
                });

                return this;
            },

            disable: function (param) {
                param
                    .addClass('disabled')
                    .removeClass('active')
                    .find('.jsMessage')
                    .addClass('lock');
                this.lockFields(param);

                return this;
            },

            enable: function ($param) {
                $param
                    .removeClass('disabled')
                    .addClass('active')
                    .find('.jsMessage')
                    .removeClass('lock');
                this.unlockFields($param);

                return this;
            },

            lockFields: function ($param) {

                $('input, select, textarea', $param).attr({
                    'disabled': 'true',
                    'readonly': 'true'
                });
                $('.jsMessage', $param)
                    .attr('title', this.options.message_choice_limiting)
                    .tooltip();

                return this;
            },

            unlockFields: function ($param) {

                $('input, select, textarea', $param).removeAttr('disabled readonly');
                $('.jsMessage', $param)
                    .tooltip()
                    .tooltip('destroy');

                return this;
            },

            hasValue: function (param) {

                var field = $('select, input, textarea', param),
                    value = '';

                if (field.attr('type') == 'radio') {
                    field = $('input[type="radio"]:checked', param);
                    value = field.val();
                } else {
                    value = field.val();
                }
                value = $.trim(value);

                return value.length > 0;
            },

            validate: function () {
                this.errors = {};
                this.errors = this.duplicates();

                return this.errors;
            },

            getErrors: function () {

                this.validate();
                if (JBZoo.empty(this.errors)) {
                    return false;
                }

                var $this = this,
                    variations = this.$('.jbprice-variation-row');

                $.each(this.errors, function (key, error) {
                    var variants = $(variations.get(error.variant)),
                        params = $('.simple-param', variants),
                        label = $('.jsVariantLabel', variants);

                    $this.message(label, $this.options.message_variant_invalid);
                    $.each(error.index, function (index) {
                        var param = params.get(index);
                        $this.message(param, $this.options.message_duplicate_values);
                    });
                });

                return this.errors;
            }

        }
    );

})(jQuery, window, document);