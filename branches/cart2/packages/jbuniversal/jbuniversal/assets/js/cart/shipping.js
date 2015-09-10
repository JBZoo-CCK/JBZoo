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

            var $input    = $(this),
                $shipping = $input.closest('.jsShipping');

            $this.$('.jsShippingElement').hide();
            $('.jsShippingElement', $shipping).fadeIn();

            $this.$('.jsShipping').removeClass('active');
            $shipping.addClass('active');

            $this._toggleFields($input.val());

            $this.getPrices($shipping);
            $this._repaintChosen($shipping);
        },

        /**
         * Get prices
         * @param $shipping
         */
        getPrices: function ($shipping) {

            var $this = this;

            $this.ajax({
                url    : $this.options.url_shipping,
                data   : $this._getShippingData($shipping),
                success: function (data) {
                    var $cart = $('.jsJBZooCart');
                    $cart.JBZooCart('updatePrices', data.cart);
                    $cart.JBZooCart('reloadModule');
                },
                error  : function (data) {
                    if (data.message) {
                        $this.alert(data.message);
                    }
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

            var $this    = this,
                fields   = $this.options.fields_assign[elementId],
                $wrapper = $('.jbzoo .jsShippingFieldWrapper');

            $('.jsShippingField').stop().hide();
            if (!JBZoo.empty(fields)) {
                $wrapper.find('.jsShippingFieldEmpty').hide();
                $.each(fields, function (n, fieldId) {
                    $('.js' + fieldId).fadeIn();
                });

            } else {
                $wrapper.find('.jsShippingFieldEmpty').stop().fadeIn();
            }

            $this._repaintChosen($wrapper);
        },

        _repaintChosen: function ($wrapper) {
            $wrapper.find('select').JBZooSelect('repaintChosen');
        }

    });

})(jQuery, window, document);
