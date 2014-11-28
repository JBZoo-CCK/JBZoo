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

    JBZoo.widget('JBZoo.PriceEdit', {
            // options
            'isOverlay': false,
            'isValid'  : false,
            'text_hide': 'Hide variations',
            'text_show': 'Show variations',
            'duration' : 300
        },
        {
            'validator': {},

            init: function ($this) {
                $this.sortable();

                //create validator
                if (!!this.options.isOverlay) {
                    this.validator = this.el.JBZooPriceValidatorCalc().data('JBZooPriceValidatorCalc');
                } else {
                    this.validator = this.el.JBZooPriceValidatorPlain().data('JBZooPriceValidatorPlain');
                }
            },

            'click .jsShowVariations': function (e, $this) {
                var variations = $this.getVariations();
                if (variations.is(':hidden')) {

                    $(this).text($this.options.text_hide);
                    variations.slideDown();
                    return;
                }

                $(this).text($this.options.text_show);
                variations.slideUp();
            },

            'click .jsToggleVariation': function () {

                var $row = $(this).closest('fieldset');
                $row
                    .toggleClass('fieldset-hidden')
                    .siblings()
                    .addClass('fieldset-hidden');
            },

            'click .jsJBRemove': function (e, $this) {

                var $row = $(this).closest('.jbprice-variation-row');

                $row.slideUp(300, function () {
                    $row.remove();
                    $this.validator.clear().fill();
                    $this.reBuild();
                });
            },

            'click .jsNewPrice': function (e, $this) {

                var row = $this.$('.jbprice-variation-row:first').clone().hide();
                $('*', row)
                    .removeAttr('id checked selected')
                    .unbind();

                $('input[type="text"], textarea', row).val("");
                $('label', row).removeAttr('for');

                $('.variant-param', row).each(function (i) {
                    var id = parseInt(new Date().getTime() + i);
                });

                $this.$('.variations-list').append(row);

                $this
                    .reBuild()
                    .sortable();
                $this.validator
                    .clearRow(row)
                    .clearOptions(row);

                row.slideDown();
            },

            sortable: function () {

                var $this = this,
                    rows = this.$('.jbprice-variation-row');

                rows.delegate('.jsJBMove', 'mousedown', function () {
                    rows
                        .siblings()
                        .addClass('fieldset-hidden')
                });

                this.$('.jsJBMove').sortable({
                    'forcePlaceholderSize': true,
                    'items'               : rows,
                    'placeholder'         : "ui-state-highlight",
                    'stop'                : function () {
                        $this.reBuild();
                        $this.validator.getErrors();
                    }
                });
            },

            reBuild: function () {

                this.$('.jbprice-variation-row:first .jsJBRemove').hide();

                this.$('.jbprice-variation-row').each(function (n) {

                    var $row = $(this);
                    $('input[type="radio"]', $row).each(function () {

                        var field = $(this),
                            name = field.attr('name'),
                            random = Math.floor((Math.random() * 999999) + 1);

                        field.attr('name', field.attr('name')
                            .replace(/\[variations\]\[\d\]/i, '[variations-' + random + '][' + n + ']'));
                    });
                });

                this.$('.jbprice-variation-row').each(function (i) {
                    i++;
                    var row = $(this);
                    if (!row.is(':first-child')) {
                        $('.jsJBRemove', row).show();
                    }

                    $('.list-num', row).text(i);

                    $('input:not([type="radio"]), select, textarea', row).each(function () {

                        var field = $(this),
                            name = field.attr('name');

                        field.attr('name', field.attr('name').replace(/\[variations\]\[\d\]/i, '[variations][' + i + ']'));
                    });

                    $('input[type="radio"]', row).each(function () {

                        var field = $(this),
                            name = field.attr('name');
                        field.attr('name', field.attr('name').replace(/\[variations\-\d*\]\[\d\]/i, '[variations][' + i + ']'));

                        if (field.is(':checked') == true) {
                            field.attr('checked', 'checked');
                        }
                    });
                });

                return this;
            },

            isValid: function () {

                var errors = this.validator.clear().fill().getErrors();
                if (!JBZoo.empty(errors)) {
                    var $this = this,
                        variations = this.$('.jbprice-variation-row');

                    $.each(errors, function (key, data) {
                        $this.scrollTo(variations.get(data.variant));
                    });
                }

                return !!JBZoo.empty(errors);
            },

            scrollTo: function (row) {

                var $body = $('body');
                row = $(row);
                if (!$body.is(':animated')) {

                    $body.stop(true).animate({
                        scrollTop: row.offset().top
                    }, 500);
                }

                if (this.$('.variations').is(':hidden')) {
                    this.$('.jsShowVariations').trigger('click');
                }

                if (row.hasClass('fieldset-hidden')) {
                    row.toggleClass('fieldset-hidden');
                }
            },

            getVariations: function () {
                return this.$('.variations');
            }

        }
    );

})(jQuery, window, document);