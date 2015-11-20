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

    JBZoo.widget('JBZoo.PriceElement.Buttons',
        {
            'item_id'        : '',
            'element_id'     : '',
            'hash'           : '',
            'isInCart'       : false,
            'add'            : '',
            'remove'         : '',
            'basket'         : '',
            'modal'          : '',
            'isModal'        : 0,
            'addAlert'       : false,
            'addAlertText'   : 'Item has been added to the cart!',
            'addAlertTimeout': 3000
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
            'isModal'   : false,

            init: function ($this) {

                this.price = this.el.closest('.' + this.options.hash);
                this.item_id = this.options.item_id;
                this.element_id = this.options.element_id;
                this.isModal = this.options.isModal;
                this.hash = this.options.hash;

                this.toggleButtons();
                this.price.on('removeItem', function () {
                    $this.removeItem();
                    $this.toggleButtons();
                });
            },

            getState: function () {
                var items = this.getItems();
                if (items.hasOwnProperty(this.item_id) && items[this.item_id].hasOwnProperty(this.element_id)) {
                    this.isInCart = 1;

                } else {
                    this.isInCart = 0;
                }

                return this.isInCart;
            },

            getItems: function () {
                if (this.isModal) {
                    return window.parent.JBZoo.getVar('cartItems', {}) || {};
                }

                return JBZoo.getVar('cartItems', {}) || {};
            },

            setItems: function (items) {
                if (this.isModal) {
                    return window.parent.JBZoo.addVar('cartItems', items);
                }

                return JBZoo.addVar('cartItems', items);
            },

            removeItem: function () {
                var items = this.getItems();

                if (JBZoo.empty(items[this.item_id])) {
                    delete items[this.item_id];
                }

                if (!JBZoo.empty(items) && !JBZoo.empty(items[this.item_id]) && !JBZoo.empty(items[this.item_id])[this.element_id]) {
                    delete items[this.item_id][this.element_id];
                }

                this.setItems(items);
            },

            addItem: function () {
                var items = this.getItems();
                if (!items.hasOwnProperty(this.item_id)) {
                    var obj = {};
                    obj[this.element_id] = this.element_id;
                    items[this.item_id] = obj;
                }
                else if (items.hasOwnProperty(this.item_id) && !items[this.item_id].hasOwnProperty(this.element_id)) {
                    items[this.item_id][this.element_id] = this.element_id;
                }

                this.setItems(items);
            },

            'emptyCart.JBZooCartModule {document} .jsJBZooCartModule': function (e, $this) {
                $this.toggleButtons();
            },

            'click .jsAddToCartModal': function (e, $this) {
                $.fancybox({
                    'type'      : 'iframe',
                    'href'      : $this.options.modal + '&args[hash]=' + $this.hash,
                    'autoSize'  : true,
                    'fitToView' : false,
                    'beforeShow': function () {
                        var content = $('.fancybox-iframe').contents().find('.jsPrice');
                        content.addClass('jsPriceModal jbprice-modal');

                        this.width = (content.width() + 100);
                        this.height = (content.height() + 100);
                    },
                    'helpers'   : {
                        'overlay': {
                            'locked': false,
                            'css'   : {
                                'background': 'rgba(119, 119, 119, 0.4)'
                            }
                        }
                    }
                });
            },

            'click .jsAddToCart': function (e, $this) {

                var jbPrice  = $this.el.closest('.jsPrice').data('JBZooPrice'),
                    quantity = jbPrice.get('quantity', '1'),
                    input    = $(this);

                $this.ajax({
                    'target' : $(this),
                    'url'    : $this.options.add,
                    'data'   : {
                        "args": {
                            'template': jbPrice.getTemplate(),
                            'quantity': quantity,
                            'values'  : jbPrice.getValue()
                        }
                    },
                    'success': function () {

                        $this.addItem();
                        $this.toggleButtons();

                        $this.basketReload();

                        if (input.hasClass('jsGoTo') && $this.options.basket) {
                            window.parent.location.href = $this.options.basket;
                        }

                        if (typeof parent.jQuery.fancybox != 'undefined') {
                            window.parent.jQuery.fancybox.close();
                        }

                        if ($this.options.addAlert) {

                            if ($this.options.addAlertTimeout <= 0) {
                                $this.options.addAlertTimeout = '';
                            }

                            $this.alert($this.options.addAlertText, $.noop, {
                                type : "success",
                                timer: ($this.options.addAlertTimeout)
                            });
                        }

                    },
                    'error'  : function (data) {
                        if (data.message) {
                            $this.alert(data.message);
                        }
                    }
                });
            },

            'click .jsPriceButton': function (e, $this) {

                var $btn = $(this);
                if ($btn.hasClass('jsGoTo') && $this.options.basket) {
                    parent.location.href = $this.options.basket;
                }

                if (typeof parent.jQuery.fancybox != 'undefined') {
                    window.parent.jQuery.fancybox.close();
                }
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

                        if (typeof parent.jQuery.fancybox != 'undefined') {
                            parent.jQuery.fancybox.close();
                        }

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

                var jsButtons = this.$('.{hash} .jsPriceButtons', this.isModal);

                if (this.getState()) {
                    jsButtons
                        .addClass('in-cart')
                        .removeClass('not-in-cart');
                } else {
                    jsButtons
                        .removeClass('in-cart')
                        .addClass('not-in-cart');
                }

                if (this.isModal) {
                    this.isModal = false;
                    this.toggleButtons();
                    this.isModal = true;
                }

                return this;
            },

            getKey: function () {
                return this.key;
            },

            basketReload: function () {
                if (this.isWidgetExists('JBZooCartModule')) {
                    var module = this.$('{document} .jsJBZooCartModule', this.isModal);
                    return module.JBZooCartModule('reload');
                }
            },

            get: function (identifier, defValue) {
                if (this.isWidgetExists('JBZooPrice')) {
                    return this.price.JBZooPrice('get', identifier, defValue);
                }

                return defValue;
            },

            $: function (selector, _parent) {

                if (selector == '{element}') {
                    return this.el;
                }
                var _$ = $;

                if (_parent === true) {
                    _$ = window.parent.jQuery;
                }

                if (selector.indexOf('{document} ') === 0) {
                    selector = selector.replace('{document} ', '');
                    return _$(selector);
                }

                if (selector.indexOf('{hash}') === 1) {
                    selector = selector.replace('{hash}', this.hash);
                    return _$(selector);
                }

                return _$(selector, this.el);
            },

            isWidgetExists: function (name) {
                if (this.isModal) {
                    return window.parent.JBZoo.isWidgetExists(name);
                }

                return JBZoo.isWidgetExists(name);
            },

            /**
             * Widget fire on ajax end
             */
            _onAjaxStop: function (options, args) {
                var $target = (options.target) ? $(options.target) : this.el;
                $target.removeClass('jbloading');
                this.toggleButtons();
            }

        }
    );

})(jQuery, window, document);