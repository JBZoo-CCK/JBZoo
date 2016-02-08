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
                        $header  = $element.children(options.headerWidget);
                } else {
                    var $content = $element.children(options.contentWidget + ':odd'),
                        $header  = $element.children('div:even');
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
                        $span       = $(this, $element).find('.jbzootabs-accordion-header-icon'),
                        $allSpan    = $header.find('.jbzootabs-accordion-header-icon');

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

                    setTimeout(function () {
                        $(window).trigger('resize');
                    }, 200);
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

})(jQuery, window, document);