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
            'item_id'   : '',
            'element_id': '',
            'isInCart'  : false,
            'add'       : '',
            'remove'    : '',
            'basket'    : '',
            'modal'     : ''
        },
        {
            // default data
            'image'     : '',
            'link'      : '',
            'price'     : {},
            'key'       : {},
            'item_id'   : '',
            'element_id': '',
            'isInCart'  : 0,

            'isInCartVariant' : 0,
            'canRemoveVariant': 0,

            init: function () {

                this.price = this.el.closest('.jsJBPrice');
                this.item_id = this.options.item_id;
                this.element_id = this.options.element_id;

                this.toggleButtons();
            },

            getState: function () {
                var items = JBZoo.getVar('cartItems', {}) || {};
                if (items.hasOwnProperty(this.item_id) && items[this.item_id].hasOwnProperty(this.element_id)) {
                    this.isInCart = 1;

                } else {
                    this.isInCart = 0;
                }

                return this.isInCart;
            },

            removeItem: function () {
                var items = JBZoo.getVar('cartItems', {}) || {};

                delete items[this.item_id][this.element_id];

                if (JBZoo.empty(items[this.item_id])) {
                    delete items[this.item_id];
                }

                JBZoo.addVar('cartItems', items);
            },

            addItem: function () {
                var items = JBZoo.getVar('cartItems', null) || {};
                if (!items.hasOwnProperty(this.item_id)) {
                    var obj = {};
                    obj[this.element_id] = this.element_id;
                    items[this.item_id] = obj;
                }
                else if (items.hasOwnProperty(this.item_id) && !items[this.item_id].hasOwnProperty(this.element_id)) {
                    items[this.item_id][this.element_id] = this.element_id;
                }

                JBZoo.addVar('cartItems', items);
            },

            'emptyCart.JBZooCartModule {document} .jsJBZooCartModule': function (e, $this) {

                $this.toggleButtons();
            },

            'click .jsAddToCart': function (e, $this) {

                var jbPrice = $this.price.data('JBZooPrice'),
                    quantity = $this.get('_quantity', 1),
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
                    'success': function () {

                        $this.addItem();
                        $this.toggleButtons();

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
                $this.ajax({
                    'target' : $(this),
                    'url'    : $this.options.remove,
                    'data'   : {
                        "args": {
                            'key': $this.getKey()
                        }
                    },
                    'success': function () {

                        $this.removeItem();
                        $this.toggleButtons();

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
                this.toggleButtons();
            },

            toggleButtons: function () {
                var jsButtons = this.$('.jsPriceButtons');

                if (this.getState()) {
                    jsButtons
                        .addClass('in-cart');
                } else {
                    jsButtons
                        .removeClass('in-cart');
                }

                return this;
            },

            getKey: function () {
                return this.key;
            },

            basketReload: function () {
                if (this.isWidgetExists('JBZooCartModule')) {
                    $('.jsJBZooCartModule').JBZooCartModule('reload');
                }
            },

            get: function (identifier, defValue) {
                if (this.isWidgetExists('JBZooPrice')) {
                    return this.el.closest('.jsJBPrice').JBZooPrice('get', identifier, defValue);
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