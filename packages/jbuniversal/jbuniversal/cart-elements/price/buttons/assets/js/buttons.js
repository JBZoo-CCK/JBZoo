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

    JBZoo.widget('JBZoo.PriceElement_buttons',
        {
            'isInCart': false,
            'add'     : '',
            'remove'  : '',
            'basket'  : '',
            'modal'   : ''
        },
        {
            // default data
            'image'   : '',
            'link'    : '',
            'price'   : {},
            'key'     : {},
            'isInCart': 0,

            'isInCartVariant' : 0,
            'canRemoveVariant': 0,

            init: function ($this) {

                this.price = this.el.closest('.jsJBPrice');

                this.set({
                    'key'     : this.options.key,
                    'isInCart': this.options.isInCart
                });
            },

            'emptyCart.JBZooCartModule {document} .jsJBZooCartModule': function(e, $this) {
                $this.set({
                    'key'     : $this.options.key,
                    'isInCart': $this.options.isInCart
                });

                $this.toggleButtons();
            },

            'click .jsAddToCart': function (e, $this) {

                var jbPrice = $this.price.data('JBZooPrice'),
                    quantity = jbPrice.get('_quantity', 1),
                    input = $(this);

                $this.ajax({
                    'target' : $(this),
                    'url'    : $this.options.add,
                    'data'   : {
                        "args": {
                            'quantity': quantity,
                            'values'  : jbPrice.getValue()
                        }
                    },
                    'success': function (data) {

                        var params = {
                            'key'     : $this.getKey(),
                            'isInCart': data.result ? 1 : 0
                        };
                        this.set(params);
                        jbPrice._updateCache('_buttons', params);
                        
                        if (input.hasClass('jsAddToCartGoTo')) {
                            if ($this.options.basket) {
                                parent.location.href = $this.options.basket;
                            }
                        }
                        $this.basketReload();
                    },
                    'error'  : function (data) {
                        if (data.message) {
                            $this.alert(data.message);
                        }
                    }
                });
            },

            'click .jsRemoveFromCart': function (e, $this) {

                var jbPrice = $this.price.data('JBZooPrice');
                $this.ajax({
                    'target' : $(this),
                    'url'    : $this.options.remove,
                    'data'   : {
                        "args": {
                            'key': $this.getKey()
                        }
                    },
                    'success': function (data) {

                        var params = {
                            'key'     : $this.getKey(),
                            'isInCart': data.removed ? 0 : 1
                        };
                        this.set(params);

                        jbPrice._updateCache('_buttons', params);
                        $this.basketReload();
                    },
                    'error'  : function (data) {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });
            },

            rePaint: function (data) {
                this.set(data);
                this.toggleButtons();
            },

            toggleButtons: function () {
                var jsButtons = this.$('.jsPriceButtons');

                jsButtons
                    .toggleClass('in-cart', this.isInCart == true)
                    .toggleClass('in-cart-variant', this.isInCartVariant == true);

                return this;
            },

            set: function (data) {

                if ((!JBZoo.empty(data))) {

                    this.isInCart = data.isInCart;
                    this.key = data.key;
                }

                return false;
            },

            getKey: function () {
                return this.key;
            },

            basketReload: function () {
                if (this.isWidgetExists('JBZooCartModule')) {
                    $('.jsJBZooCartModule').JBZooCartModule('reload');
                }
            },

            /**
             * Widget fire on ajax end
             */
            _onAjaxStop: function (options, arguments) {
                var $target = (options.target) ? $(options.target) : this.el;
                $target.removeClass('jbloading');
                this.toggleButtons();
            }

        }
    );

})(jQuery, window, document);