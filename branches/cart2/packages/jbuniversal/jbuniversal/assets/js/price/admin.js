/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /**
    * JBZoo JBPrice advance (for admin panel)
    * @param options
    * @constructor
    */
    $.fn.JBZooPriceAdvanceAdmin = function (options) {

        return $(this).each(function (n, obj) {

            var $obj = $(obj),
                $variations = $('.variations', $obj);

            if ($variations.length === 0) {
                return false;
            }
            options = $.extend({}, {
                'price_mode'          : 0,
                'text_variation_show' : 'Show variations',
                'text_variation_hide' : 'Hide variations',
                'adv_field_param_edit': 0,
                'all_params'          : {},
                'base_currency'       : $('.basic-currency', $obj).val(),
                'base_sku'            : $('.basic-sku', $obj).val()
            }, options);

            // init
            var validator = $obj.JBZooPriceAdvanceValidator({
                'price_mode': options.price_mode
            });

            bindToggleVariationEvent();
            addSortable();

            rebuildList();
            $('.jbpriceadv-variation-row', $obj).addClass('fieldset-hidden');
            if (!options.adv_field_param_edit) {
                $.each(options.all_params, function (n, obj) {
                    $('.element-' + obj).hide();
                });
            }

            function rebuildList() {

                $('.jbpriceadv-variation-row .jbremove', $obj).show();

                $('.jbpriceadv-variation-row', $obj).each(function () {

                    var $row = $(this);

                    $('input[type="radio"]', $row).each(function () {
                        var $this = $(this),
                            random = Math.floor((Math.random() * 999999) + 1);

                        $this.attr('name', $this.attr('name').replace(/\[variations\]\[\d\]/i, '[variations-' + random + '][' + n + ']'));
                    });
                });

                $('.jbpriceadv-variation-row', $obj).each(function (n, row) {
                    n++;
                    var $row = $(this),
                        $variantLabel = $('.variation-label', $row);
                    if (n == 1) {
                        $('.jbremove', $row).hide();
                    }

                    $('.list-num', $row).text(n);

                    if (!$('.row-sku', $row).val() && options.base_sku) {
                        $('.row-sku', $row).val(options.base_sku);
                    }

                    if ($('.row-balance', $row).val() == '') {
                        $('.row-balance', $row).val('-1');
                    }

                    $('input[type=text], input[type=hidden], input[type=checkbox], select, textarea', $row).each(function () {
                        var $control = $(this);

                        if (typeof $control.attr('name') != 'undefined') {
                            $control.attr('name', $control.attr('name').replace(/\[variations\]\[\d\]/i, '[variations][' + n + ']'));
                        }

                    });

                    $('input[type="radio"]', $row).each(function () {
                        var $this = $(this);

                        if (typeof $this.attr('name') != 'undefined') {
                            $this.attr('name', $this.attr('name').replace(/\[variations\-\d*\]\[\d\]/i, '[variations][' + n + ']'));
                            if ($this.is(':checked') == true) {
                                $this.attr('checked', 'checked');
                            }
                        }
                    });

                });
            }

            function bindToggleVariationEvent() {

                $('.jbpriceadv-variation-row', $obj).each(function () {

                    var $row = $(this);

                    var $toggle = $('.jsToggleVariation', $row);

                    if (!$toggle.hasClass('init')) {

                        $toggle.on('click', function () {

                            $row.toggleClass('visible fieldset-hidden');
                            $row.removeClass('visible').siblings().addClass('fieldset-hidden');
                        });
                    }

                    $toggle.addClass('init');
                });
            }

            $('.jsShowVariations', $obj).click(function () {

                if ($variations.is(':hidden')) {
                    $(this).text(options.text_variation_hide);
                    $variations.slideDown();
                } else {
                    $(this).text(options.text_variation_show);
                    $variations.slideUp();
                }

                return false;
            });

            function addSortable() {

                $('.jbpriceadv-variation-row', $obj).delegate(".jsJBMove", "mousedown", function () {

                    $('.jbpriceadv-variation-row', $obj)
                        .removeClass('visible')
                        .addClass("fieldset-hidden");
                });

                $('.jsJBMove', $obj).sortable({
                    forcePlaceholderSize: true,
                    'items'             : $('.jbpriceadv-variation-row', $obj),
                    'placeholder'       : "ui-state-highlight",
                    'stop'              : function (ev, ui) {
                        rebuildList();

                        validator.showErrors();
                    }
                }).disableSelection();
            }

            $('.variations-list').on('stop-move', function () {
                rebuildList();
            });

            $('.jsNewPrice', $obj).click(function () {

                var $newRow = $('.jbpriceadv-variation-row:first', $obj).clone().hide();

                $('input, select, textarea, select option:selected', $newRow)
                    .removeAttr('id')
                    .removeAttr('checked')
                    .removeAttr('selected')
                    .unbind();

                $('input[type="text"], textarea', $newRow).val('');

                $('.jsToggleVariation', $newRow)
                    .removeClass('init')
                    .unbind();

                var jbColor = $('.jbzoo-colors', $newRow);
                if (jbColor.length > 0) {

                    jbColor.removeClass('jbcolor-initialized');

                    $('.jbcolor-label', jbColor).removeClass('checked');
                    $('.jbcolor-input', jbColor)
                        .unbind()
                        .removeClass('checked');

                    jbColor.JBColorHelper({
                        "multiple": false
                    });
                }

                var jbImage = $('.jbprice-img-row-file', $newRow);

                if (jbImage.length > 0) {

                    $('span, button', jbImage).remove();
                    jbImage
                        .removeClass('JBPriceImage-init')
                        .attr('id', Math.random().toString(36).replace('.', ''))
                        .initJBPriceAdvImage();
                }

                $('.variation-label .description, ' +
                '.variation-label .options .overflow, ' +
                '.variation-label .options .jsVariantPrice', $newRow)
                    .html('');
                $('.variation-label .options .jsVariantPrice', $newRow)
                    .removeAttr('title data-original-title')
                    .unbind();

                $('label', $newRow).removeAttr('for');

                $('.variant-param', $newRow).each(function (i) {

                    var $param = $(this),
                        id = parseInt(new Date().getTime() + i);
                    $(' > * label', $param).each(function (n) {

                        var $label = $(this),
                            random = Math.floor((Math.random() * 999999) + 1);
                        id += n + random;

                        $label.attr('for', id);

                        $('input', $label).attr('id', id);
                        $label.prev('input').attr('id', id);
                    });
                });

                $('.variations-list', $obj).append($newRow);

                rebuildList();
                bindToggleVariationEvent();
                addSortable();
                $obj.trigger('newvariation');

                $newRow.slideDown();

                return false;
            });

            $obj.on('click', '.jbremove', function () {
                var $row = $(this).closest('.jbpriceadv-variation-row');
                $row.slideUp(300, function () {
                    $row.remove();
                    rebuildList();
                });
                validator.showErrors();
            });

        });
    };

})(jQuery, window, document);