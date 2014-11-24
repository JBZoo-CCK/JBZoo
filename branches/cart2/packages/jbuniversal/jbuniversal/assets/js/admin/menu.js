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

            $obj.append(
                '<li class="dropdown" id="jbzoo-adminmenu">' +
                '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' +
                options.name +
                ' <span class="caret"></span></a>' +
                '<ul class="dropdown-menu">' + html + '</ul></li>');
        }

    };

})(jQuery, window, document);