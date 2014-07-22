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
            'url': 'index.php?format=raw&tmpl=component',
            'data': {},
            'dataType': 'json',
            'success': false,
            'error': false,
            'onFatal': function (responce) {
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
            'option': 'com_zoo',
            'tmpl': 'component',
            'format': 'raw'
        }, options.data);

        $.ajax({
            'url': options.url,
            'data': options.data,
            'dataType': options.dataType,
            'type': 'POST',
            'success': function (data) {

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
            'error': options.onFatal,
            'cache': false,
            'headers': {
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

/**
 * JBZoo UI jQuery plugins
 */
(function ($) {

    /**
     * Empty cart action
     */
    $(document).on('click', '.jbzoo .jsEmptyCart', function () {

        JBZoo.ajax({
            'url': $(this).attr('href'),
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
            'indexTab': 0
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
            'onTabShow': false,
            'headerWidget': 'h3',
            'contentWidget': 'div',
            'activeTab': 0
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

    /**
     * JBZoo Basket widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooBasket = function (options) {

        var options = $.extend({}, {}, options);

        return $(this).each(function () {

            var $obj = $(this);

            // recount basket
            var recount = function (data) {

                for (var itemId in data.items) {
                    var subTotal = data.items[itemId];
                    $('.row-' + itemId + ' .jsSubtotal', $obj).text(subTotal);
                }

                $('.jsTotalCount', $obj).text(data.count);
                $('.jsTotalPrice', $obj).text(data.total);
            };

            // remove one item
            $('.jsDelete', $obj).click(function () {

                var $button = $(this),
                    itemid = $button.closest('tr').data('itemid'),
                    hash = $button.closest('tr').data('hash');

                JBZoo.ajax({
                    'url': options.deleteUrl,
                    'data': {
                        'itemid': itemid,
                        'hash': hash
                    },
                    'success': function (data) {
                        var $row = $button.closest('tr');
                        $row.find('td').slideUp(300, function () {
                            $row.remove();
                            if ($obj.find('tbody tr').length == 0) {
                                window.location.reload();
                            }
                        });
                        recount(data);
                        $.fn.JBZooPriceReloadBasket();
                    }
                });

                return false;
            });

            // remove all
            $('.jsDeleteAll', $obj).click(function () {

                var $button = $(this);

                if (confirm(options.clearConfirm)) {

                    JBZoo.ajax({
                        'url': options.clearUrl,
                        'success': function () {
                            window.location.reload();
                        }
                    });

                }

            });

            // quantity
            var $quantity = $('.jsQuantity', $obj),
                lastQuantityVal = $quantity.val(),
                changeCallback = function ($input) {

                    var value = parseInt($input.val(), 10),
                        itemid = parseInt($input.closest('tr').data('itemid'), 10),
                        hash = $input.closest('tr').data('hash');

                    if ($input.val().length && value >= 0) {
                        lastQuantityVal = value;
                        JBZoo.ajax({
                            'url': options.quantityUrl,
                            'data': {
                                'value': value,
                                'itemId': itemid,
                                'hash': hash
                            },
                            'success': function (data) {
                                recount(data);
                                $.fn.JBZooPriceReloadBasket();
                            },
                            'error': function (data) {
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
                    }, 200);
                })
                .change(function () {
                    var $input = $(this);
                    clearTimeout(changeTimer);
                    changeTimer = setTimeout(function () {
                        changeCallback($input);
                    }, 200);
                });
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
                    'url': $toggle.attr("href"),
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
                    'url': $toggle.attr("href"),
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
                    'url': $toggle.attr("href"),
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

    /**
     * JBZoo JBPrice advance (for admin panel)
     * @param options
     * @constructor
     */
    $.fn.JBZooPriceAdvanceAdmin = function (options) {

        return $(this).each(function (n, obj) {

            var $obj = $(obj),
                $variations = $('.variations', $obj);

            options = $.extend({}, {
                'text_variation_show': 'Show variations',
                'text_variation_hide': 'Hide variations',
                'adv_field_param_edit': 0,
                'all_params': {},
                'base_currency': $('.basic-currency', $obj).val(),
                'base_sku': $('.basic-sku', $obj).val()
            }, options);

            function rebuildList() {
                $('.jbpriceadv-variation-row .jbremove', $obj).show();
                $('.jbpriceadv-variation-row', $obj).each(function (n, row) {
                    var $row = $(this);
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

                    $('input, select, textarea', $row).each(function () {
                        var $control = $(this);
                        $control.attr('name', $control.attr('name').replace(/\[variations\]\[\d\]/i, '[variations][' + n + ']'));

                    });

                });
            }

            $('.jsToggleVariation', $obj).on('click', function () {
                var $toggle = $(this),
                    $fieldset = $toggle.parents('.jbpriceadv-variation-row');

                if (!$fieldset.hasClass('visible')) {
                    $fieldset.removeClass('fieldset-hidden');
                    $fieldset.addClass('visible');
                } else {
                    $fieldset.removeClass('visible');
                    $fieldset.addClass('fieldset-hidden');
                }

            });

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


            $('.jbpriceadv-variation-row', $obj).delegate(".jbmove", "mousedown", function () {
                $(".jbpriceadv-variation-row", $obj).removeClass('visible');
                $(".jbpriceadv-variation-row", $obj).addClass("fieldset-hidden");
            });

            $('.jbmove', $obj).sortable({
                forcePlaceholderSize: true,
                'items': $('.jbpriceadv-variation-row', $obj),
                'placeholder': "ui-state-highlight",
                'stop': function (ev, ui) {
                    $('.variations-list').trigger('oops');
                }
            }).disableSelection();


            $('.variations-list').on('oops', function () {
                rebuildList();
            });
            $('.jsNewPrice', $obj).click(function () {

                var $newRow = $('.jbpriceadv-variation-row:first', $obj).clone().hide();
                $('input, select', $newRow).removeAttr('value');
                $('input, select', $newRow).removeAttr('checked');

                $('.variations-list', $obj).append($newRow);
                rebuildList();

                $newRow.slideDown();

                return false;
            });

            $obj.on('click', '.jbremove', function () {
                var $row = $(this).closest('.jbpriceadv-variation-row');
                $row.slideUp(300, function () {
                    $row.remove();
                    rebuildList();
                });
            });

            // init
            (function () {
                rebuildList();
                $('.jbpriceadv-variation-row', $obj).addClass('fieldset-hidden');
                if (!options.adv_field_param_edit) {
                    $.each(options.all_params, function (n, obj) {
                        $('.element-' + obj).hide();
                    });
                }

            }());
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
                    url: "index.php?option=com_media&view=images&tmpl=component&e_name=" + id,
                    size: {x: 850, y: 500}
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
                'uniqid': '',
                'items': null,
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
                    'type': 'iframe',
                    'href': href,
                    'width': 360,
                    'autoHeight': true,
                    'autoResize': true,
                    'fitToView': true,
                    'iframe': {
                        'scrolling': 'no',
                        'preload': true
                    },
                    'helpers': {
                        'overlay': {
                            'locked': false,
                            'css': {
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
                    'url': $link.data('href'),
                    'data': {
                        "args": {
                            'quantity': $('.jsQuantity').val(),
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
                    'url': $link.data("href"),
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
     * JBZoo JBPrice Toggler (depricated!)
     * @depricated
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
     * JBZoo JBPrice Cart reloader (depricated!)
     * @depricated
     * @constructor
     */
    $.fn.JBZooPriceReloadBasket = function () {

        $('.jsJBZooModuleBasket').each(function (n, obj) {

            var $obj = $(obj);

            JBZoo.ajax({
                'data': {
                    'controller': 'basket',
                    'task': 'reloadModule',
                    'app_id': $obj.attr('appId'),
                    'moduleId': $obj.attr('moduleId')
                },
                'dataType': 'html',
                'success': function (data) {
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
            'text_complete': "Complete!",
            'text_stop_confirm': "Are you sure?",
            'text_start_confirm': "Are you sure?",
            'text_start': "Start",
            'text_stop': "Stop",
            'text_ready': "Ready",
            'text_wait': "Wait please ...",
            'autoStart': false,
            'url': '',
            'onStart': new Function(),
            'onStop': new Function(),
            'onRequest': new Function(),
            'onTimer': new Function(),
            'onFinal': function (callback) {
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
                    'passed': timeFormat(++secondsPassed),
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
                'url': options.url,
                'data': {
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

    /**
     * JBZoo JBPrice Advance
     * @param options
     * @returns {*|jQuery}
     */
    $.fn.JBZooPriceAdvance = function (options) {

        var options = $.extend({}, {
            'mainHash': '',
            'itemId': 0,
            'identifier': '',
            'relatedImage': '',
            'mainImage': '',
            'popup': 0,
            'prices': {},
            'addToCartUrl': '',
            'removeFromCartUrl': '',
            'changeVariantUrl': '',
            'basketUrl': '',
            'modalUrl': '',
            'isInCart': 0
        }, options);

        options.params = $.extend({}, {
            'startValue': 1,
            'multipleValue': 1,
            'currencyDefault': 'EUR',
            'advFieldText': 0,
            'advAllExistShow': 0
        }, options.params);

        return $(this).each(function () {

            var $obj = $(this);

            if ($obj.hasClass('jbprice-adv-inited')) {
                return $obj;
            }

            $obj.addClass('jbprice-adv-inited');

            var AjaxProcess = false,
                currency = options.params.currencyDefault,
                prices = {};
            prices[options.mainHash] = options.prices;

            function getPrices(newCurrency) {
                var hash = getCurrentHash();

                if (typeof prices[hash] != 'undefined') {

                    AjaxProcess = false;
                    toggle(prices, newCurrency);
                } else {
                    JBZoo.ajax({
                        'url': options.changeVariantUrl,
                        'data': {
                            'args': {
                                'hash': hash
                            }
                        },
                        'success': function (data) {
                            AjaxProcess = false;

                            if (typeof data != 'undefined') {
                                prices[hash] = data;
                            } else {
                                prices[hash] = prices[options.mainHash];
                            }

                            toggle(prices, newCurrency);
                        },
                        'error': function (data) {
                            AjaxProcess = false;
                            if (data.result == false) {
                                prices[hash] = prices[options.mainHash];
                            }

                            toggle(prices, newCurrency);
                        }
                    });
                }
            }

            function togglePrices(newCurrency) {

                if (AjaxProcess) {
                    return false;
                }
                AjaxProcess = true;

                getPrices(newCurrency);
                currency = newCurrency;
            }

            function toggle(prices, newCurrency) {
                var hash = getCurrentHash();

                var values = '',
                    description = '';

                values = prices[options.mainHash][newCurrency];
                console.log(prices);
                //TODO optimize code
                if (typeof prices[hash] != 'undefined') {

                    values = prices[hash][newCurrency];
                    description = $.trim(prices[hash].description);


                    if (options.params.advAllExistShow == 0) {
                        $('.jbprice-buttons', $obj).removeClass('disabled');
                    }
                } else {
                    values = prices[options.mainHash][newCurrency];

                    if (options.params.advAllExistShow == 0) {
                        $('.jbprice-buttons', $obj).addClass('disabled');
                    }

                }

                $('.not-paid-box', $obj).show();
                if (values.totalNoFormat == 0) {
                    $('.not-paid-box', $obj).hide();
                }

                $('.jsSave', $obj).text(values.save);
                $('.jsTotal', $obj).text(values.total);

                $('.jsPrice', $obj).html('&nbsp;' + values.price + '&nbsp;');
                $('.jbcurrency-' + newCurrency.toLowerCase(), $obj).addClass('active');
                $('.jsDescription', $obj).text(description);

                $('.jbprice-balance .balance', $obj).hide();
                $('.jbprice-balance .' + hash, $obj).show();

                //$('.jbprice-sku .sku', $obj).hide();
                //$('.jbprice-sku .' + hash, $obj).show();

                $('.jbprice-sku .sku', $obj).html(prices[hash].sku);


                if (typeof prices[hash] != 'undefined') {
                    if (prices[hash].file) {
                        var $relatedImg = $('.' + options.relatedImage);

                        $relatedImg.attr('src', prices[hash].file);

                        if (options.popup == 1) {
                            $relatedImg.parent().attr('href', prices[hash].file_popup);
                        }
                    }
                }

            }

            function isTextParam() {
                return options.params.advFieldText == 2; // replace number to const
            }

            function bindDataIndex() {
                $('.jbpriceParams', $obj).each(function (n) {
                    n++;
                    var $param = $(this);
                    $param.attr('data-index', n);
                })
            }

            /**
             * Get param value
             * @returns Object
             */
            function getParamValues() {

                var values = getCurrentValues();

                if (isTextParam()) {
                    var values = {
                        '1': values['p1-'],
                        '2': values['p2-'],
                        '3': values['p3-'],
                        'desc': values['d-']
                    };
                } else {
                    var values = {
                        '1': values['p1-'],
                        '2': values['p2-'],
                        '3': values['p3-']
                    };
                }

                return values;
            }

            /**
             * Add item to cart
             * @param callback
             */
            function addToCart(callback) {

                var count = options.params.startValue;
                if ($('.jsCount', $obj).length) {
                    count = $('.jsCount', $obj).val();
                }

                JBZoo.ajax({
                    'url': options.addToCartUrl,
                    'data': {
                        "args": {
                            'quantity': count,
                            'params': getParamValues()
                        }
                    },
                    'success': function (data) {

                        if ($.isFunction(callback)) {
                            callback(data);
                        }
                    },
                    'error': function (data) {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });
            }

            function removeFromCart() {
                JBZoo.ajax({
                    'url': options.removeFromCartUrl,
                    'success': function (data) {
                        $obj.removeClass('in-cart').addClass('not-in-cart');
                        $.fn.JBZooPriceReloadBasket();
                    }
                });
            }

            /**
             * Build hash to string
             * @param hash
             * @returns {string}
             */
            function buildHash(hash) {
                var result = [];

                for (var key in hash) {
                    var val = hash[key];
                    result.push(key + val);
                }

                return result.join('_');
            }

            function buildValue(value) {
                var result = [];

                for (var key in value) {
                    var val = value[key];
                    result.push(val);
                }

                return result.join('-');
            }

            function getCurrentValues() {

                var data = {};

                $('.jbpriceParams', $obj).each(function (n, row) {
                    var $param = $(row),
                        type = $param.data('type'),
                        index = $param.data('index');

                    if (type == 'radio') {
                        var radio = $('input[type="radio"]:checked', $param);

                        data['p' + index + '-'] = $.trim(radio.val());

                    }

                    if (type == 'select') {
                        var select = $('select.jsParam', $param);
                        data['p' + index + '-'] = $.trim(select.val());
                    }

                    if (type == 'checkbox') {
                        var checkbox = {};


                        $('input[type="checkbox"]:checked', $param).each(function (n) {
                            var $checkbox = $(this);
                            checkbox[n] = $checkbox.val();

                        });

                        data['p' + index + '-'] = $.trim(buildValue(checkbox));
                    }


                });

                return data;
            }

            /**
             * Get current hash for price
             * @returns {string}
             */
            function getCurrentHash() {

                var newHash = getCurrentValues(),
                    result = buildHash(newHash);

                if (result == (['p1-', 'p2-', 'p3-', 'd-']).join('_') ||
                    result == (['p1-', 'p2-', 'p3-']).join('_')) {
                    return options.mainHash;
                }

                result = options.mainHash + '-' + buildHash(newHash);
                if (!buildHash(newHash).length) {
                    result = options.mainHash;
                }

                return result;
            }

            //init quantity plugin
            $.fn.JBZooPriceAdvanceQuantity($obj);

            /*// count
             $('.jsAddQuantity', $obj).click(function () {
             var quantity = parseInt($('.jsCount', $obj).val(), 10);
             quantity += parseInt(options.params.multipleValue, 10);
             $('.jsCount', $obj).val(quantity);
             return false;
             });

             $('.jsRemoveQuantity', $obj).click(function () {
             var quantity = parseInt($('.jsCount', $obj).val(), 10);
             quantity -= parseInt(options.params.multipleValue, 10);
             if (quantity <= 0) {
             quantity = options.params.startValue;
             }

             $('.jsCount', $obj).val(quantity);
             return false;
             });

             $('.jsCount', $obj).bind('change', function () {
             var value = parseInt($.trim($(this).val()));
             value = (isNaN(value) || value < 1) ? options.params.startValue : value;
             $(this).val(value);
             });*/

            // currency list
            $(".jsPriceCurrency", $obj).bind('click', function () {
                var $cur = $(this),
                    $parent = $(this).parent(),
                    currency = $cur.data('currency');

                $parent.addClass('jbprice-lock');

                $(".jsPriceValue", $obj).removeClass('active');
                $(".jbprice-" + currency, $obj).addClass('active');
                $(".jsPriceCurrency", $obj).removeClass('active');
                $cur.addClass("active");
                togglePrices(currency);
            });

            $('.jsParam', $obj).bind('change', function () {
                togglePrices(currency);
            });

            $('.jsParamDesc', $obj).bind('change', function () {
                togglePrices(currency);
            });

            $('.jbprice-param-radio input', $obj).bind('change', function () {
                togglePrices(currency);
            });

            $(".jsAddToCart", $obj).click(function () {
                addToCart(function (data) {
                    if (data) {
                        $.fn.JBZooPriceToggle(options.identifier, options.itemId);
                    }
                });
                return false;
            });

            $(".jsRemoveFromCart", $obj).click(function () {
                removeFromCart();
                return false;
            });

            $(".jsAddToCartModal", $obj).click(function () {
                var $link = $(this),
                    href = options.modalUrl,
                    params = 'format=raw&tmpl=component';

                // force added params (sef bug)
                if (href.indexOf('?') == -1) {
                    href += '?' + params;
                } else {
                    href += '&' + params;
                }

                $.fancybox({
                    'type': 'iframe',
                    'href': href,
                    'width': 400,
                    'fitToView': true,
                    'autoHeight': true,
                    'autoResize': true,

                    'iframe': {
                        'scrolling': 'no',

                        'preload': true
                    },
                    'helpers': {
                        'overlay': {
                            'locked': false,

                            'css': {
                                'background': 'rgba(119, 119, 119, 0.4)'
                            }
                        }
                    }
                });

                return false;
            });

            $('.jsCartModal .jsAddToCart').unbind().click(function () {
                addToCart(function (data) {
                    if (data) {
                        parent.jQuery.fn.JBZooPriceToggle(options.identifier, options.itemId);

                        if (typeof parent.jQuery.fancybox != 'undefined') {
                            parent.jQuery.fancybox.close();
                        }

                    }
                });

                return false;
            });

            $('.jsAddToCartOne', $obj).click(function () {
                addToCart(function (data) {
                    if (data) {
                        $.fn.JBZooPriceToggle(options.identifier, options.itemId);
                        $.fn.JBZooPriceReloadBasket();
                    }
                });

                return false;
            });

            $('.jsAddToCartGoto', $obj).click(function () {
                addToCart(function (data) {
                    if (data) {
                        if (options.basketUrl) {
                            parent.location.href = options.basketUrl;
                        }
                    }
                });

                return false;
            });

            // init
            (function () {
                $obj.addClass(options.isInCart ? 'in-cart' : 'not-in-cart');
                bindDataIndex();
                $(".jbcurrency-" + options.params.currencyDefault, $obj).addClass('active');
                togglePrices(options.params.currencyDefault);

            }());
            /*
             *                 togglePrices(options.params.currencyDefault);
             * */

        });
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
    }

    $.fn.JBZooPriceAdvanceQuantity = function ($obj) {

        var $quantity = $('.jbprice-quantity', $obj),
            multiple = $quantity.data('multiple'),
            $default = $quantity.data('default');

        var digits = $('.item-count-digits dd', $quantity),
            digits_box = $('.item-count-digits', $quantity),
            jsCount = $('input.jsCount', $quantity),
            processing = false,
            min_val = 1;

        function scroll(e, i) {
            e.preventDefault();
            e.stopPropagation();

            jsCount.blur();

            if (processing) return;
            var val = parseInt(jsCount.val());

            i = i > 0 ? 1 : -1;
            var newVal = val + i;

            if (!checkVal(newVal)) return;

            processing = true;

            setDigitVals(val);
            placeDigits();

            digits_box
                .stop()
                .animate({
                    top: i * digits.height() + 'px'
                    //marginTop:0
                }, {
                    duration: 500,
                    //easing : '',
                    complete: function () {
                        processing = false;
                        jsCount.val(newVal);
                    }
                });
        }

        function setDigitVals(value) {
            var max = value + 2;

            for (var i = 0; i < 5; i++) {
                var num = max - i;

                digits.eq(i).html(checkVal(num) ? num : '');
            }
        }

        function refresh() {
            setDigitVals(parseInt(jsCount.val(), 10));
            placeDigits();
        }

        function checkVal(i) {
            return i >= min_val;
        }

        function placeDigits() {

            digits_box.css({
                top: 0,
                'marginTop': -digits.height() * 2 + 'px'
            });
        }

        // count
        $('.jsAddQuantity', $quantity).click(function (e) {

            scroll(e, 1);
            return false;
        });

        $('.jsRemoveQuantity', $quantity).click(function (e) {

            scroll(e, -1);
            return false;
        });

        $('.jsCount', $quantity).bind('change', function () {

            var value = parseInt($.trim($(this).val()));
            value = (isNaN(value) || value < 1) ? $default : value;
            $(this).val(value);
        });

        setDigitVals($default);
        placeDigits();

        jsCount
            .focus(function () {
                jsCount.css('opacity', '1');
                digits_box.hide();
            }).keyup(function () {
                refresh();
            }).blur(function () {
                jsCount.css('opacity', '0');
                digits_box.show();
            });

        $('.jsCount', $quantity).val($default);
        $('.jsCountValue', $quantity).text($default);

        if ($default <= 1) {
            $('.count-value-wrapper', $quantity).hide();
        }

        /*var CountBox = function (box, onChange) {
         var
         self = this,
         box = $(box),
         inp = box.find('input.jsCount'),
         digits_box = box.find('.item-count-digits'),
         digits = box.find('.item-count-digits dd'),

         minus = box.find('.jsAddQuantity'),
         plus = box.find('.jsRemoveQuantity'),

         processing = false,
         min_val = 1,
         max_val = 99,
         duration = 500
         ;
         this.getValue = function () {
         return parse();
         };
         this.setValue = function (num) {
         val(num);

         applyInpVal();
         };

         if (box.length) init();

         function init() {
         inp
         .focus(function () {
         inp.css('opacity', '1');
         digits_box.hide();
         })
         .keyup(function () {
         applyInpVal();
         })
         .blur(function () {
         applyInpVal();

         inp.css('opacity', '0');
         digits_box.show();
         })
         ;

         applyInpVal(true);


         minus.click(function (e) {
         scroll(e, -1);
         });
         plus.click(function (e) {
         scroll(e, 1);
         });

         box.data('CountBox', self);
         }

         function scroll(e, iVal) {
         e.preventDefault();
         e.stopPropagation();

         inp.blur();

         if (processing) return;

         iVal = iVal > 0 ? 1 : -1;
         var newVal = parse() + iVal;

         if (!checkVal(newVal)) return;

         processing = true;

         placeDigits();
         applyInpVal();

         digits_box
         .stop()
         .animate({
         top: iVal * digits.height() + 'px'
         //				marginTop:0
         }, {
         duration: duration,
         //easing : '',
         complete: function () {
         val(newVal);
         processing = false;
         }
         })
         ;
         }


         function applyInpVal(first) {
         var iVal = setInpVal();
         setDigitVals(iVal);
         placeDigits();

         if (!first && onChange) onChange();
         }

         function setInpVal() {
         var res = parse();
         val(res);
         return res;
         }

         function setDigitVals(iVal) {
         var max = iVal + 2;

         for (var i = 0; i < 5; i++) {
         var num = max - i;
         digits.eq(i).html(checkVal(num) ? num : '');
         }
         }

         function placeDigits() {
         digits_box.css({
         top: 0,
         'marginTop': -digits.height() * 2 + 'px'
         });
         }

         function checkVal(iVal) {
         return iVal >= min_val && iVal <= max_val;
         }

         function parse() {
         var res = val()
         .replace(',', '.')
         .replace(/[^\d\.]/gi, '')
         ;

         return parseInt(res) || 0;
         }

         function val() {
         if (arguments.length) {
         //inp.val(arguments[0]);
         }
         return inp.val() + '';
         }
         }

         //CountBox($quantity);*/
    }


    /**
     * @param options
     */
    $.fn.JBZooViewed = function (options) {

        var options = $.extend({}, {
            'message': 'Do you really want to delete the history?',
            'app_id': ''
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
                    'data': {
                        'controller': 'viewed',
                        'task': 'clear',
                        'app_id': options.app_id
                    },
                    'dataType': 'html',
                    'success': function () {
                        $this.slideUp('slow');
                    }
                });
            }

            return false;
        });
    }

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
    }

    /**
     * jQuery helper plugin for color element
     * @param options
     */
    $.fn.JBColorHelper = function (options) {

        var options = $.extend({}, {
            'multiple': true
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

