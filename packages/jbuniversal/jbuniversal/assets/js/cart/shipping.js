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
     * JBZoo Shipping widget in cart
     */
    JBZoo.widget('JBZoo.Shipping', {
        url_shipping : '',
        fields_assign: {}
    }, {

        init: function ($this) {
            $this._toggleFields($this.$('.jsRadio:checked').val());
        },

        'change .jsRadio': function (e, $this) {

            var $input = $(this),
                $shipping = $input.closest('.jsShipping');

            $this.$('.jsShippingElement').hide();
            $('.jsShippingElement', $shipping).fadeIn();

            $this.$('.jsShipping').removeClass('active');
            $shipping.addClass('active');

            $this._toggleFields($input.val());

            $this.getPrices($shipping);
        },

        /**
         * Get prices
         * @param $shipping
         */
        getPrices: function ($shipping) {

            this.ajax({
                url    : this.options.url_shipping,
                data   : this._getShippingData($shipping),
                success: function (data) {
                    var $cart = $('.jsJBZooCart');
                    $cart.JBZooCart('updatePrices', data.cart);
                    $cart.JBZooCart('reloadModule');
                }
            });
        },

        /**
         * @param $shipping
         * @returns {*}
         * @private
         */
        _getShippingData: function ($shipping) {
            return $('[name]', $shipping).serialize();
        },

        /**
         * @param elementId
         * @private
         */
        _toggleFields: function (elementId) {

            var $this = this,
                fields = $this.options.fields_assign[elementId];

            $('.jsShippingField').stop().hide();
            if (fields) {
                $.each(fields, function (n, fieldId) {
                    $('.js' + fieldId).fadeIn();
                });
            }
        }

    });

})(jQuery, window, document);
