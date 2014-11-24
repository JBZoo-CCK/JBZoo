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

    $.fn.JBZooPriceAdvanceDefaultValidator = function (parent) {

        var $this = parent;
        $this.super = parent;

        $this.refreshAllModeDefault = function () {

            $('.jbpriceadv-variation-row', $this).each(function () {

                var $row = $(this);
                $('.simple-param', $row).each(function () {

                    var $param = $(this);

                    bindChangeEventModeDefault($param);
                });
            });
        };

        $this.showErrors = function () {

            $this.clearStyles();
            var data = validateModeDefault();

            if (Object.keys(data).length > 0) {

                var $attention = null;
                for (var j in data) {
                    var $row = $('.jbpriceadv-variation-row', $this).eq(data[j].variant);
                    $this.setValid(false);

                    if (data[j].exists === false) {

                        $this.super.setValid(false);

                    } else if (data[j].empty == true) {

                        $this.setValid(false);
                        $attention = $('.variation-label .jsAttention', $row);
                        $attention.addClass('error');
                        $this.addTooltipEmptyMessage($attention);
                    }

                    for (var p in data[j].repeat) {

                        var $jbpriceParams = $('.jbprice-params', $row),
                            $paramByIndex = $jbpriceParams.children().eq(data[j].repeat[p].elem_index);
                        $attention = $('.jsJBPriceAttention', $paramByIndex);

                        $attention.addClass('error');
                        $this.addTooltipMessage($attention);
                    }

                }
            } else {
                $this.setValid(true);
            }
        };

        function addEmptyMessage($row) {
            $row.attr('title', 'These variation is empty');
            $row.tooltip();
        }

        function scrollToRow($row) {

            if (typeof $row != 'undefined' && $row.length > 0) {
                $row.removeClass('fieldset-hidden');
                $row.addClass('visible');

                $('html, body').stop(true).animate({
                    scrollTop: $row.offset().top
                }, 500);

                return true;
            }

            var data = validateModeDefault();

            if (Object.keys(data).length > 0) {

                var first = data[Object.keys(data)[0]];

                if ($('.variations', $this).is(':hidden')) {
                    $('.jsShowVariations', $this).trigger('click');
                }

                var $first = $('.jbpriceadv-variation-row', $this).eq(first['variant']);

                $first.removeClass('fieldset-hidden');
                $first.addClass('visible');

                $('html, body').stop(true).animate({
                    scrollTop: $first.offset().top
                }, 500);
            }

            return false;
        }

        function validateModeDefault() {

            var objects = {},
                result = {},
                value = '';

            $('.jbpriceadv-variation-row', $this).each(function (i) {

                var $row = $(this),
                    param = {};

                var exists = $('.simple-param', $row).each(function (p) {

                    var $param = $(this);

                    value = $this.setStatus($param);

                    if (value.length > 0) {
                        param[p] = {
                            'value' : value,
                            'index' : $param.index(),
                            'exists': true
                        };
                    }

                });

                if (Object.keys(param).length > 0) {
                    objects[i] = param;

                } /*else if (exists.length === 0) {
                 objects[i] = {
                 'exists': false
                 }

                 }*/ else if ($('.jbpriceadv-variation-row', $this).length > 1 && exists.length !== 0) {
                    objects[i] = {
                        'empty': true
                    };
                }
            });

            for (var n in objects) {
                var data = objects[n],
                    repeatable = {};
                for (var key in objects) {
                    for (var j in objects[key]) {
                        if (typeof data[j] == 'object' && typeof objects[key][j] == 'object') {
                            var dataIndex = $.trim(data[j].index),
                                dataValue = $.trim(data[j].value);

                            if ($.trim(objects[key][j].index) == dataIndex &&
                                $.trim(objects[key][j].value) == dataValue &&
                                $.trim(Object.keys(objects[key]).length) ==
                                $.trim(Object.keys(data).length) &&
                                n != key
                            ) {

                                repeatable[dataIndex] = {
                                    'variant'   : key,
                                    'elem_index': dataIndex,
                                    'elem_value': dataValue
                                };

                            }
                        }
                    }
                }
                if (data.exists === false) {
                    result[n] = {
                        'variant': n,
                        'exists' : data.exists
                    }

                } else if (data.empty === true) {
                    result[n] = {
                        'variant': n,
                        'empty'  : data.empty
                    };

                } else if (Object.keys(data).length > 0 &&
                    Object.keys(repeatable).length == Object.keys(data).length) {
                    result[n] = {
                        'variant': n,
                        'length' : Object.keys(data).length,
                        'repeat' : repeatable
                    };

                }
            }

            return result;
        }

        function bindChangeEventModeDefault($param) {

            $('input, select', $param).on('change', function () {

                $this.clearStyles();
                $this.insertOptions();
                $this.showErrors();
            });
        }

        $this.refreshAllModeDefault();
        $this.insertOptions();

        $this.on('newvariation', function () {

            $this.refreshAllModeDefault();
        });

        $this.on('errorsExists', function () {

            $this.showErrors();
            if ($this.data('valid') == false) {
                scrollToRow();
            }
        });

        return $this;
    };

    $.fn.JBZooPriceAdvanceOverlayValidator = function (parent) {

        var global = parent;

        $(this).each(function () {

            var $this = $(this);

            if ($this.hasClass('init')) {
                //return $this;
            }
            $this.super = global;

            function scrollToRow($row) {

                if (typeof $row != 'undefined' && $row.length > 0) {
                    $row.removeClass('fieldset-hidden');
                    $row.addClass('visible');

                    $('html, body').stop(true).animate({
                        scrollTop: $row.offset().top
                    }, 500);

                    return true;
                }

                var data = validateModeOverlay();

                for (var i in data) {

                    var variant = data[i].variant;
                }

                if (Object.keys(data).length > 0) {

                    if ($('.variations', $this).is(':hidden')) {
                        $('.jsShowVariations', $this).trigger('click');
                    }

                    var $first = $('.jbpriceadv-variation-row', $this).eq(variant);

                    $first.removeClass('fieldset-hidden');
                    $first.addClass('visible');

                    $('html, body').stop(true).animate({
                        scrollTop: $first.offset().top
                    }, 500);
                }

                return false;
            }

            function validateModeOverlay() {

                var objects = {},
                    result = {},
                    value = null;
                $('.jbpriceadv-variation-row', $this).each(function (i) {

                    var $row = $(this),
                        $active = $('.simple-param.active', $row);

                    if ($active.length > 0) {
                        value = $this.super.setStatus($active);

                        if (value.length > 0) {
                            objects[i] = {
                                'value': value,
                                'index': $active.index(),
                                'empty': false
                            };
                        } else if (value.length == 0 && $('.jbpriceadv-variation-row', $this).length > 1) {
                            objects[i] = {
                                'empty': true
                            };
                        }
                    }
                });

                var i = 0;
                for (var n in objects) {
                    var data = objects[n],
                        repeatable = [];

                    for (var key in objects) {
                        if ($.trim(objects[key].index) == $.trim(data.index) &&
                            $.trim(objects[key].value) == $.trim(data.value) &&
                            n != key
                        ) {
                            i++;

                            repeatable.push(key);
                            result[i] = {
                                'variant': n,
                                'repeat' : repeatable,
                                'index'  : objects[key].index,
                                'empty'  : objects[key].empty
                            };
                        }
                    }
                    if (data.empty === true) {
                        i++;
                        result[i] = {
                            'variant': n,
                            'empty'  : data.empty
                        };
                    }
                }

                return result;
            }

            function bindChangeSimpleParamEvent($param) {

                $('input, select', $param).on('change', function () {
                    var $field = $(this),
                        $param = $field.parents('.simple-param'),
                        $row = $field.parents('.jbpriceadv-variation-row');

                    $this.super.clearStyles();
                    $this.super.disableParams($row);
                    $this.super.setStatus($param);

                    if ($('.simple-param.active', $row).length == 0) {
                        $this.super.activateParams($row);
                    }

                    $this.super.insertOptions();
                    $this.super.showErrors();
                });
            }

            function refreshSimpleParam($param) {

                $this.super.setStatus($param);
                bindChangeSimpleParamEvent($param);
            }

            global.showErrors = function () {

                var data = validateModeOverlay();

                if (Object.keys(data).length > 0) {

                    for (var j in data) {

                        var $row = $('.jbpriceadv-variation-row', $this).eq(data[j].variant);

                        if (data[j].empty == true) {

                            var $attention = $('.variation-label .jsAttention', $row);
                            $attention.addClass('error');
                            $this.super.addTooltipEmptyMessage($attention);
                        }

                        var $param = $('.jbprice-params', $row).children().eq(data[j].index),
                            $attentionOverlay = $('.jsJBPriceAttention', $param);

                        $attentionOverlay.addClass('error');
                        $this.super.addTooltipMessage($attentionOverlay);
                    }

                    $this.super.setValid(false);

                } else {
                    $this.super.setValid(true);
                }

            };

            $this.refreshAllModeOverlay = function () {

                $('.jbpriceadv-variation-row', $this).each(function () {

                    var $row = $(this);
                    $this.super.disableParams($row);

                    $('.simple-param', $row).each(function () {

                        var $param = $(this);
                        refreshSimpleParam($param);
                    });

                    if ($('.simple-param.active', $row).length == 0) {
                        $this.super.activateParams($row);
                    }
                });
            };

            $this.refreshAllModeOverlay();
            $this.super.insertOptions();
            global.showErrors();

            $this.on('newvariation', function () {

                $this.refreshAllModeOverlay();
            });

            $this.on('errorsExists', function () {

                global.showErrors();
                if ($this.data('valid') === false) {
                    scrollToRow();
                }
            });

            $this.addClass('init');

            return $this;
        });
    };

    $.fn.JBZooPriceAdvanceValidator = function (options) {

        var validator = this;

        $(this).each(function () {

            validator.setValid = function (valid) {
                var valid = {
                    'valid': valid
                };
                validator.data(valid);
            };

            validator.setStatus = function ($param) {

                var $field = $('select, input', $param),
                    type = $field.attr('type'),
                    value = '',
                    field = null;

                if (type == 'radio') {
                    field = $('input[type="radio"]:checked', $param);
                    value = $.trim(field.val());

                } else {
                    value = $.trim($field.val());
                }

                if (value.length > 0) {

                    validator.activateParam($param);
                    return value;
                }

                return '';
            };

            validator.clearStyles = function () {
                $('.jbpriceadv-variation-row', validator).each(function () {

                    var $row = $(this);
                    $('.variation-label .jsAttention', $row)
                        .removeClass('error')
                        .tooltip()
                        .tooltip('destroy');

                    $('.simple-param', $row).each(function () {

                        var $param = $(this);
                        $('.jsJBPriceAttention', $param)
                            .removeClass('error')
                            .removeClass('disabled')
                            .tooltip()
                            .tooltip('destroy');
                    });
                });
            };

            validator.activateParam = function ($param) {

                $param.removeClass('disabled');
                $param.addClass('active');

                $('.jsJBPriceAttention', $param)
                    .removeClass('disabled')
                    .tooltip()
                    .tooltip('destroy');

                $('input, select', $param)
                    .removeAttr('disabled')
                    .removeAttr('readonly');
            };

            validator.disableParam = function ($param) {

                $param.removeClass('active');
                $param.addClass('disabled');

                $('.jsJBPriceAttention', $param).addClass('disabled');
                validator.addTooltipMessage($('.jsJBPriceAttention', $param));

                $('input, select', $param).attr({'disabled': 'true', 'readonly': 'true'});
            };

            validator.activateParams = function ($row) {

                $('.simple-param', $row).each(function () {
                    validator.activateParam($(this));
                });
            };

            validator.disableParams = function ($row) {

                $('.simple-param', $row).each(function () {
                    validator.disableParam($(this));
                });
            };

            validator.addTooltipMessage = function ($attention) {

                if ($attention.hasClass('error')) {
                    $attention.attr('title', 'These values ​​are already in another variation');
                    $attention.tooltip();
                    $attention.effect('pulsate');
                } else if ($attention.hasClass('disabled')) {
                    $attention.attr('title', 'In this mode you can choose only one parameter');
                    $attention.tooltip();
                }
            };

            validator.addTooltipEmptyMessage = function ($attention) {
                $attention.attr('title', 'No parameters selected');
                $attention.tooltip();
            };

            validator.insertOptions = function () {

                $('.jbpriceadv-variation-row', validator).each(function () {

                    var $row = $(this),
                        $options = $('.variation-label .options', $row),
                        $overflow = $('.overflow', $options),
                        $price = $('.jsVariantPrice', $options),
                        core = [];

                    var description = $('.core-param .description', $row),
                        label = $('.variation-label', $row);

                    if (typeof description.val() != 'undefined' && description.val().length > 0) {
                        $('.description', label).html(description.val());
                    }

                    var value = $('.variant-value', $row).val(),
                        currency = $('.variant-currency', $row).val();

                    $overflow.html('');

                    $price.html(value + " " + currency.toUpperCase());

                    var c = 0;
                    $('.core-param', $row).each(function () {

                        var $param = $(this),
                            $field = $('input, select', $param),
                            type = $field.attr('type'),
                            label = $('.label', $param),
                            value = $.trim($field.val()),
                            params = [];

                        var key = $.trim(label.text());
                        if ($field.length > 1 && value.length > 0) {

                            var i = 0;
                            $('input, select', $param).each(function () {

                                var field = $(this),
                                    val = $.trim(field.val()),
                                    type = field.attr('type');

                                if (type == 'radio' && field.is(':checked') && val.length > 0 ||
                                    type != 'radio' && val.length > 0
                                ) {

                                    params['key'] = key;
                                    params[i] = val;
                                    i++;
                                }

                            });

                            if (params.length > 0) {
                                core.push(params);
                                c++;
                            }
                        } else if (value.length > 0 && $field.length === 1) {

                            core[c] = key + ': ' + value;
                            c++;
                        }
                    });

                    for (var p = 0; p < core.length; p++) {

                        if (typeof core[p] == 'object') {

                            var key = core[p]['key'],
                                test = core[p].join(" ");

                            core[p] = key + ': ' + test;
                        }
                    }

                    $price.attr('title', core.join("<br/>"));
                    $price.tooltip();

                    $('.simple-param', $row).each(function (i) {

                        var $param = $(this),
                            data = {},
                            $field = $('input, select, textarea', $param),
                            type = $field.attr('type'),
                            label = $('.label', $param);

                        if (type == 'radio') {

                            var radio = $('input[type="radio"]:checked', $param);
                            if ($.trim(radio.val()).length > 0) {
                                data[i] = {
                                    'value': $.trim(radio.val()),
                                    'key'  : $.trim(label.text())
                                }
                            }

                        } else if (type == 'select') {

                            if ($.trim($field.val()).length > 0) {
                                data[i] = {
                                    'value': $.trim($field.val()),
                                    'key'  : $.trim(label.text())
                                }
                            }

                        } else {

                            if (typeof $field.val() != 'undefined' && $.trim($field.val()).length > 0) {
                                data[i] = {
                                    'value': $.trim($field.val()),
                                    'key'  : $.trim(label.text())
                                }
                            }
                        }

                        if (typeof data[i] != 'undefined') {

                            $overflow.append(
                                '<div class="option">' +
                                '<span title=\"' + data[i].key + '\" class="key">' + data[i].value + '</span></div>');
                        }

                        $('.option .key', $options).tooltip();
                    });

                });

            };

            options = $.extend({}, {
                'price_mode': 1
            }, options);

            if (options.price_mode === 2) {

                return validator.JBZooPriceAdvanceOverlayValidator(validator);

            } else if (options.price_mode === 1) {

                return validator.JBZooPriceAdvanceDefaultValidator(validator);
            }
        });

        return validator;
    };

})(jQuery, window, document);