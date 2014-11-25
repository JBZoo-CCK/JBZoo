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
            'max'     : 9999999,
            'decimals': 0,
            'scroll'  : true
        },
        {
            'table' : {},
            'digits': {},
            'box'   : {},
            'plus'  : {},
            'minus' : {},

            init: function () {
                this._paint();

                this.table = this.el.parents('.jsQuantityTable');
                this.item = $('.item-count', this.table);
                this.box = $('.item-count-digits', this.table);
                this.digits = $('.item-count-digits dd', this.table);

                this.plus = $('.jsAddQuantity', this.table);
                this.minus = $('.jsRemoveQuantity', this.table);

                this._setDefault();
                this.refresh();
                this._bindEvents();
            },

            add: function (e) {
                this.scroll(e, this.options.step);
            },

            remove: function (e) {
                this.scroll(e, this.options.step);
            },

            refresh: function () {
                this.refreshDigits(this.el.val());
                this.placeDigits()
            },

            refreshDigits: function (value) {

                var max = this.validate(value) + parseFloat(3 * this.options.step);

                for (var i = 0; i < 5; i++) {
                    max = max - this.options.step;

                    this.digits.eq(i).html(this.convert(max));
                }
            },

            placeDigits: function () {
                this.box.css({
                    top      : 0,
                    marginTop: -this.digits.height() * 2 + 'px'
                });
            },

            isValid: function (value) {

                if (value < this.options.min) {
                    return false;
                }

                if (value > this.options.max) {
                    return false;
                }

                return !isNaN(value);
            },

            validate: function (value) {

                if (value < this.options.min) {
                    value = this.options.min;
                }

                if (value > this.options.max) {
                    value = this.options.max;
                }

                if (isNaN(value)) {
                    value = this.options.min;
                }

                return parseFloat(value);
            },

            convert: function (value) {

                value = this.validate(value);

                return value.toFixed(this.options.decimals);
            },

            scrollError: function (e, value) {

                e.preventDefault();
                e.stopPropagation();
                var $this = this;
                if (this.processing) return;

                this.processing = true;
                var top = parseInt(thi.box.css('top')),
                    i = value > 0 ? 1 : -1;

                this.box
                    .stop()
                    .animate({
                        top: (top + ($this.digits.height() / 2 * i)) + 'px'
                    }, {
                        duration: 200,
                        complete: function () {
                            $this.box
                                .stop()
                                .animate({
                                    top: top + 'px'
                                }, {
                                    duration: 200,
                                    complete: function () {
                                        $this.processing = false;
                                    }
                                });
                        }
                    });
            },

            scroll: function (e, value) {

                e.preventDefault();
                e.stopPropagation();
                var $this = this,
                    old = this.validate(this.el.val()),
                    val = old + value,
                    i = value > 0 ? 1 : -1;

                if (!this.isValid(val)) {
                    this.scrollError(e, value);
                    return;
                }

                if (this.processing) return;

                this.processing = true;
                this.el.blur();
                this.refresh();

                this.el.trigger('change', '.jsQuantity');
                this.el.val(this.convert(val));
                this.box
                    .stop()
                    .animate({
                        top: i * $this.digits.height() + 'px'
                    }, {
                        duration: 500,
                        complete: function () {
                            $this.processing = false;
                        }
                    });
            },

            _paint: function () {
                var parent = this.el.parent();

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

                this.el.addClass('input-quantity')
                    .appendTo($('.jsQuantityTable .item-count', parent));
            },

            _bindEvents: function () {

                var $this = this;
                $('.jsAddQuantity', this.table).on('click', function (e) {

                    $this.add(e);
                    return false;
                });

                $('.jsRemoveQuantity', this.table).on('click', function (e) {

                    $this.remove(e);
                    return false;
                });

                this.el.on('change', function () {
                    $(this).val($this.convert($(this).val()));
                    $this.refresh();
                });

                if ($this.options.scroll === true) {
                    $this._bindScrollEvent();
                }

                this.el
                    .on('focus', function () {
                        $this.el.css('opacity', '1');
                        $this.box.hide();
                    }).on('keyUp', function () {
                        $this.refresh();
                    }).on('blur', function () {
                        $this.el.css('opacity', '0');
                        $this.box.show();
                    });
            },

            _bindScrollEvent: function () {

                var $this = this,
                    oldVal = this.el.val(),
                    newVal = this.el.val();
                this.item.on('mouseenter', function () {

                    oldVal = $this.el.val();
                    $this.el.focus();
                });

                this.item.on('mouseleave', function () {

                    newVal = $this.el.val();

                    if (newVal != oldVal) {
                        $this.el.trigger('change', '.jsQuantity');
                    }

                });

                this.el.on('mousewheel', function (e) {

                    e.preventDefault(e);
                    if ($this.el.is(':focus')) {

                        var value = $this.validate($this.el.val());

                        if (e.originalEvent.wheelDelta > 0) {

                            value += $this.options.step;
                            if (value > $this.options.max) {
                                value = $this.options.max;
                            }
                        } else {

                            value -= $this.options.step;
                            if (value < $this.options.min) {
                                value = $this.options.min;
                            }
                        }

                        $this.el.fadeOut(10, function () {
                            $this.el.fadeIn(10, function () {
                                $this.el.val($this.convert(value));
                                $this.refresh();
                            });
                        });
                    }
                });
            },

            _setDefault: function () {
                if (this.el.val().length === 0) {
                    this.el.val(this.convert(this.options.default));

                } else if (this.el.val().length > 0) {
                    this.el.val(this.convert(this.el.val()));

                }
            }
        }
    );

})(jQuery, window, document);