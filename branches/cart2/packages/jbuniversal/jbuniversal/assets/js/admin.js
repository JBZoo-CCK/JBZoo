/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

/**
 * Scripts for Joomla CP
 */
jQuery(function ($) {

    /**
     * jQuery plugin for color element
     * @param options
     */
    $.fn.JBColorElement = function (options) {

        var options = $.extend({}, {
            message: ' already in the list settings',
            theme: 'bootstrap',
            position: 'bottom'
        }, options);

        onAdded();

        function initMinicolors(element) {

            if ($.isFunction($.fn.minicolors)) {
                var $minicolors = $(element).find('.jbpicker .jbcolor');

                if ($minicolors.hasClass('jbcolor-colors-init')) {
                    return $minicolors;
                }

                $minicolors.minicolors({
                    theme: options.theme,
                    position: options.position
                });

                $minicolors.addClass('jbcolor-colors-init');

            } else {

                $$('.jbpicker .jbcolor', element).each(function (item) {

                    if (item.hasClass('jbcolor-colors-init') || item.id == '') {
                        return item;
                    }

                    new MooRainbow(item, {
                        id: item.id,
                        imgPath: '../media/system/images/mooRainbow/',
                        startColor: [255, 0, 0],
                        onComplete: function (color) {
                            this.element.value = color.hex;
                        }
                    });
                    item.addClass('jbcolor-colors-init');

                });
            }
        }

        function onAdded() {
            $('#element-list').on('element.added', function (event, element) {
                initMinicolors(element);
            });
        }


        return $(this).each(function () {

            var $this = $(this);

            if ($this.hasClass('added-initialized')) {
                return $this;
            } else {
                $this.addClass('added-initialized');
            }

            initMinicolors($this);
            $('.jsColorAdd', $this).on('click', function () {

                var error = false,
                    $jbname = $('.jbname', $this),
                    $jbcolor = $('.jbcolor', $this),
                    name = $jbname.val(),
                    val = $jbcolor.val(),
                    color = val.toLowerCase(),
                    textVal = $.trim($('.jbcolor-textarea', $this).val()),
                    text = textVal.toLowerCase(),
                    space = text ? '\n' : '';

                if (color && text.indexOf(color) >= 0) {
                    alert(color + options.message);
                }

                if (!name.length) {
                    $jbname.addClass('error').focus();
                    error = true;
                }

                if (!color.length) {
                    $jbcolor.addClass('error').focus();
                    error = true;
                }

                if (error) {
                    return false;
                }

                $('.jbpicker input', $this).removeClass('error');

                $('.jbcolor-textarea', $this).val(text + space + name + color);
                $jbname.focus();
                $jbname.val('');
                $jbcolor.val('');
                $('.minicolors-swatch span', $this).removeAttr('style');

            });

            $('.jbcolor, .jbname', $this).on('keyup', function (event) {
                if (event.keyCode == 13) {
                    $('.jsColorAdd', $this).trigger('click');
                }
            });

        });

    };

    /**
     * Show/hide joomla field jbdelimiter
     */
    $.fn.JBZooDelimiter = function (options) {

        var options = $.extend({}, {
            'version': '3'
        }, options);

        var $mode = $('#jform_params_mode'),
            $parent = $(this).parents('#attrib-base'),
            group = $(this).attr('data-group');

        if (options.version == 2) {
            $parent = $(this).parents('.adminformlist');
        }

        $(this).each(function () {

            var $this = $(this);

            if (!$this.hasClass('jbdelimiter-init')) {
                $this.addClass('jbdelimiter-init');
            } else {
                return $this;
            }

            var $control = $this.parents('.control-group');

            if (options.version == 2) {
                $control = $this.parent('li');
            }

            $control.attr('data-group', group).addClass('stop');
            $control.nextUntil('.stop').attr('data-group', group);
        });

        var update = function (selected) {
            $parent.children().each(function () {
                group = $(this).attr('data-group');
                if (group != selected && typeof group != 'undefined' && group != 'close') {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }

        update($mode.val());

        if ($mode.hasClass('initialized')) {
            return $(this);
        } else {
            $mode.addClass('initialized');
        }

        $mode.on('change', function () {
            update($(this).val());
        });
    }

    $.fn.JBZooAdminMenu = function (options) {

        var $obj = $(this),
            html = '';

        if ($obj.is('.disabled')) {
            $obj.append('<li class="disabled"><a>' + options.name + '</a>');
        } else {

            $.each(options.items, function (parentKey, items) {

                var attrs = '';

                if (typeof items.target != "undefined") {
                    attrs += 'target="' + items.target + '"';
                }

                if (items == 'divider') {
                    html += '<li class="separator divider"><span></span></li>';

                } else if (typeof items.children == 'undefined' || items.children.length == 0) {
                    html += '<li><a ' + attrs + ' class="' + parentKey + '-item parent-link" href="' + items.url + '">' + items.name + '</a></li>';

                } else {

                    var classes = parentKey + '-item dropdown-toggle parent-link';
                    if (typeof items.icon != "undefined") {
                        attrs += ' style="background-image:url(' + items.icon + ');" ';
                    }

                    html += '<li class="node dropdown-submenu">';
                    html += '<a ' + attrs + ' class="' + classes + '" data-toggle="dropdown" href="' + items.url + '">' +
                        items.name + '</a><ul class="dropdown-menu">';

                    $.each(items.children, function (childKey, item) {

                        var innerAttrs = '';
                        if (typeof item.target != "undefined") {
                            innerAttrs += ' target="' + items.target + '" ';
                        }

                        if (item == 'divider') {
                            html += '<li class="separator divider"><span></span></li>';

                        } else {
                            html += '<li><a ' + innerAttrs + ' class="' + childKey + '-item" href="' + item.url + '">' + item.name + '</a></li>';
                        }
                    });

                    html += '</ul></li>';
                }

            });

            $obj.append('<li class="dropdown" id="jbzoo-adminmenu">' +
                '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' +
                options.name +
                ' <span class="caret"></span></a>' +
                '<ul class="dropdown-menu">' + html + '</ul></li>');
        }

    };

    /**
     * Pseudo jQuery plugin for form filed key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooKeyValue = function (options) {
        $('body').on('click', '.jsKeyValue .jsKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsKeyValue'),
                $template = $parent.find('.jbkeyvalue-row:first').clone(),
                length = $parent.find('.jbkeyvalue-row').length;

            $template.find('input').attr('value', '');

            html = '<div class="jbkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            return false;
        });
    }


    /**
     * Pseudo jQuery plugin for form filed joomla key-value
     * @param options
     * @constructor
     */
    $.fn.JBZooJKeyValue = function (options) {

        $('body').on('click', '.jsJKeyValue .jsJKeyValueAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsJKeyValue'),
                $template = $parent.find('.jbjkeyvalue-row:first').clone(),
                length = $parent.find('.jbjkeyvalue-row').length;


            $template.find('input').attr('value', '');
            $template.find('div').remove();
            $template.find('select').removeClass().show();
            $template.find('select option:selected').removeAttr('selected');

            if (length != 0) {
                $template.append('<a href="#jbjkeyvalue-rem" class="jsJKeyValueRemove">');
            }

            html = '<div class="jbjkeyvalue-row">' + $template.html() + '</div>';
            html = html.replace('[0][key]', '[' + (length) + '][key]');
            html = html.replace('0key', (length) + 'key');
            html = html.replace('[0][value]', '[' + (length) + '][value]');

            $addButton.before(html);

            if (typeof jQuery.fn.chosen !== 'undefined') {
                jQuery('.jbjkeyvalue-row:last select').chosen({
                    disable_search_threshold: 10,
                    allow_single_deselect: true
                });
            }

            return false;
        });

        $('body').on('click', '.jsJKeyValue .jsJKeyValueRemove', function () {
            var $remButton = $(this),
                $row = $remButton.closest('.jbjkeyvalue-row'),
                $parent = $remButton.closest('.jsJKeyValue'),
                $pattern = /[0-9]+?/;

            $row.remove();

            $parent.find('.jbjkeyvalue-row').each(function (key, obj) {
                var $obj = $(obj),
                    $keyName = $('select', $obj).attr('name'),
                    $id = $('select', $obj).attr('id'),
                    $newName = $keyName.replace($pattern, (key)),
                    $newValue = $newName.replace('[key]', '[value]'),
                    $newId = $id.replace($pattern, (key));

                $('div', $obj).remove();
                $('select', $obj).removeClass().show().attr('name', $newName).attr('id', $newId);

                if (typeof jQuery.fn.chosen !== 'undefined') {
                    $('select', $obj).chosen('destroy');

                    jQuery('select', $obj).chosen({
                        disable_search_threshold: 10,
                        allow_single_deselect: true
                    });
                }

                $('input', $obj).attr('name', $newValue);

            });

            return false;
        });
    }

    /**
     * Pseudo jQuery plugin for form filed ItemOrder
     * @param options
     * @constructor
     */
    $.fn.JBZooItemOrder = function (options) {
        $('body').on('click', '.jsItemOrder .jsItemOrderAdd', function () {

            var $addButton = $(this),
                $parent = $addButton.closest('.jsItemOrder'),
                $template = $parent.find('.jbzoo-itemorder-row:first').clone(),
                length = $parent.find('.jbzoo-itemorder-row').length;

            $template.find('select option').removeAttr('selected');
            $template.find('input[type=checkbox]').removeAttr('checked');
            $template.find('label').removeAttr('for');

            html = '<div class="jbzoo-itemorder-row">' + $template.html() + '</div><br>';
            html = html.split(/_jbzoo_[0-9]_/).join('_jbzoo_' + length + '_');

            $addButton.before(html);

            return false;
        });
    }

    /**
     * Menu tabs hack
     */
    $('li[data-href-replace]').each(function (n, obj) {
        var $obj = $(obj),
            replace = $(obj).data('href-replace'),
            $link = $obj.children('a'),
            href = $link.attr('href');

        if (replace) {
            $link.attr('href', href.replace(replace, 'controller=item'));
        }
    });

    // init Joomla CP Scripts
    (function () {
        if (typeof JBAdminItems != 'undefined') {
            $('#menu').JBZooAdminMenu(JBAdminItems);
        }

        if ($("#nav [data-jbzooversion].active").length) {
            $('<span class="version" />')
                .text("JBZoo " + $("#nav [data-jbzooversion].active").data("jbzooversion"))
                .appendTo("#nav div.bar");
        }

        // some plugins
        $.fn.JBZooKeyValue();
        $.fn.JBZooJKeyValue();
        $.fn.JBZooItemOrder();
    }());

});
