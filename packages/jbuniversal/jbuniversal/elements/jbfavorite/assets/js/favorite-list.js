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
        'url_clear'       : '',
        'text_confirm'    : 'Are you sure?',
        'text_confirm_all': 'Are you sure?'
    }, {

        'click .jsFavoriteItemRemove': function (e, $this) {

            var $button = $(this),
                $item = $button.closest('.jsFavoriteItem');

            $this.confirm($this.options.text_confirm, function () {

                $this.ajax({
                    'url'    : $button.data('url'),
                    'target' : this,
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
            });

            return false;
        },

        'click .jsFavoriteClear': function (e, $this) {

            $this.confirm($this.options.text_confirm_all, function () {
                $this.ajax({
                    'url'    : $this.options.url_clear,
                    'target' : this,
                    'success': function () {
                        window.location.reload();
                    }
                });
            });

        }

    });

})(jQuery, window, document);