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
     * JBZoo Compare widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBCompareButtons = function (options) {

        var options = $.extend({}, {}, options);

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

})(jQuery, window, document);