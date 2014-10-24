/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */


/**
 * JBZoo JSLibHelper
 * @constructor
 */
var JBZooHelper = function () {

    var $this = this,
        $ = jQuery;

    $this.DEBUG = false; // general debug flag

    /**
     * Set number format (as PHP function)
     * @param number
     * @param decimals
     * @param point
     * @param separator
     * @returns {*}
     */
    $this.numberFormat = function (number, decimals, point, separator) {

        if (isNaN(number)) {
            return(null);
        }

        point = point ? point : '.';
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

        return(number.join(point));
    }

    /**
     * Event logger to browser console
     * @param type String
     * @param message String
     * @param vars mixed
     */
    $this.logger = function (type, message, vars) {

        if (!$this.DEBUG || typeof console == 'undefined') {
            return false;
        }

        var postfix = "\t\t\t\tvars:";

        if (type == 'e') { // error
            vars !== undefined ? console.error(message + postfix, vars) : console.error(message);

        } else if (type == 'w') { // warning
            vars !== undefined ? console.warn(message + postfix, vars) : console.warn(message);

        } else if (type == 'i') { // information
            vars !== undefined ? console.info(message + postfix, vars) : console.info(message);

        } else if (type == 'l') { // log
            vars !== undefined ? console.log(message + postfix, vars) : console.log(message);

        } else {
            vars !== undefined ? console.log(message + postfix, vars) : console.log(message);
        }
    }

    /**
     * Ajax call
     * @param options = {
     *      'url'     : 'index.php?format=raw&tmpl=component',
     *      'data'    : {},
     *      'dataType': 'json',
     *      'success' : false,
     *      'error'   : false,
     *      'onFatal' : function () {}
     *  }
     */
    $this.ajax = function (options) {

        $this.logger('w', 'ajax::request', options);

        var options = $.extend({}, {
            'url'     : 'index.php?format=raw&tmpl=component',
            'data'    : {},
            'dataType': 'json',
            'success' : false,
            'error'   : false,
            'onFatal' : function (responce) {
                if ($this.DEBUG) {
                    $this.logger('e', 'ajax::request - ' + options.url, options.data);

                    // parse exeption message
                    var $nodes = $.parseHTML(responce.responseText),
                        exceptionMessage = '';

                    if (!JBZoo.empty($nodes)) {
                        $.each($nodes, function (n, obj) {
                            if ($(obj).find('#techinfo').length == 1) {
                                exceptionMessage = $.trim($(obj).find('#techinfo').text());
                                return false;
                            }
                        });
                    }

                    if (exceptionMessage) {
                        jbdump(exceptionMessage, 'Ajax error responce:');
                    } else {
                        jbdump(responce.responseText, 'Ajax error responce:');
                    }
                }

                throw "Ajax response no parse"
            }
        }, options);

        if (!options.url) {
            throw "AJAX url is no set!";
        }

        // set default request data
        options.data = $.extend({}, {
            'nocache': Math.random(),
            'option' : 'com_zoo',
            'tmpl'   : 'component',
            'format' : 'raw'
        }, options.data);

        $.ajax({
            'url'     : options.url,
            'data'    : options.data,
            'dataType': options.dataType,
            'type'    : 'POST',
            'success' : function (data) {

                if (typeof data == 'string') {
                    data = $.trim(data);
                }

                if (options.dataType == 'json') {
                    //$this.logger('i', 'ajax::responce', {'result': data.result, 'message': data.message});

                    if (data.result && $.isFunction(options.success)) {
                        options.success.apply(this, arguments);
                    } else if (!data.result && $.isFunction(options.error)) {
                        options.error.apply(this, arguments);
                    }

                } else if ($.isFunction(options.success)) {
                    options.success.apply(this, arguments);
                }

            },
            'error'   : options.onFatal,
            'cache'   : false,
            'headers' : {
                "cache-control": "no-cache"
            }
        });
    }

    /**
     * Check is variable empty
     * @link http://phpjs.org/functions/empty:392
     * @param mixedVar
     * @return {Boolean}
     */
    $this.empty = function (mixedVar) {

        // check simple var
        if (typeof mixedVar === 'undefined'
            || mixedVar === ""
            || mixedVar === 0
            || mixedVar === "0"
            || mixedVar === null
            || mixedVar === false
            ) {
            return true;
        }

        // check object
        if (typeof mixedVar == 'object') {
            if ($this.countProps(mixedVar) == 0) {
                return true
            }
        }

        return false;
    }


    /**
     * Count object properties
     * @param object
     * @return {Number}
     */
    $this.countProps = function (object) {

        var count = 0;

        for (var property in object) {

            if (object.hasOwnProperty(property)) {
                count++;
            }
        }

        return count;
    }

    /**
     * Backtrace for debug
     * Function may use dump function for show backtrace as string
     * Work only if environment is "development"
     * @param asString
     */
    $this.trace = function (asString) {

        if (!$this.DEBUG || typeof console == 'undefined') {
            return false;
        }

        if ($this.empty(asString)) {
            asString = false;
        }

        var getStackTrace = function () {
            var obj = {};
            Error.captureStackTrace(obj, getStackTrace);
            return obj.stack;
        };

        if (asString) {
            jbdump(getStackTrace(), 'trace', false);
        } else {
            if (typeof console != 'undefined') {
                console.trace();
            }
        }
    }

    /**
     * Die script
     * @param dieMessage
     */
    $this.die = function (dieMessage) {

        if (!$this.DEBUG) {
            return false;
        }

        if ($this.empty(dieMessage)) {
            dieMessage = ' -= ! DIE ! =-';
        }

        throw dieMessage;
    }

    /**
     * Check is value in array
     * @param needle
     * @param haystack
     * @param strict
     * @return {Boolean}
     */
    $this.in_array = function (needle, haystack, strict) {

        var found = false, key;

        strict = !!strict;

        for (key in haystack) {
            if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
                found = true;
                break;
            }
        }

        return found;
    }

    /**
     * Alias for console log + backtrace
     * For debug only
     * Work only if environment is "development"
     * @param vars mixed
     * @param name String
     * @param showTrace Boolean
     * @return {Boolean}
     */
    $this.jbdump = function (vars, name, showTrace) {

        // get type
        if (typeof vars == 'string' || typeof vars == 'array') {
            var type = ' (' + typeof(vars) + ', ' + vars.length + ')';
        } else {
            var type = ' (' + typeof(vars) + ')';
        }

        // wrap in vars quote if string
        if (typeof vars == 'string') {
            vars = '"' + vars + '"';
        }

        // get var name
        if (typeof name == 'undefined') {
            name = '...' + type + ' = ';
        } else {
            name += type + ' = ';
        }

        // is show trace in console
        if (typeof showTrace == 'undefined') {
            showTrace = false;
        }

        // dump var
        console.log(name, vars);

        // show console
        if (showTrace && typeof console.trace != 'undefined') {
            console.trace();
        }

        return true;
    }

};

