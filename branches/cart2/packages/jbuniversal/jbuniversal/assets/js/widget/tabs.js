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
            var $element       = $(this),
                $widgetHeader  = $element.children('ul'),
                $widgetList    = $widgetHeader.children('li'),
                $widgetLinks   = $widgetList.children('a'),
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
                        link   = getAnchor(result);

                    $widgetContent.hide();
                    $(link[0], $element).show();

                    $widgetList.removeClass('jbzootabs-active jbzootabs-state-active');

                    $(this).parent().addClass('jbzootabs-active jbzootabs-state-active');

                    if ($.isFunction(options.onTabShow)) {
                        var index = $($widgetList, $element).index($('.jbzootabs-active', $element));
                        options.onTabShow(index);
                    }

                    setTimeout(function () {
                        $(window).trigger('resize');
                    }, 200);

                    return false;
                });

                // init widget tab
                (function () {

                    var link = getAnchor(window.location.href);

                    if (link && link[1]) {

                        var loc   = window.location.hash,
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

})(jQuery, window, document);