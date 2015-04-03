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
