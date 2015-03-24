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

    JBZoo.widget('JBZoo.PriceValidator', {
            'isOverlay': false,

            'message_variant_invalid' : 'The variant is invalid',
            'message_duplicate_values': 'Values duplicates in other variant'
        },
        {
            init: function () {

                var $this = this;
                this.$('.jbprice-variation-row').each(function (i, row) {
                    $this.addOptions($(row));
                });
            },
            'change .simple-param .jsElement input, .simple-param .jsElement select, .simple-param .jsElement textarea': function (e, $this) {

                var $row = $(this).closest('.jbprice-variation-row');
                $this
                    .clear()
                    .addOptions($row)
                    .getErrors();
            },

            selected: function () {

                var $this = this,
                    result = {};

                this.$('.jbprice-variation-row').each(function (i, row) {
                    var data = {};
                    $('.simple-param .jsElement', row).each(function (j, param) {

                        var value = $this._simpleData(param);
                        if (!JBZoo.empty(value)) {
                            value.index = j;
                            data[j] = value;
                        }
                    });

                    if (!JBZoo.empty(data)) {
                        result[i] = data;
                    }
                });

                return result;
            },

            duplicates: function () {

                var selected = this.selected(),
                    duplicates = {};
                $.each(selected, function (i) {
                    var subject = selected[i];
                    $.each(selected, function (j, row) {
                        if ((Object.keys(row).length === Object.keys(subject).length) && i != j) {
                            var errors = {};
                            $.each(row, function (k, param) {
                                if ((!JBZoo.empty(param)) && (!JBZoo.empty(subject[k]))) {
                                    if (param.value == subject[k].value && param.index == subject[k].index) {
                                        errors[subject[k].index] = {
                                            'index': subject[k].index,
                                            'value': subject[k].value
                                        };
                                    }
                                }
                            });
                            if (Object.keys(subject).length == Object.keys(errors).length && !JBZoo.empty(errors)) {
                                duplicates[i + j] = {
                                    'variant': i,
                                    'index'  : errors
                                };
                            }
                        }
                    });
                });

                return duplicates;
            },

            addOptions: function ($row) {

                var $this = this,
                    $options = $('.variation-label .options', $row),
                    $overflow = $('.overflow', $options),
                    $price = $('.jsVariantPrice', $options),
                    core = {};

                $overflow.html('');
                $('.core-param', $row).each(function (i, param) {

                    var option = $this._coreData(param);
                    if (!JBZoo.empty(option)) {
                        core[i] = option;
                    }
                });

                $.each(core, function (index, data) {

                    if (index === 0) {
                        $price.html()
                    }
                });

                $('.simple-param .jsElement', $row).each(function (i, param) {

                    var option = $this._simpleData($(param));
                    if (!JBZoo.empty(option)) {

                        $overflow.append(
                            '<div class="option">' +
                            '<span title=\"' + option.label + '\" class="key">' + option.value + '</span></div>');
                    }
                });

                $('.option .key', $options).tooltip();

                return this;
            },

            clearOptions: function (row) {
                $('.overflow', row).html('');

                return this;
            },

            message: function (parent, message) {

                $('.jsMessage', parent)
                    .attr('title', message)
                    .addClass('error')
                    .tooltip();

                return this;
            },

            clearMessages: function (row) {

                $('.jsMessage', row)
                    .removeClass('error lock')
                    .removeAttr('title data-original-title')
                    .tooltip()
                    .tooltip('destroy');

                return this;
            },

            _simpleData: function (simple) {

                var $field = $('input, select, textarea', simple),
                    data = {},
                    value = $field.val(),
                    label = $('.label', simple);

                if ($field.attr('type') == 'radio') {
                    $field = $('input[type="radio"]:checked', simple);
                    value = $field.val();
                }

                value = $.trim(value);
                if (value.length > 0) {
                    data = {
                        'value': value,
                        'label': $.trim(label.text())
                    }
                }

                return data;
            },

            _coreData: function (core) {

                var $field = $('input, select, textarea', core),
                    data = {},
                    value = $field.val(),
                    label = $('.label', core);

                if ($field.attr('type') == 'radio') {
                    $field = $('input[type="radio"]:checked', core);
                    value = $field.val();
                }

                value = $.trim(value);
                if (!JBZoo.empty(value)) {
                    data = {
                        'value': value,
                        'label': $.trim(label.text())
                    }
                }

                return data;
            }

        }
    );

})(jQuery, window, document);