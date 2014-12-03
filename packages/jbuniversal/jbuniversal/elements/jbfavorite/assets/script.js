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
     * JBZoo Favorite widget
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBFavoriteButtons = function (options) {

        var options = $.extend({}, {}, options);

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

})(jQuery, window, document);