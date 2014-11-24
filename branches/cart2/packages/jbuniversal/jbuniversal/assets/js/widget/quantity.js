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

    $.fn.JBZooQuantity = function (settings) {

        return this.each(function () {

            var basic = {
                'default' : 1,
                'step'    : 1,
                'min'     : 1,
                'max'     : 9999999,
                'decimals': 0,
                'scroll'  : true
            }, options = setOptions(settings);

            function setOptions(settings) {

                if (typeof settings != 'undefined') {

                    if (typeof settings.decimals != 'undefined') {
                        settings.decimals = parseInt(settings.decimals, 10);
                    }

                    if (typeof settings.min == 'undefined' && typeof settings.default != 'undefined') {
                        settings.min = settings.default;
                    }

                    if (typeof settings.step == 'undefined' || settings.step <= 0) {
                        settings.step = basic.step;
                    }

                    return $.extend({}, basic, settings);
                }

                return basic;
            }

            var $this = $(this),
                processing = false;

            if ($this.hasClass('quantity-init')) {
                return $this;
            }

            $this.addClass('quantity-init');

            function refreshDigits(value) {

                var max = validate(value) + parseFloat(3 * options.step);

                for (var i = 0; i < 5; i++) {
                    max = max - options.step;

                    digits.eq(i).html(convert(max));
                }
            }

            function placeDigits() {

                box.css({
                    top      : 0,
                    marginTop: -digits.height() * 2 + 'px'
                });
            }

            function isValid(value) {

                if (value < options.min) {
                    return false;
                }

                if (value > options.max) {
                    return false;
                }

                return !isNaN(value);
            }

            function validate(value) {

                if (value < options.min) {
                    value = options.min;
                }

                if (value > options.max) {
                    value = options.max;
                }

                if (isNaN(value)) {
                    value = options.min;
                }

                return parseFloat(value);
            }

            function convert(value) {

                value = validate(value);

                return value.toFixed(options.decimals);
            }

            function scrollError(e, value) {

                e.preventDefault();
                e.stopPropagation();

                if (processing) return;

                processing = true;
                var top = parseInt(box.css('top')),
                    i = value > 0 ? 1 : -1;

                box
                    .stop()
                    .animate({
                        top: (top + (digits.height() / 2 * i)) + 'px'
                    }, {
                        duration: 200,
                        complete: function () {
                            box
                                .stop()
                                .animate({
                                    top: top + 'px'
                                }, {
                                    duration: 200,
                                    complete: function () {
                                        processing = false;
                                    }
                                });
                        }
                    });
            }

            function scroll(e, value) {

                e.preventDefault();
                e.stopPropagation();

                var old = validate($this.val()),
                    val = old + value,
                    i = value > 0 ? 1 : -1;

                if (!isValid(val)) {
                    scrollError(e, value);
                    return;
                }

                if (processing) return;

                processing = true;
                $this.blur();
                $this.refresh();

                $this.trigger('change').val(convert(val));
                box
                    .stop()
                    .animate({
                        top: i * digits.height() + 'px'
                    }, {
                        duration: 500,
                        complete: function () {
                            processing = false;
                        }
                    });
            }

            $this.drawBody = function () {

                var parent = $this.parent();

                $('' +
                '<table cellpadding="0" cellspacing="0" border="0" class="jsQuantityTable quantity-table">' +
                '<tr><td rowspan="2">' +
                '<div class="item-count-wrapper">' +
                '<div class="item-count">' +
                '<dl class="item-count-digits">' +
                '<dd></dd>' +
                '<dd></dd>' +
                '<dd></dd>' +
                '<dd></dd>' +
                '<dd></dd>' +
                '</dl>' +
                '</div>' +
                '</div>' +
                '</td>' +
                '<td>' +
                '<a href="#plus" class="jsAddQuantity plus btn-mini" title="Plus"></a>' +
                '</td></tr>' +
                '<tr><td>' +
                '<a href="#minus" class="jsRemoveQuantity minus btn-mini" title="Minus"></a>' +
                '</td></tr>' +
                '</table>').prependTo(parent);

                $this.addClass('input-quantity')
                    .appendTo($('.jsQuantityTable .item-count', parent));
            };

            $this.bindEvents = function () {

                $('.jsAddQuantity', table).on('click', function (e) {

                    $this.add(e);
                    return false;
                });

                $('.jsRemoveQuantity', table).on('click', function (e) {

                    $this.remove(e);
                    return false;
                });

                $this.on('change', function () {

                    $this.val(convert($this.val()));
                    $this.refresh();
                });

                if (options.scroll === true) {

                    $this.bindScrollEvent();
                }

                $this
                    .on('focus', function () {
                        $this.css('opacity', '1');
                        box.hide();
                    }).on('keyup', function () {
                        $this.refresh();
                    }).on('blur', function () {
                        $this.css('opacity', '0');
                        box.show();
                    });
            };

            $this.bindScrollEvent = function () {

                var oldVal = $this.val();
                var newVal = $this.val();
                $(item).on('mouseenter', function () {

                    oldVal = $this.val();
                    $this.focus();
                });

                $(item).on('mouseleave', function () {

                    newVal = $this.val();

                    if (newVal != oldVal) {
                        $this.trigger('change');
                    }

                });

                $this.on('mousewheel', function (e) {

                    e.preventDefault(e);
                    if ($this.is(':focus')) {

                        var value = validate($this.val());

                        if (e.originalEvent.wheelDelta > 0) {

                            value += options.step;
                            if (value > options.max) {
                                value = options.max;
                            }

                        } else {

                            value -= options.step;
                            if (value < options.min) {
                                value = options.min;
                            }

                        }

                        $this.fadeOut(10, function () {

                            $this.fadeIn(10, function () {

                                $this.val(convert(value));
                                $this.refresh();
                            });
                        });
                    }
                });
            };

            $this.setDefault = function () {

                if ($this.val().length === 0) {

                    $this.val(convert(options.default));

                } else if ($this.val().length > 0) {

                    $this.val(convert($this.val()));

                }
            };

            $this.refresh = function () {

                refreshDigits($this.val());
                placeDigits();
            };

            $this.add = function (e) {

                scroll(e, options.step);
            };

            $this.remove = function (e) {

                scroll(e, -options.step);
            };

            $this.drawBody();

            var table = $this.parents('.jsQuantityTable'),
                item = $('.item-count', table),
                digits = $('.item-count-digits dd', table),
                box = $('.item-count-digits', table),
                plus = $('.jsAddQuantity', table),
                minus = $('.jsRemoveQuantity', table);

            $this.setDefault();
            $this.refresh();
            $this.bindEvents();
        });
    };

})(jQuery, window, document);