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

    JBZoo.widget('JBZoo.FavoriteButtons', {
        url_toggle: ''
    }, {

        'click .jsFavoriteToggle': function (e, $this) {

            $this.ajax({
                'url'    : $this.options.url_toggle,
                'target' : this,
                'success': function (data) {

                    if (data.status) {
                        $this.el.removeClass('unactive').addClass('active');
                    } else {
                        if (data.message) {
                            $this.alert(data.message);
                        }

                        $this.el.removeClass('active').addClass('unactive');
                    }
                }
            });
        },

        'click .jsJBZooFavoriteRemove': function (e, $this) {

            $this.ajax({
                'url'    : $this.options.url_toggle,
                'target' : this,
                'success': function (data) {
                    if (data.result) {
                        $favorite.slideUp(function () {
                            $favorite.remove();
                            if ($('.jbfavorite-item-wrapper').length == 0) {
                                $('.jsJBZooFavoriteEmpty').fadeIn();
                            }
                        });
                    }
                }
            });
        }

    });

})(jQuery, window, document);