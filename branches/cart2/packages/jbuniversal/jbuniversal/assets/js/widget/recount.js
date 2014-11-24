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

    $.fn.reCount = function (value, settings) {

        var options = $.extend({}, {
                decimals : 2,
                point    : '.',
                separator: " ",
                currency : 'EUR',
                duration : 250
            }, settings),
            object = $(this);

        $(this).each(function () {

            var $this = $(this);

            object.add = function (value) {

                var old = object.getValue();

                add(old, value, options.duration, true);

            };

            function add(old, value, duration, repeat) {

                $({value: old}).animate({
                        value: parseFloat(object.clean(value))
                    },

                    {
                        step: function (now, fx) {

                            var val = parseInt(this.value, 10);
                            if (options.decimals > 0) {
                                val = object.toFormat(parseFloat(this.value));
                            }

                            setValue(val);

                            var percentageDone = (fx.now - fx.start) / (fx.end - fx.start),
                                done = parseFloat(percentageDone).toFixed(2);

                            if (done > 0.95 && repeat === true) {

                                $(fx.elem).stop();

                                duration += duration * 2;

                                add(this.value, value, duration);
                            }
                        },

                        complete: function () {

                            setValue(value);
                            object.process = false;
                        },
                        duration: duration
                    }
                )
            }

            object.getValue = function () {

                var value = 0;
                if (typeof $this.attr('type') == 'undefined') {

                    value = $this.text();
                    value = this.clean(value);

                }

                return value;
            };

            object.clean = function (value) {

                value = value + "";
                var val = value.toString().replace(/[\s]/g, "");
                val = parseInt(val, 10);

                return val;
            };

            object.toFormat = function (number, decimals, point, separator) {

                if (isNaN(number)) {
                    return (null);
                }

                point = point ? point : options.point;
                separator = separator ? separator : options.separator;
                decimals = decimals ? decimals : options.decimals;

                number = new String(number);
                number = number.split('.');

                if (separator) {

                    var tmpNumber = new Array();

                    for (var i = number[0].length, j = 0; i > 0; i -= 3) {
                        var pos = i > 0 ? i - 3 : i;
                        tmpNumber[j++] = number[0].substring(i, pos);
                    }

                    number[0] = tmpNumber.reverse().join(separator);
                }

                if (decimals) {

                    number[1] = number[1] ? number[1] : '';
                    number[1] = Math.round(parseFloat(number[1].substr(0, decimals) + '.' + number[1].substr(decimals, number[1].length), 10));

                    if (isNaN(number[1])) {
                        number[1] = '';
                    }

                    var k = decimals - number[1].toString().length;

                    for (var i = 0; i < k; i++) {
                        number[1] += '0';
                    }
                }

                return (number.join(point));
            };

            function setValue(value) {

                //this.element.innerHTML = value;
                //value = parseFloat(value);
                if (typeof $this.attr('type') == 'undefined') {
                    $this.html(' ' + value + ' ');
                }

                //this.element.val(value);

                return $this;
            }

            object.add(value);
        });

        return object;
    };

})(jQuery, window, document);