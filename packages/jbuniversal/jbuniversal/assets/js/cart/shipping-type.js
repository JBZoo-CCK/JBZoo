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
    JBZoo.widget('JBZoo.ShippingType', {}, {

        /**
         * @private
         */
        _updatePrice: function () {
            $('.jsJBCartShipping').JBZooShipping('getPrices', $('.jsShipping.active'));
        },

        'jbzooShippingAjax {closest .jsShippingElement}': function (e, $this) {
            $this.onRecountAjax($(this).data('JBZooShippingAjax'));
        },

        onRecountAjax: function (params) {
            // noop
        }

    });

})(jQuery, window, document);