var reCount = {

    options : {
        decimals : 2,
        point    : '.',
        separator: " ",
        currency : 'EUR',
        duration : 1000
    },
    process : false,
    element : null,
    elements: [],
    hash    : null,

    add: function (element, value, options) {

        var object = this;

        object.setOptions(options);

        object.element = element;

        var settings = object.options;

        var old = object.getValue(element);
        object.process = true;


        jQuery({ value: old.value })
            .animate({ value: value.noformat }, {
                easing: 'swing',
                step  : function () {

                    var val = parseInt(this.value, 10);
                    if (settings.decimals > 0) {
                        val = object.toFormat(parseFloat(this.value));
                    }

                    jQuery(element).text(' ' + val + ' ');
                },

                complete: function () {
                    jQuery(element).text(value.total);
                    object.process = false;
                },
                duration: settings.duration
            });

        /*jQuery({value: old.value})
         .animate({
         value: parseFloat(value.noformat)
         },
         {
         step: function (now, fx) {

         //console.log(now);
         //console.log(durationDone);

         //console.log(rest);

         var val = parseInt(this.value, 10);
         if (settings.decimals > 0) {
         val = object.toFormat(parseFloat(this.value));
         }

         object.setValue(val);
         },

         complete: function () {

         object.setValue(value.total);
         object.process = false;
         },
         duration: settings.duration
         }
         );*/

        return this;
    },

    getValue: function (element) {

        var value = 0;
        if (typeof element.attr('type') == 'undefined') {
            value = element.text();

            value = this.clean(value);

        }

        return value;
    },

    setValue: function (value) {

        //this.element.innerHTML = value;
        //value = parseFloat(value);
        if (typeof this.element.attr('type') == 'undefined') {
            this.element.text(value);
        }

        //this.element.val(value);

        return this;
    },

    clean: function (value) {

        var val = value.replace(/[\s]/g, "");

        val = parseInt(val, 10);

        var result = {
            value: val,
            total: this.toFormat(val)
        };

        return  result;
    },

    toFormat: function (number, decimals, point, separator) {

        if (isNaN(number)) {
            return(null);
        }

        point = point ? point : this.options.point;
        separator = separator ? separator : this.options.separator;
        decimals = decimals ? decimals : this.options.decimals;

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

        return(number.join(point));
    },

    setOptions: function (options) {

        for (var key in options) {
            if (this.options.hasOwnProperty(key)) {
                if (options[key].length > 0) {
                    this.options[key] = options[key];
                }
            }
        }

        return this;
    },

    trash: function () {
        this.element = null;
        this.options = {};
    }

};

/**
 * JBZoo UI jQuery plugins
 */
