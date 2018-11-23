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

    /**
     * DiscountCode widget
     */
    JBZoo.widget('JBZoo.DiscountCode', {
        'url': ''
    }, {

        'click .jsSendCode': function (e, $this) {
            $this._update();
            return false;
        },

        'keypress .jsCode': function (e, $this) {
            if (e.which == 13) {
                $this._update();
                return false;
            }
        },

        _update: function () {

            var $this = this;

            $this.ajax({
                url : $this.options.url,
                data: {
                    'args': {
                        'code': $this.$('.jsCode').val()
                    }
                },

                success: function (data) {
                    $('.jsJBZooCart').JBZooCart('updatePrices', data.cart);
                },

                error: function (data) {

                    $('.jsJBZooCart').JBZooCart('updatePrices', data.cart);

                    $this.alert(data.message, function () {
                        $this.$('.jsCode').focus().val('');
                    });

                }
            });
        }

    });

})(jQuery, window, document);
