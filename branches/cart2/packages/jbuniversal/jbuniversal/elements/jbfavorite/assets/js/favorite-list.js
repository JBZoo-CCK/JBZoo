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

    JBZoo.widget('JBZoo.FavoriteList', {
        url_clear: ''
    }, {

        'click .jsFavoriteItemRemove': function (e, $this) {

            var $button = $(this),
                $item = $button.closest('.jsFavoriteItem');

            $this.ajax({
                'url'    : $(this).data('url'),
                'success': function (data) {
                    if (data.result) {
                        $item.slideUp(function () {
                            $item.remove();
                            if ($this.$('.jsFavoriteItem').length == 0) {
                                $this.$('.jsJBZooFavoriteEmpty').fadeIn();
                                $this.$('.jsFavoriteClear').hide();
                            }
                        });
                    }
                }
            });
        },

        'click .jsFavoriteClear': function (e, $this) {

            $this.ajax({
                'url'    : $this.options.url_clear,
                'success': function () {
                    window.location.reload();
                }
            });

        }

    });

})(jQuery, window, document);