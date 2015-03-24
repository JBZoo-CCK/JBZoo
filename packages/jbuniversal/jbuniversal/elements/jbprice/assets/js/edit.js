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
            'isAdvance'                 : true,
            'isOverlay'                 : false,
            'isValid'                   : false,
            'text_hide'                 : 'Hide variations',
            'text_show'                 : 'Show variations',
            'validator_variant_invalid' : 'The variant is invalid',
            'validator_duplicate_values': 'Values duplicates in other variant',
            'validator_choice_limiting' : 'In this mode you can choose only one parameter',
            'duration'                  : 300
        },
        {
            'validator': {},

            init: function () {
                var _options = {
                    'message_variant_invalid' : this.options.validator_variant_invalid,
                    'message_duplicate_values': this.options.validator_duplicate_values,
                    'message_choice_limiting' : this.options.validator_choice_limiting
                };
                this.$('.jbprice-variation-row:first .jsJBRemove').hide();
                this.sortable();

                //create validator if variations is on
                if (this.options.isAdvance) {
                    if (!!this.options.isOverlay) {
                        this.validator = this.el.JBZooPriceValidatorCalc(_options).data('JBZooPriceValidatorCalc');
                    } else {
                        this.validator = this.el.JBZooPriceValidatorPlain(_options).data('JBZooPriceValidatorPlain');
                    }
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

                var row = $this.$('.jbprice-variation-row:first'),
                    clone = row.clone().hide();

                $('*', clone)
                    .removeAttr('id checked selected');

                $('input[type="text"], textarea', clone).val("");

                //Init JBZooColors.
                var colors = $('.variant-color-wrap', row);
                if (colors.length > 0) {
                    var oldColor = $('.jbzoo-colors', colors).data('JBZooColors'),
                        newColor = $('.variant-color-wrap .jbzoo-colors', clone);

                    $('.jbcolor-label, .jbcolor-input', newColor).removeClass('checked')
                    newColor.JBZooColors(oldColor.options);
                }
                //Init JBZooMedia.
                var image = $('.variant-image-wrap', row);
                if (image.length > 0) {
                    var oldMedia = $('.jsMedia', image).data('JBZooMedia'),
                        newMedia = $('.variant-image-wrap .jsMedia', clone);
                    $('.jsMediaCancel, .jsMediaButton', newMedia).remove();

                    newMedia.JBZooMedia(oldMedia.options);
                }
                //Init JBZooBalance. Helper for radio input.
                var balance = $('.variant-balance-wrap', row);
                if (balance.length > 0) {
                    var oldBalance = $('.jsBalance', balance).data('JBZooPriceBalance'),
                        newBalance = $('.variant-balance-wrap .jsBalance', clone);

                    newBalance.JBZooPriceBalance(oldBalance.options);
                }
                //Init JBZooPriceEditElement_descriptionEdit.
                var description = $('.jsDescription', row);
                if (description.length > 0) {
                    var newDesc = $('.jsDescription .jsField', clone);
                    newDesc.JBZooPriceEditElement_descriptionEdit();
                }

                $('.variant-param', clone).each(function (i, param) {
                    var $param = $(param),
                        id = parseInt(new Date().getTime() + i);

                    $('.jsElement label', $param).each(function (n, label) {

                        var $label = $(label),
                            random = Math.floor((Math.random() * 999999) + 1);

                        id += n + random;

                        $label.attr('for', id);

                        $('input', $label).attr('id', id);
                        $label.prev('input').attr('id', id);
                    });
                });

                $this.$('.variations-list').append(clone);

                $this
                    .reBuild()
                    .sortable();
                $this.validator
                    .clearRow(clone)
                    .clearOptions(clone);

                clone.slideDown();
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
                        field.attr('name', field.attr('name').replace(/(\[variations-\d*\]\[\d*\])/i, '[variations][' + i + ']'));

                        if (field.is(':checked') == true) {
                            field.attr('checked', 'checked');
                        }
                    });
                });

                return this;
            },

            isValid: function () {

                if (!!this.options.isAdvance === false) {
                    return true;
                }

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