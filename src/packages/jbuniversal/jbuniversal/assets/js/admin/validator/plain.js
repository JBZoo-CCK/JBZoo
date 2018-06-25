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

    JBZoo.widget('JBZoo.PriceValidator.Plain', {
            'message_duplicate_values': 'Values duplicates in other variant',
            'message_variant_invalid' : 'The variant is invalid'
        },
        {
            errors: {},

            init: function ($this) {

                this.constructor.parent.init.apply($this);
            },

            validate: function () {
                this.errors = {};
                this.errors = this.duplicates();

                return this.errors;
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
                this.clearMessages($row);

                return this;
            },

            fill: function () {
                var $this = this;
                this.$('.jbprice-variation-row').each(function (i, row) {
                    var $row = $(row);
                    $this.addOptions($row);
                });

                return this;
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