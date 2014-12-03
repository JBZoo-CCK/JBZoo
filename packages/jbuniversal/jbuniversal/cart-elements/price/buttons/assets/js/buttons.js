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
            'modal'   : ''
        },
        {
            // default data
            'image'   : '',
            'link'    : '',
            'price'   : {},
            'key'     : {},
            'isInCart': 0,

            init: function () {

                this.price = this.el.closest('.jsJBPriceAdvance');

                this.set({
                    'key'     : this.options.key,
                    'isInCart': this.options.isInCart
                });
            },

            'click .jsAddToCart': function (e, $this) {

                var jbPrice = $this.price.data('JBZooPrice'),
                    quantity = jbPrice.get('_quantity', 1);

                if (!$this.isAjax) {
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
                        },
                        'error'  : function (data) {
                            if (data.message) {
                                alert(data.message);
                            }
                        }
                    });
                }
            },

            'click .jsRemoveFromCart': function (e, $this) {

                var jbPrice = $this.price.data('JBZooPrice');
                if (!$this.isAjax) {
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
                            console.log(this.isInCart);
                            jbPrice._updateCache('_buttons', params);
                        },
                        'error'  : function (data) {
                            if (data.message) {
                                alert(data.message);
                            }
                        }
                    });
                }
            },

            rePaint: function (data) {

                this.set(data);

                this.toggleButtons();
            },

            toggleButtons: function () {
                console.log(this.isInCart);
                var jsButtons = this.$('.jsPriceButtons');

                jsButtons.toggleClass('in-cart', this.isInCart == true);
                
                if (this.isWidgetExists('JBZooCartModule')) {
                    $('.jsJBZooCartModule').JBZooCartModule('reload');
                }

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

            _onAjaxStart: function (options) {
                options.target.toggleClass('loading', true);
            },

            _onAjaxStop: function (options) {
                options.target.toggleClass('loading', false);
                this.toggleButtons();
            }

        }
    );

})(jQuery, window, document);