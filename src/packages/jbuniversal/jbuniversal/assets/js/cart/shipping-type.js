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
