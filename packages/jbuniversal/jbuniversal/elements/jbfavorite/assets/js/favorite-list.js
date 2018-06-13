/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
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