(function ($) {

    /**
     * Empty cart action
     */
    $(document).on('click', '.jbzoo .jsEmptyCart', function () {

        JBZoo.ajax({
            'url'    : $(this).attr('href'),
            'success': function () {
                $.fn.JBZooPriceReloadBasket();
            }
        });

        return false;
    });

    /**
     * Goto link by button click
     */
    $(document).on('click', '.jbzoo .jsGoto', function () {
        var url = $(this).attr('href');
        if (!url) {
            url = $(this).data('href');
        }

        parent.location.href = url;
        return false;
    });

    /**
     * Check is numeric
     * @param mixedVar
     * @return {Boolean}
     */
    function isNumeric(mixedVar) {
        return (typeof(mixedVar) === 'number' || typeof(mixedVar) === 'string') && mixedVar !== '' && !isNaN(mixedVar);
    }

    /**
     * JBZoo tabs widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooTabs = function (options) {

        function getAnchor(link) {
            return link.match(/#(.*)$/);
        }

        var options = $.extend({}, {
            'onTabShow': false,
            'indexTab' : 0
        }, options);

        return $(this).each(function () {

            // init vars, links to DOM objects
            var $element = $(this),
                $widgetHeader = $element.children('ul'),
                $widgetList = $widgetHeader.children('li'),
                $widgetLinks = $widgetList.children('a'),
                $widgetContent = $element.children('div');

            if ($element.hasClass('jbzootabs-widget')) {
                return true;
            } else {

                $element.addClass('jbzootabs jbzootabs-widget jbzootabs-widget-cont');
                $widgetLinks.addClass('jbzootabs-anchor');
                $widgetHeader.addClass('jbzootabs-nav jbzootabs-header');
                $widgetList.addClass('jbzootabs-state-default');
                $widgetContent.addClass('jbzootabs-content');

                $widgetContent.hide();

                $widgetList.hover(function () {
                    $(this).addClass('jbzootabs-state-hover');
                }, function () {
                    $(this).removeClass('jbzootabs-state-hover');
                });

                /**
                 * Click action for tabs
                 */
                $widgetLinks.bind('click', function () {

                    var result = $(this, $element).attr('href'),
                        link = getAnchor(result);

                    $widgetContent.hide();
                    $(link[0], $element).show();

                    $widgetList.removeClass('jbzootabs-active jbzootabs-state-active');

                    $(this).parent().addClass('jbzootabs-active jbzootabs-state-active');

                    if ($.isFunction(options.onTabShow)) {
                        var index = $($widgetList, $element).index($('.jbzootabs-active', $element));
                        options.onTabShow(index);
                    }

                    return false;
                });

                // init widget tab
                (function () {

                    var link = getAnchor(window.location.href);

                    if (link && link[1]) {

                        var loc = window.location.hash,
                            index = $widgetContent.siblings().not($widgetHeader).index($(loc, $element));

                        if (index > 0) {
                            $(loc, $element).show();
                            $widgetList.eq(index).addClass('jbzootabs-active jbzootabs-state-active');

                        } else {
                            $widgetList.eq(options.indexTab).addClass('jbzootabs-active jbzootabs-state-active');
                            $widgetContent.first().show();
                        }

                    } else if (options.indexTab > 0) {
                        $widgetContent.not($widgetHeader).eq(options.indexTab).show();
                        $widgetList.eq(options.indexTab).addClass('jbzootabs-active jbzootabs-state-active');

                    } else {
                        $widgetList.eq(options.indexTab).addClass('jbzootabs-active jbzootabs-state-active');
                        $widgetContent.first().show();
                    }
                }());
            }

        });
    };

    /**
     * JBZoo accordion
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooAccordion = function (options) {

        var options = $.extend({}, {
            'onTabShow'    : false,
            'headerWidget' : 'h3',
            'contentWidget': 'div',
            'activeTab'    : 0
        }, options);

        return $(this).each(function () {

            // init vars, links to DOM objects
            var $element = $(this);

            if ($element.hasClass('jbzootabs-accordion')) {
                return true;
            } else {
                if (options.headerWidget == 'h3') {
                    var $content = $element.children(options.contentWidget),
                        $header = $element.children(options.headerWidget);
                } else {
                    var $content = $element.children(options.contentWidget + ':odd'),
                        $header = $element.children('div:even');
                }

                $content.hide();

                $header.hover(
                    function () {
                        $(this).addClass('jbzootabs-state-hover');
                    },
                    function () {
                        $(this).removeClass('jbzootabs-state-hover');
                    }
                );

                $($element).addClass('jbzootabs-accordion');
                $($header).addClass('jbzootabs-accordion-header jbzootabs-state-default jbzootabs-accordion-icons');
                $($header).append('<span class="jbzootabs-accordion-header-icon jbzootabs-icon jbzootabs-icon-closed"></span>');
                $($content).addClass('jbzootabs-accordion-content');

                /**
                 * Click action for accordion header
                 */
                $header.bind('click', function () {

                    var $contActive = $(this, $element).next(),
                        $span = $(this, $element).find('.jbzootabs-accordion-header-icon'),
                        $allSpan = $header.find('.jbzootabs-accordion-header-icon');

                    $header.removeClass('jbzootabs-accordion-active jbzootabs-state-active');
                    $allSpan.removeClass('jbzootabs-icon-opened');
                    $($content).slideUp('normal');

                    if ($($contActive).is(":hidden")) {
                        $(this, $element).addClass('jbzootabs-accordion-active');
                        $span.addClass('jbzootabs-icon-opened');
                        $($contActive).slideDown('normal');
                    }

                    if ($.isFunction(options.onTabShow)) {
                        index = $header.index($('.jbzootabs-accordion-active', $element));

                        var map = $('.googlemaps').children('div').first();

                        map.data('Googlemaps').refresh();
                    }

                });

                function initAccordion() {
                    $header.eq(options.activeTab).addClass('jbzootabs-accordion-active jbzootabs-state-active');
                    $allSpan = $header.find('.jbzootabs-accordion-header-icon');
                    $allSpan.eq(options.activeTab).addClass('jbzootabs-icon-opened');
                    $content.eq(options.activeTab).slideDown('normal');
                }

                initAccordion();
            }
        });
    };

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
                    return(null);
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

                return(number.join(point));
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

    /**
     * JBZoo Basket widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooBasket = function (options) {

        var options = $.extend({}, {}, options),
            shipping = $('.jbzoo .shipping-list').JBCartShipping({
                'no_value_message': 'Free'
            });

        return $(this).each(function () {

            var $obj = $(this);

            //Another effect is to add the price in cart
            function reCountEffect(elem, value) {

                $(elem)
                    .animate({
                        opacity: 0.3
                    },
                    {
                        duration: 1000,
                        start   : function () {
                            $(this).addClass('jsSizeMedium');
                        },

                        complete: function () {
                            $(this)
                                .css('opacity', 1)
                                .removeClass('jsSizeMedium');
                        }
                    }
                );
            }

            function addLoading() {
                $obj.addClass('loading', 100);

                $('input, select, textarea', $obj).attr('disabled', 'disabled');
            }

            function removeLoading() {
                $obj.removeClass('loading', 100);
                $('input, select, textarea', $obj).removeAttr('disabled');
            }

            function morphology(num, prfxs) {
                prfxs = prfxs || ['', 'а', 'ов'];
                num = '' + num;

                if (num.match(/^(.*)(11|12|13|14|15|16|17|18|19)$/)) {
                    return prfxs[2];
                }
                if (num.match(/^(.*)1$/)) {
                    return prfxs[0];
                }
                if (num.match(/^(.*)(2|3|4)$/)) {
                    return prfxs[1];
                }
                if (num.match(/^(.*)$/)) {
                    return prfxs[2]
                }

                return prfxs[0];
            }

            // recount basket
            var recount = function (data) {
                for (var key in data['prices'].items) {

                    var subTotal = data['prices'].items[key],
                        row = $('.row-' + key),
                        elem = $('.row-' + key + ' .jsSubtotal .jsValue', $obj);

                    (elem).reCount(subTotal.total, {
                        decimals: 2
                    });
                }

                var count = $('.jsTotalCount .jsValue', $obj),
                    total = $('.jsTotalPrice .jsValue', $obj),
                    morph = $('.jsMorphology', $obj),
                    word = morph.data('word');

                morph.html(word + morphology(data.count));

                $(count).reCount(data['prices'].count, {
                    'decimals': 1,
                    'duration': 100
                });

                $(total).reCount(data['prices'].total);
                shipping.setPrices(data['shipping']);
            };

            function deleteItem($button) {
                addLoading();
                var itemid = $button.closest('tr').data('itemid'),
                    key = $button.closest('tr').data('key');

                JBZoo.ajax({
                    'url'    : options.deleteUrl,
                    'data'   : {
                        'itemid'  : itemid,
                        'key'     : key,
                        'shipping': shipping.getParams()
                    },
                    'success': function (data) {
                        var $row = $button.closest('tr');
                        $row.slideUp(300, function () {
                            $row.remove();
                            if ($obj.find('tbody tr').length == 0) {
                                window.location.reload();
                            }
                        });

                        recount(data);
                        $.fn.JBZooPriceReloadBasket();
                        removeLoading();
                    },
                    'error'  : function (error) {
                        removeLoading();
                    }
                });
            }

            // remove one item
            $('.jsDelete', $obj).click(function () {
                deleteItem($(this));
            });

            // remove all
            $('.jsDeleteAll', $obj.parents('.jbzoo')).click(function () {

                if (confirm(options.clearConfirm)) {
                    JBZoo.ajax({
                        'url'    : options.clearUrl,
                        'success': function () {
                            window.location.reload();
                        }
                    });
                }
            });

            $('.jsQuantity', $obj).JBZooQuantity({
                'default' : 1,
                'step'    : 1,
                'decimals': 1
            });
            // quantity
            var $quantity = $('.jsQuantity', $obj),
                lastQuantityVal = $quantity.val(),

                changeCallback = function ($input) {

                    var value = parseFloat($input.val()),
                        tr = $input.parents('.jsQuantityTable').closest('tr'),
                        itemid = parseInt(tr.data('itemid'), 10),
                        key = tr.data('key');

                    if ($input.val().length && value > 0) {
                        lastQuantityVal = value;
                        JBZoo.ajax({
                            'url'    : options.quantityUrl + '&' + shipping.getParams(),
                            'data'   : {
                                'value': value,
                                'key'  : key
                            },
                            'success': function (data) {
                                recount(data);
                                $.fn.JBZooPriceReloadBasket();
                            },
                            'error'  : function (data) {
                                if (data.message) {
                                    alert(data.message);
                                }
                            }
                        });

                    }
                },
                changeTimer = 0,
                timeoutCallback = function () {
                    var $input = $(this);
                    clearTimeout(changeTimer);
                    changeTimer = setTimeout(function () {
                        changeCallback($input);
                    }, 200);
                };
            /*
             $quantity
             .keyup($.proxy(timeoutCallback, $quantity))
             .change($.proxy(timeoutCallback, $quantity));
             */

            $('.jsQuantity', $obj)
                .keyup(function () {
                    var $input = $(this);
                    clearTimeout(changeTimer);
                    changeTimer = setTimeout(function () {
                        changeCallback($input);
                    }, 100);
                })
                .change(function () {
                    var $input = $(this);
                    clearTimeout(changeTimer);
                    changeTimer = setTimeout(function () {
                        changeCallback($input);
                    }, 100);
                });
        });
    };

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
                    .on('focus',function () {
                        $this.css('opacity', '1');
                        box.hide();
                    }).on('keyup',function () {
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

    /**
     * JBZoo Compare widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBCompareButtons = function (options) {

        var options = $.extend({}, {
        }, options);

        return $(this).each(function () {

            var $compare = $(this);

            if ($compare.hasClass('jbcompare-init')) {
                return true;
            }

            $compare.addClass('jbcompare-init');

            $('.jsCompareToggle', $compare).click(function () {

                var $toggle = $(this);

                JBZoo.ajax({
                    'url'    : $toggle.attr("href"),
                    'success': function (data) {
                        if (data.status) {
                            $compare.removeClass('unactive').addClass('active');

                        } else {
                            if (data.message) {
                                alert(data.message);
                            }

                            $compare.removeClass('active').addClass('unactive');
                        }
                    }
                });

                return false;
            });

        });
    };

    /**
     * JBZoo Favorite widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBFavoriteButtons = function (options) {

        var options = $.extend({}, {
        }, options);

        return $(this).each(function () {

            var $favorite = $(this);

            if ($favorite.hasClass('jbfavorite-init')) {
                return true;
            }

            $favorite.addClass('jbfavorite-init');

            $('.jsFavoriteToggle', $favorite).click(function () {

                var $toggle = $(this);

                JBZoo.ajax({
                    'url'    : $toggle.attr("href"),
                    'success': function (data) {

                        if (data.status) {
                            $favorite.removeClass('unactive').addClass('active');
                        } else {
                            if (data.message) {
                                alert(data.message);
                            }

                            $favorite.removeClass('active').addClass('unactive');
                        }
                    }
                });

                return false;
            });

            $('.jsJBZooFavoriteRemove', $favorite).click(function () {
                var $toggle = $(this);

                JBZoo.ajax({
                    'url'    : $toggle.attr("href"),
                    'success': function (data) {
                        if (data.result) {
                            $favorite.slideUp(function () {
                                $favorite.remove();
                                if ($('.favorite-item-wrapper').length == 0) {
                                    $('.jsJBZooFavoriteEmpty').fadeIn();
                                }
                            });
                        }
                    }
                });

                return false;
            });

        });
    };

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
                        $parent = $field.parents('.simple-param'),
                        $row = $field.parents('.jbpriceadv-variation-row');

                    $this.super.disableParams($row);
                    $this.super.setStatus($parent);

                    if ($('.simple-param.active', $row).length == 0) {
                        $this.super.activateParams($row);
                    }

                    $this.super.clearStyles();
                    $this.super.insertOptions();
                    global.showErrors();
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

                } else if (type == 'text') {
                    value = $.trim($field.val());

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
                    $('.variation-label .jsAttention', $row).removeClass('error');
                    $('.variation-label .jsAttention', $row).tooltip('destroy');
                    $('.simple-param', $row).each(function () {

                        var $param = $(this);
                        $('.jsJBPriceAttention', $param).removeClass('error');
                        $('.jsJBPriceAttention', $param).removeClass('disabled');
                        $('.jsJBPriceAttention', $param).tooltip('destroy');
                    });
                });
            };

            validator.activateParam = function ($param) {

                $param.removeClass('disabled');
                $param.addClass('active');
                $('.jsJBPriceAttention', $param).removeClass('disabled');
                $('.jsJBPriceAttention', $param).tooltip('destroy');

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

                    var $row = $(this),
                        $variantLabel = $('.variation-label', $row);
                    if (n == 0) {
                        $('.jbremove', $row).hide();
                    }

                    $('.list-num', $row).text(n + 1);

                    if (!$('.row-sku', $row).val() && options.base_sku) {
                        $('.row-sku', $row).val(options.base_sku);
                    }

                    if ($('.row-balance', $row).val() == '') {
                        $('.row-balance', $row).val('-1');
                    }

                    $('input[type=text], input[type=checkbox], select, textarea', $row).each(function () {
                        var $control = $(this);
                        $control.attr('name', $control.attr('name').replace(/\[variations\]\[\d\]/i, '[variations][' + n + ']'));

                    });

                    $('input[type="radio"]', $row).each(function () {
                        var $this = $(this);

                        $this.attr('name', $this.attr('name').replace(/\[variations\-\d*\]\[\d\]/i, '[variations][' + n + ']'));
                        if ($this.is(':checked') == true) {
                            $this.attr('checked', 'checked');
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
                    $('.jbcolor-input', jbColor).unbind();

                    jbColor.JBColorHelper({
                        "multiple": false
                    });
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

    $.fn.initJBPriceAdvImage = function () {
        var url = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

        var $form = $('form.item-edit');

        return $('.jbprice-img-row-file', $form).each(function (n) {

            var $this = $(this);

            if ($this.hasClass('JBPriceImage-init')) {
                return $this;
            }

            $this.addClass('JBPriceImage-init');
            var $jsJBPriceImage = $('.jsJBPriceImage', $this),
                id = "jsJBPriceImage-" + n,
                $selectButton = $('<button type="button" class="jbprice-img-button" />').text("Select Image").insertAfter($jsJBPriceImage),
                $cancelSelect = $('<span class="jbprice-img-cancel image-cancel"/>').insertAfter($jsJBPriceImage);

            $jsJBPriceImage.attr("id", id);

            $cancelSelect.click(function () {
                $cancelSelect.prev().val("");
            });

            $selectButton.click(function (event) {
                event.preventDefault();

                SqueezeBox.fromElement(this, {
                    handler: "iframe",
                    url    : "index.php?option=com_media&view=images&tmpl=component&e_name=" + id,
                    size   : {x: 850, y: 500}
                });
            });
            var func = 'insertJBPriceImage' + id;
            if ($.isFunction(window.jInsertEditorText)) {
                window[func] = window.jInsertEditorText;
            }

            window.jInsertEditorText = function (c, a) {

                if (a.match(/^jsJBPriceImage-/)) {

                    var $element = $("#" + a),
                        value = c.match(/src="([^\"]*)"/)[1];

                    $element.parent()
                        .find("img")
                        .attr("src", url + value);

                    $element.val(value);

                } else {
                    $.isFunction(window[func]) &&
                    window[func](c, a);
                }

            };

        })

    };

    /**
     * Plugin constructor
     * @param options
     * @returns {*|HTMLElement}
     * @constructor
     */
    $.fn.JBCascadeSelect = function (options) {

        /**
         * Private methods and properties
         * @private
         */
        var $this = $(this),
            _options = {
                'uniqid'  : '',
                'items'   : null,
                'text_all': 'All'
            },
            _selects = {},
            _init = function ($element, groupNum) {

                _selects[groupNum] = $('select', $element);
                _selects[groupNum]
                    .change(function () {
                        var $select = $(this),
                            listOrder = parseInt($select.attr('list-order'), 10),
                            value = $select.val(),
                            parentValues = _parentValues(listOrder, groupNum),
                            $selectNext = $('.jbselect-' + (listOrder + 1), $element);

                        _fill($selectNext, value, parentValues, listOrder, false);

                        if ($selectNext.find('option').length > 1) {
                            _enable($selectNext);
                        }

                        $selectNext.trigger('change');
                    })
                    .each(function (n, obj) {
                        var $select = $(obj),
                            listOrder = parseInt($select.attr('list-order'), 10),
                            value = $select.val(),
                            parentValues = _parentValues(listOrder, groupNum);

                        _disable($select);
                        if (!_checkValue(value)) {
                            _enable($select);
                        }

                        if ($select.find('option').length > 1) {
                            _enable($select);
                        }
                    });
            },
            _fill = function ($select, value, parentValues, listOrder, force) {

                var tempList = _options.items;

                _clear($select);

                if (!force) {
                    $.each(parentValues, function (n, obj) {

                        if (typeof tempList[obj] != 'undefined') {
                            tempList = tempList[obj];
                        } else if (!_checkValue(obj)) {
                            return false;
                        } else {
                            tempList = {};
                            return false;
                        }
                    });
                }

                $.each(tempList, function (n, obj) {
                    _addOption($select, n, n);
                });

            },
            _parentValues = function (listOrder, n) {
                var result = {};

                for (var i = 0; i <= listOrder; i++) {
                    var val = $(_selects[n].get(i)).val();
                    result[i] = val;
                }

                return result;
            },
            _checkValue = function (value) {

                if (typeof value == 'undefined') {
                    return false;
                }

                return !$.inArray(value, ['', ' ', '0']);
            },
            _clear = function ($select) {
                $select.empty();
                _disable($select);
                return _addOption($select, '', _options.text_all);
            },
            _disable = function ($select) {
                $select.attr('disabled', 'disabled');
            },
            _enable = function ($select) {
                $select.removeAttr('disabled');
            },
            _addOption = function ($select, key, value) {
                var $option = $('<option>').attr('value', key).html(value);
                return $select.append($option);
            };

        ////// plugin init
        if (!$this.length) {
            return $this;
        }

        _options = $.extend({}, _options, options);

        $('.jbcascadeselect', $this).each(function (n, obj) {
            _init($(obj), n);
        });

        // init new dinamic add selects
        var $parent = $('.repeat-elements', $this);
        $parent.find('p.add').bind('click', function () {

            var newIndex = $parent.find("li.repeatable-element").length - 1,
                $newObj = $this.find('.jbcascadeselect:eq(' + newIndex + ')');

            $('select', $newObj).each(function (n, obj) {
                if (n != 0) {
                    _clear($(obj));
                } else {
                    $(obj).val('');
                }
            });
            _init($newObj, newIndex);
        });

        return $this;
    };

    /**
     * JBZoo Price (depricated!)
     * @depricated
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooPrice = function (options) {

        var $this = $(this);

        return $this.each(function (n, obj) {

            var $obj = $(obj);

            $(".jsPriceCurrency", $obj).click(function () {
                var $cur = $(this),
                    currency = $cur.attr("currency");

                $(".jsPriceValue", $obj).removeClass("active");
                $(".price-currency-" + currency, $obj).addClass("active");

                $(".jsPriceCurrency", $obj).removeClass("active");
                $cur.addClass("active");
            });

            $(".jsAddToCart", $obj).click(function () {
                var $link = $(this),
                    href = $link.data('href'),
                    params = 'format=raw&tmpl=component';

                // force added params (sef bug)
                if (href.indexOf('?') == -1) {
                    href += '?' + params;
                } else {
                    href += '&' + params;
                }

                $.fancybox({
                    'type'      : 'iframe',
                    'href'      : href,
                    'width'     : 360,
                    'autoHeight': true,
                    'autoResize': true,
                    'fitToView' : true,
                    'iframe'    : {
                        'scrolling': 'no',
                        'preload'  : true
                    },
                    'helpers'   : {
                        'overlay': {
                            'locked': false,
                            'css'   : {
                                'background': 'rgba(119, 119, 119, 0.4)'
                            }
                        }
                    }
                });

                return false;
            });

            // order in one click
            $('.jsBayIt', $obj).click(function () {

                var $link = $(this),
                    indexPrice = 0;

                if ($('.jbprice-row input:checked', $obj).length) {
                    indexPrice = $('.jbprice-row input:checked', $obj).val();
                }

                JBZoo.ajax({
                    'url'    : $link.data('href'),
                    'data'   : {
                        "args": {
                            'quantity'  : $('.jsQuantity').val(),
                            'indexPrice': indexPrice
                        }
                    },
                    'success': function (data) {
                        if (data.result) {
                            window.location.href = data.basketUrl;
                        }
                    }
                });

                return false;
            });

            $(".jsRemoveFromCart", $obj).click(function () {
                var $link = $(this);

                JBZoo.ajax({
                    'url'    : $link.data("href"),
                    'success': function (data) {
                        $obj.removeClass('in-cart').addClass('not-in-cart');
                        $.fn.JBZooPriceReloadBasket();
                    }
                });

                return false;
            });
        });
    };

    /**
     * JBZoo JBPrice Toggler (deprecated!)
     * @deprecated
     * @param elementId
     * @param itemId
     * @constructor
     */
    $.fn.JBZooPriceToggle = function (elementId, itemId) {
        var $priceObj = $('.jsPrice-' + elementId + '-' + itemId + ', .jsJBPriceAdvance-' + elementId + '-' + itemId);
        $priceObj.removeClass('not-in-cart').addClass('in-cart');
        $.fn.JBZooPriceReloadBasket();
    };

    /**
     * JBZoo JBPrice Cart reloader (deprecated!)
     * @deprecated
     * @constructor
     */
    $.fn.JBZooPriceReloadBasket = function () {

        $('.jsJBZooModuleBasket').each(function (n, obj) {

            var $obj = $(obj);

            JBZoo.ajax({
                'data'    : {
                    'controller': 'basket',
                    'task'      : 'reloadModule',
                    'app_id'    : $obj.attr('appId'),
                    'moduleId'  : $obj.attr('moduleId')
                },
                'dataType': 'html',
                'success' : function (data) {
                    $obj.closest('.jbzoo').replaceWith(data);
                }
            })
        });
    };

    /**
     * JBZoo Progress bar
     * @param options
     * @constructor
     */
    $.fn.JBZooProgressBar = function (options) {

        function timeFormat(seconds) {

            if (seconds <= 0 || isNaN(seconds)) {
                return '00:00';
            }

            var formatedMin = Math.floor(seconds / 60),
                formatedSec = seconds % 60;

            if (formatedSec < 10) {
                formatedSec = '0' + formatedSec;
            }

            if (formatedMin < 10) {
                formatedMin = '0' + formatedMin;
            }

            return formatedMin + ':' + formatedSec;
        }

        var options = $.extend({}, {
            'text_complete'     : "Complete!",
            'text_stop_confirm' : "Are you sure?",
            'text_start_confirm': "Are you sure?",
            'text_start'        : "Start",
            'text_stop'         : "Stop",
            'text_ready'        : "Ready",
            'text_wait'         : "Wait please ...",
            'autoStart'         : false,
            'url'               : '',
            'onStart'           : new Function(),
            'onStop'            : new Function(),
            'onRequest'         : new Function(),
            'onTimer'           : new Function(),
            'onFinal'           : function (callback) {
                callback()
            }
        }, options);

        // init html
        var $obj = $(this);
        $obj.html('<div id="jbprogressbar" class="uk-progress">' +
            '<div class="uk-progress-bar" style="width: 100%;">' + options.text_ready + '</div>' +
            '</div>' +
            '<div class="clr"></div>' +
            '<input type="submit" class="jsStart uk-button uk-button-success" value="' + options.text_start + '" />' +
            '<input type="button" class="jsStop uk-button" value="' + options.text_stop + '" style="display:none;" />'
        );

        // vars
        var $bar = $('#jbprogressbar', $obj),
            $progress = $('.uk-progress-bar', $obj),
            $label = $(".progress-label", $obj),
            $start = $('.jsStart', $obj),
            $stop = $('.jsStop', $obj),
            currentProgress = 0,
            secondsPassed = 0,
            stopFlag = true,
            timerId = 0,
            page = 0;

        function timerStart() {
            secondsPassed = 0;
            timerId = setInterval(function () {
                options.onTimer({
                    'passed'   : timeFormat(++secondsPassed),
                    'remaining': timeFormat(parseInt((secondsPassed * 100 / currentProgress) - secondsPassed, 10))
                });
            }, 1000);
        }

        function timerStop() {
            clearInterval(timerId);
        }

        function triggerStart() {
            currentProgress = 0;
            $start.hide();
            $stop.show();
            $bar.addClass('uk-progress-striped uk-active');
            $('.jsErrorBlockWrapper').hide();
            $('.jsErrorBlock').empty();

            stopFlag = false;
            page = 0;
            request(0);

            options.onStart();
            timerStart();
        }

        function triggerStop() {
            $start.show();
            $stop.hide();
            $bar.removeClass('uk-progress-striped uk-active');

            stopFlag = true;
            timerStop();
            options.onStop();
        }

        /**
         * Request for step in server
         * @param page
         */
        function request(page) {

            if (stopFlag || currentProgress >= 100) {
                triggerStop();
                return;
            }

            JBZoo.ajax({
                'url'    : options.url,
                'data'   : {
                    'page': page
                },
                'success': function (data, status) {
                    currentProgress = data.progress;
                    options.onRequest(data);
                    $progress.css('width', currentProgress + '%');

                    if (data.progress >= 100) {

                        $progress.text(options.text_wait);
                        options.onFinal(function () {
                            $progress.text(options.text_complete);
                        });

                        triggerStop();

                    } else {
                        $progress.text(currentProgress + ' %');
                        request(++page);
                    }
                },
                'onFatal': function (responce) {
                    $('.jsErrorBlock').html(responce.responseText);
                    $('.jsErrorBlockWrapper').fadeIn();
                    triggerStop();
                }
            });
        }

        $start.bind('click', function () {
            if (confirm(options.text_start_confirm)) {
                triggerStart();
            }
            return false;
        });

        $stop.bind('click', function () {
            if (confirm(options.text_stop_confirm)) {
                triggerStop();
            }
            return false;
        });

        // autostart init
        if (options.autoStart) {
            triggerStart();
            $start.hide();
            $stop.hide();
        }

    };

    $.fn.JBZooPriceAdvanceBalanceHelper = function () {

        return $(this).each(function () {

            var $this = $(this),
                init = false;

            if (init == true) {
                return $this;
            }

            var $input = $('.jsBalanceInput', $this);

            function change(val) {
                $input.removeAttr('disabled');

                if (val != 1) {
                    $input.attr('disabled', true);
                    $input.val('');
                }
            }

            $('input[type="radio"]', $this).on('change', function () {
                var $radio = $(this);

                change($radio.val());
            });

            change($('input[type="radio"]:checked', $this).val());

            init = true;
        });
    };

    /**
     * @param options
     */
    $.fn.JBZooViewed = function (options) {

        var options = $.extend({}, {
            'message': 'Do you really want to delete the history?',
            'app_id' : ''
        }, options);
        var $this = $(this);

        if ($this.hasClass('module-items-init')) {
            return $this;
        } else {
            $this.addClass('module-items-init');
        }

        return $this.find('.jsRecentlyViewedClear').on('click', function () {
            var ok = confirm(options.message);

            if (ok) {
                JBZoo.ajax({
                    'data'    : {
                        'controller': 'viewed',
                        'task'      : 'clear',
                        'app_id'    : options.app_id
                    },
                    'dataType': 'html',
                    'success' : function () {
                        $this.slideUp('slow');
                    }
                });
            }

            return false;
        });
    };

    /**
     * Height fix plugin
     */
    $.fn.JBZooHeightFix = function () {

        var $this = $(this), maxHeight = 0;

        setTimeout(function () {
            $('.column', $this).each(function (n, obj) {
                var tmpHeight = parseInt($(obj).height(), 10);
                if (maxHeight < tmpHeight) {
                    maxHeight = tmpHeight;
                }
            }).css({height: maxHeight});
        }, 300);
    };

    /**
     * jQuery helper plugin for color element
     * @param options
     */
    $.fn.JBColorHelper = function (options) {

        var options = $.extend({}, {
            'multiple': true,
            'method'  : ''
        }, options);

        return $(this).each(function () {

            var $this = $(this);

            $this.find('input[type=' + options.type + ']:checked').next().addClass('checked');

            if ($this.hasClass('jbcolor-initialized')) {
                return $this;
            } else {
                $this.addClass('jbcolor-initialized');
            }

            $('.jbcolor-input', $this).on('click', function () {
                var $obj = $(this);
                if (!options.multiple) {
                    if ($obj.hasClass('checked')) {
                        $obj
                            .attr('checked', false)
                            .addClass('unchecked')
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $obj.trigger('change');
                    } else {
                        $('.jbcolor-input', $this).removeClass('checked');
                        $('.jbcolor-label', $this).removeClass('checked');
                        $obj
                            .attr('checked', true)
                            .addClass('checked')
                            .removeClass('unchecked')
                            .next()
                            .addClass('checked');
                    }
                } else {

                    if ($obj.hasClass('checked')) {
                        $obj
                            .removeClass('checked')
                            .next()
                            .removeClass('checked');

                        $obj.trigger('change');
                    } else {
                        $obj
                            .addClass('checked')
                            .next()
                            .addClass('checked');
                    }

                }
            });

        });
    };

    $.fn.JBInputHelper = function (settings) {

        var options = $.extend({}, {
            'base'    : '.ghost',
            'label'   : '.upgrd-label',
            'classes' : {
                'hover' : 'hover',
                'active': 'active',
                'focus' : 'focus'
            },
            'multiple': false,
            'parent'  : '.shipping-default'
        }, settings);

        return $(this).each(function () {

            var $this = $(this),
                $label = $this.prev(options.label);

            if ($this.hasClass('init')) {
                return $this;
            }

            $this.addClass('init');
            $this.on('click', function () {

                $(options.label).remove(options.classes);
                $label.addClass('checked');
            });

            $label.on('mouseenter', function () {

                $label.addClass('hover');
            });

            $label.on('mouseleave', function () {

                $label.removeClass('hover');
            });

        });
    };

    $.fn.JBCartShipping = function (settings) {

        var options = $.extend({}, {
            'no_value_message': 'Free'
        }, settings);

        var byDefault = 'default',
            plugins = [],
            $this = $(this),
            create = false;

        $this.getParams = function () {

            var params = {};
            for (var name in plugins) {

                var plg = plugins[name];

                var identifier = $('.jsInputShippingService', plg).val(),
                    options = $('.jsCalculate', plg);

                params[identifier] = {};
                $('input, select', options).each(function () {

                    var field = $(this);

                    if (typeof field.attr('name') != 'undefined') {

                        var nameOf = field.attr('name');

                        nameOf = nameOf.replace(/shipping(?:[\[])(\w+)(?:[\]])/, "$1");

                        params[identifier][nameOf] = field.val();
                    }
                });

            }
            return $.param({'shipping': params});
        };

        $this.recount = function () {

            for (var name in plugins) {
                plugins[name].getPrice();
            }

            return $this.getPrice();
        };

        $this.createPlugins = function () {

            if (create === false) {
                $('.jsShippingElement', $this).each(function () {

                    if ($(this).length > 0) {
                        $('select', $(this)).chosen();
                        $this.createPlugin($(this));
                        create = true;
                    }
                });
            }

            return plugins;
        };

        $this.getPrice = function () {

            for (var name in plugins) {

                var plg = plugins[name];

                if ($('.jsInputShippingService:checked', plg).length > 0) {

                }
            }
        };

        $this.setPrices = function (plgs) {

            for (var id in plgs) {
                var input = $('.jsInputShippingService[value="' + id + '"]', $('.jbzoo .shipping-list')),
                    type = input.parents('.jsShippingElement').data('type'),
                    label = input.next();

                if (input.is(':checked')) {
                    $this.setPrice(plgs[id]);
                }
                plugins[type].price = plgs[id].price;
                $('.shipping-info .value .jsValue', label).html(plgs[id].price);
                $('.shipping-info .value .jsCurrency', label).html(plgs[id].symbol);
            }
        };

        $this.setPrice = function (price) {

            if (price == 'undefined') {
                price = options.no_value_message;
            }

            $('.jsShippingPrice .jsValue', $this.parents('.jbzoo')).html(price.price);
            $('.jsShippingPrice .jsCurrency', $this.parents('.jbzoo')).html(price.symbol);
        };

        var toggleShipFields = function (shipFields) {

            var shippingBlock = $this.parents('.jbzoo').find('.shippingfileds-list');
            shippingBlock.addClass('loading');

            shippingBlock.show();
            if (shipFields.indexOf(':') > 0) {

                var fields = shipFields.split(':'),
                    classes = '.element-' + fields.join(', .element-');

                $(classes, shippingBlock).fadeIn()
                    .find('input, select, textarea')
                    .removeAttr('disabled');

                $('div.element:not(' + classes + ')', shippingBlock)
                    .fadeOut(function () {

                        $(this)
                            .find('input, select')
                            .attr('disabled', 'disabled');
                    });

            } else if (shipFields.length > 0) {

                $('.element-' + shipFields, shippingBlock)
                    .fadeIn()
                    .find('input, select, textarea')
                    .removeAttr('disabled');

                $('div.element', shippingBlock).not('.element-' + shipFields)
                    .fadeOut(function () {

                        $(this)
                            .find('input, select, textarea')
                            .attr('disabled', 'disabled');
                    });

            } else {

                shippingBlock.hide();
            }

            setTimeout(function () {
                shippingBlock.removeClass('loading');
            }, 500);
        };

        $('.jsInputShippingService', $this).on('change', function () {

            var $element = $(this).parents('.jsShippingElement');
            var plg = plugins[$element.data('type')];

            $this.setPrice({'price': plg.price, 'symbol': plg.symbol});

            $element.addClass('active');
            $element.siblings('.element').removeClass('active');

            $this.toggleShipFields($element);
            $this.hide();
            $this.show($element);

        });

        $this.hide = function () {

            $('.jsMoreOptions', $this).slideUp('fast', function () {
                //$('input, select', $(this)).attr('disabled', 'disabled');
            });

            /*$('.jsMoreOptions', $this).animate({
             opacity: 0
             }, 1000, function () {

             });*/
        };

        $this.show = function ($element) {

            /* $('.jsMoreOptions', $element).animate({
             opacity: 1
             }, 1000, function () {

             });*/

            $('.jsMoreOptions', $element).slideDown('fast');
            $('.jsMoreOptions input, .jsMoreOptions select', $element).removeAttr('disabled');
        };

        $this.toggleShipFields = function ($element) {

            var settings = $element.data('settings');

            if (settings) {
                var shipFields = settings.shippingfields;
            }

            if (typeof shipFields != 'undefined') {
                toggleShipFields(shipFields);
            }

        };

        $this.createPlugin = function ($element) {

            var name = $element.data('type'),
                plugin = null;
            if (typeof name == 'undefined') {
                name = byDefault;
            }

            var plugName = $.trim('JBCartShipping' + name.toLowerCase());

            if (typeof plugins[$element.data('type')] != 'undefined' && plugins[$element.data('type')].length !== 0) {
                return plugins[$element.data('type')];
            }

            if ($.isFunction($.fn[plugName])) {

                plugin = $element[plugName]({
                    super: $this
                });

                plugins[$element.data('type')] = plugin;
            } else {

                plugName = $.trim('JBCartShipping' + byDefault);
                plugin = $element[plugName]({
                    super: $this
                });

                plugins[$element.data('type')] = plugin;
            }

            return plugin;
        };

        var $element =
            $('.jsInputShippingService:checked', $this).parents('.jsShippingElement');

        $element.addClass('active');
        $element.siblings('.element').removeClass('active');

        $this.createPlugins();
        if ($element.length > 0) {
            $this.toggleShipFields($element);
        }
        $this.hide();
        $this.show($element);

        return $this;
    };

    $.fn.JBCartShippingdefault = function (options) {

        var settings = $.extend({
                'super': {}
            }, options, $(this).data('settings')),
            global = $(this);

        $(this).each(function () {

            var $this = $(this);

            global.price = settings.default_price;
            global.symbol = settings.symbol;

            global.getPrice = function () {

                var $fields = $('.jsCalculate input:not(input:disabled), ' +
                        '.jsCalculate select:not(select:disabled)', $this),
                    result = {};

                $fields.each(function () {

                    var $field = $(this), value = $.trim($field.val()), id = $field.attr('id');
                    if (value.length > 0) {
                        id = id.replace('shipping', '');
                        result[id] = value;
                    }
                });

                global.addClass('loading');
                JBZoo.ajax({
                    'url'     : settings.getPriceUrl,
                    'data'    : {
                        "args": {
                            'fields': JSON.stringify(result)
                        }
                    },
                    'dataType': 'json',
                    'success' : function (price) {

                        settings.super.setPrice(price);
                        global.price = price.price;
                        $('.shipping-element .field-label .value .jsValue', $this).html(price.price);
                        $('.shipping-element .field-label .value .jsCurrency', $this).html(price.symbol);
                        global.removeClass('loading');
                    },
                    'error'   : function (error) {
                        global.removeClass('loading');
                    }
                });

            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            $('.jsCalculate select, .jsCalculate input', $this).on('change', function () {

                global.getPrice($(this).val());
            });

            $this.addClass('shipping-init');
        });

        return global;
    };

    $.fn.JBCartShippingemspost = function (options) {

        var settings = $.extend({
                'super'  : {},
                'toDoors': 3,
                'toWrn'  : 4
            }, options, $(this).data('settings')),
            global = $(this);

        $(this).each(function () {

            var $this = $(this);

            global.price = settings.default_price;
            global.symbol = settings.symbol;
            global.getPrice = function (to) {

                if (typeof to == 'undefined' || to.length === 0) {
                    to = $('select option:selected', $this).val();
                }

                var result = { to: to };
                global.addClass('loading');
                JBZoo.ajax({
                    'url'     : settings.getPriceUrl,
                    'data'    : {
                        "args": {
                            'to': JSON.stringify(result)
                        }
                    },
                    'dataType': 'json',
                    'success' : function (price) {

                        settings.super.setPrice(price);
                        global.price = price.price;
                        $('.shipping-element .field-label .value .jsValue', $this).html(price.price);
                        $('.shipping-element .field-label .value .jsCurrency', $this).html(price.symbol);
                        global.removeClass('loading');
                    },
                    'error'   : function (error) {
                        global.removeClass('loading');
                    }
                });
            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            $('#shippingto', $this).on('change', function () {

                var value = $(this).val();
                $('#shippingcountryto', $this).attr('disabled', 'disabled');

                if (value.length === 0) {
                    $('#shippingcountryto', $this).removeAttr('disabled');
                }

                $('#shippingcountryto', $this)
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
                global.getPrice(value);
            });

            $('#shippingcountryto', $this).on('change', function () {

                var value = $(this).val();
                $('#shippingto', $this).attr('disabled', 'disabled');

                if (value.length === 0) {
                    $('#shippingto', $this).removeAttr('disabled');
                }

                $('#shippingto', $this)
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
                global.getPrice(value);
            });

            $this.addClass('shipping-init');
        });

        return global;
    };

    $.fn.JBCartShippingnewpost = function (options) {

        var settings = $.extend({
                'super'  : {},
                'toDoors': 3,
                'toWrn'  : 4
            }, options, $(this).data('settings')),
            global = $(this);

        $(this).each(function () {

            var $this = $(this);

            global.price = settings.default_price;
            global.symbol = settings.symbol;

            global.getPrice = function () {
                var $fields = $('.jsCalculate input:not(input:disabled), ' +
                        '.jsCalculate select:not(select:disabled)', $this),
                    result = {};

                $fields.each(function () {

                    var $field = $(this), value = $.trim($field.val()), id = $field.attr('id');
                    if (value.length > 0) {
                        id = id.replace('shipping', '');
                        result[id] = value;
                    }
                });
                global.addClass('loading');
                JBZoo.ajax({
                    'url'     : settings.getPriceUrl,
                    'data'    : {
                        "args": {
                            'fields': JSON.stringify(result)
                        }
                    },
                    'dataType': 'json',
                    'success' : function (price) {

                        settings.super.setPrice(price);
                        global.price = price.price;
                        $('.shipping-element .field-label .value .jsValue', $this).html(price.price);
                        $('.shipping-element .field-label .value .jsCurrency', $this).html(price.symbol);
                        global.removeClass('loading');
                    },
                    'error'   : function (error) {
                        global.removeClass('loading');
                    }
                });
            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            var globCities = {},
                globWarehouses = {},
                proccessing = false;

            function getCitySelect() {
                return $('.jsNewPostSenderCity #shippingrecipientcity', $this);
            }

            function getRegion() {
                return $.trim($('#shippingregions option:selected', $this)
                    .val()
                    .toLowerCase());
            }

            function getCity() {
                return $.trim($('.jsNewPostSenderCity #shippingrecipientcity option:selected', $this)
                    .val()
                    .toLowerCase());
            }

            function setCities(cities) {

                var region = getRegion();

                if (typeof cities == 'object' && Object.keys(cities).length > 0) {
                    globCities[region] = cities;
                }
            }

            function setWarehouses(warehouses) {

                var city = getCity();

                if (Object.keys(warehouses).length > 0) {
                    globWarehouses[city] = warehouses;
                }
            }

            function getCities(region) {

                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                var city = {};
                if (globCities.hasOwnProperty(region) === true) {
                    return globCities[region];
                }

                return city;
            }

            function getWarhouses(city) {

                if (typeof city == 'undefined') {
                    city = getCity();
                }

                var warehouses = {};
                if (globWarehouses.hasOwnProperty(city) === true) {
                    return globWarehouses[city];
                }

                return warehouses;
            }

            function clearCities() {
                $('#shippingrecipientcity option', $this).not(':first').remove();
            }

            function clearWarehouses() {
                $('#shippingstreet option', $this).not(':first').remove();
            }

            $this.changePostType = function (type) {

                if (!type) {
                    type = parseInt($('#shippingdeliverytype_id option:selected', $this).val(), 10);
                }

                if (type === settings.toDoors) {
                    $this.showBlockDoors();

                } else if (type === settings.toWrn) {
                    $this.showBlockWarehouse();

                }
            };

            $this.showBlockDoors = function () {

                $('.jsAreaWarehouse', $this).slideUp(function () {
                    $('input, select', $(this)).attr('disabled', 'disabled');
                });

                $('.jsAreaDoors', $this)
                    .slideDown()
                    .find('input, select')
                    .removeAttr('disabled');
            };

            $this.showBlockWarehouse = function () {

                $('.jsAreaDoors', $this).slideUp(function () {
                    $('input, select', $(this)).attr('disabled', 'disabled');
                });

                $('.jsAreaWarehouse', $this)
                    .slideDown()
                    .find('input, select')
                    .removeAttr('disabled');
            };

            $this.addLoading = function () {
                var $select = getCitySelect(),
                    $wrhSelect = $('.jsNewPostWareehouse #shippingstreet', $this);

                $select.addClass('loading');
                $wrhSelect.addClass('loading');
            };

            $this.removeLoading = function () {
                var $select = getCitySelect(),
                    $wrhSelect = $('.jsNewPostWareehouse #shippingstreet', $this);

                $select.removeClass('loading');
                $wrhSelect.removeClass('loading');
            };

            $this.setCities = function (region, callback) {

                proccessing = true;
                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                if (Object.keys(getCities(region)).length > 0 && callback) {
                    callback();
                    proccessing = false;
                    return false;
                }
                $this.addLoading();

                JBZoo.ajax({
                    'url'     : settings.getCitiesUrl,
                    'data'    : {
                        "args": {
                            'region': region
                        }
                    },
                    'dataType': 'json',
                    'success' : function (cities) {
                        setCities(cities.cities);

                        if (callback) {
                            callback();
                        }

                        $this.removeLoading();
                        proccessing = false;

                    },
                    'error'   : function (error) {
                        $this.removeLoading();
                        proccessing = false;
                    }
                });
            };

            $this.addCities = function (region) {

                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                var cities = getCities(region);
                var $select = getCitySelect();

                clearCities();
                if (typeof cities != 'undefined') {
                    $.each(cities, function (key, value) {
                        $select.append($("<option/>", {
                            value: key,
                            text : value
                        }));
                    });
                }

                $select
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
            };

            $this.setWarehouses = function (city, callback) {

                if (!city) {
                    city = getCity();
                }

                if (Object.keys(getWarhouses(city)).length > 0 && callback) {
                    callback();
                    proccessing = false;
                    return false;
                }

                var $select = $('.jsNewPostWareehouse #shippingstreet', $this);
                $select.addClass('loading');

                JBZoo.ajax({
                    'url'     : settings.getWarehousesUrl,
                    'data'    : {
                        "args": {
                            'city': city
                        }
                    },
                    'dataType': 'json',
                    'success' : function (warehouses) {
                        setWarehouses(warehouses.warehouses);

                        if (callback) {
                            callback();
                        }

                        $select.removeClass('loading');
                        proccessing = false;
                    },
                    'error'   : function (error) {
                        $select.removeClass('loading');
                        proccessing = false;
                    }
                });
            };

            $this.addWarehouses = function (city) {

                if (!city) {
                    city = getCity();
                }

                var warehouses = getWarhouses(city),
                    $select = $('.jsNewPostWareehouse #shippingstreet', $this);

                clearWarehouses();
                $.each(warehouses, function (key, value) {
                    $select.append($("<option/>", {
                        value: key,
                        text : value
                    }));
                });

                $select
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
            };

            $('#shippingdeliverytype_id', $this).on('change', function () {

                $this.changePostType();
            });

            $('.jsCalculate select', $this).on('change', function () {

                global.getPrice();
            });

            $('#shippingregions', $this).on('change', function () {

                clearWarehouses();
                $this.setCities(getRegion(), $this['addCities']);
            });

            $('#shippingrecipientcity', $this).on('change', function () {

                $this.setWarehouses(getCity(), $this['addWarehouses']);
            });

            $this.changePostType();
            $this.addClass('shipping-init');
        });

        return global;
    };

    $.fn.JBZooEmailPreview = function (options) {
        var options = $.extend({}, {
            'url': ''
        }, options);

        return $(this).each(function () {

            var $this = $(this),
                init = false;

            if (init) {
                return $this;
            }
            init = true;

            $('.jsEmailTmplPreview', $this).on('click', function () {

                $('#jsOrderList', $this).toggle();

                return false;
            });


            $('#jsOrderList .order-id', $this).on('click', function () {

                var $a = $(this),
                    url = options.url + '&id=' + $a.data('id');
                SqueezeBox.initialize({});
                SqueezeBox.open(url, {
                    handler: 'iframe',
                    size   : {x: 1050, y: 700}
                });

                return false;
            })

        });
    }

})(jQuery);


/**
 * JBZoo main init
 */
(function () {
    // init JS helper
    window.JBZoo = new JBZooHelper();

    // add debuger
    window.jbdump = window.dump = function () {
        if (JBZoo.DEBUG) {
            JBZoo.jbdump.apply(this, arguments)
        }
    };

}());

