/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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