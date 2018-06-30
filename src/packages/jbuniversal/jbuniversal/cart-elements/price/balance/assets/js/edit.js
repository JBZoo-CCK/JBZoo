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

    JBZoo.widget('JBZoo.PriceBalance',
        {},
        {
            init: function () {
                this.change(this.value());
            },

            'change .jsBalanceRadio': function (e, $this) {
                var value = $(this).val();
                $this.change(value);
            },

            value: function () {
                return this.$('input[type="radio"]:checked').val();
            },

            change: function (value) {
                if (value == 1) {
                    this.$('.jsBalanceInput').removeAttr('disabled').focus();
                } else {
                    this.$('.jsBalanceInput').val('').attr('disabled', 'disabled');

                }
            }
        }
    );

})(jQuery, window, document);