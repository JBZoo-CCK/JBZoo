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
     * JBZoo Cart widget
     */
    JBZoo.widget('JBZoo.Cart', {
        'text_remove_item': '',
        'text_remove_all' : '',
        'url_shipping'    : '',
        'url_quantity'    : '',
        'url_delete'      : '',
        'url_clear'       : '',
        'items'           : {},
        'rates'           : {}
    }, {
        shipping      : {},
        shippingFields: {},
        changeDelay   : 600,

        init: function ($this) {
            $this.shipping       = $this.$('.jsShipping');
            $this.shippingFields = $this.$('.jsShippingField');

        },

        'change.JBZooQuantity .jsQuantity': function (e, $this, oldValue, newValue) {
            if (arguments.length != 4) {
                return false;
            }
            var itemKey = $(this).closest('.jsCartTableRow').data('key');
            $this._change(itemKey, newValue);
        },

        'click .jsDelete': function (e, $this) {

            var $tableRow  = $(this).closest('.jsCartTableRow'),
                itemsCount = $this.$('.jsCartTableRow').length;

            $this.confirm($this.options.text_remove_item, function () {
                $this.ajax({
                    'url'    : $this.options.url_delete,
                    'target' : $tableRow,
                    'data'   : {
                        'key': $tableRow.data('key')
                    },
                    'success': function (data) {

                        if (itemsCount != 1) {
                            $this.updatePrices(data.cart);
                            $this.reloadModule();
                        } else {
                            window.location.reload();
                        }

                        $tableRow.remove();
                    },
                    'error'  : function (error) {
                        $this.alert(error);
                    }
                });
            });

        },

        'click .jsDeleteAll': function (e, $this) {

            $this.confirm($this.options.text_remove_all, function () {
                $this.ajax({
                    url    : $this.options.url_clear,
                    success: function () {
                        window.location.reload();
                    }
                });
            });
        },

        /**
         * Change quantity for item
         * @param itemKey
         * @param newValue
         * @returns {boolean}
         * @private
         */
        _change: function (itemKey, newValue) {

            var $this = this;

            if (!$this._getItem(itemKey)) {
                return false;
            }

            $this._delay(function () {
                $this.ajax({
                    url    : $this.options.url_quantity,
                    target : '.js' + itemKey,
                    data   : {
                        value: newValue,
                        key  : itemKey
                    },
                    success: function (data) {
                        $this.updatePrices(data.cart);
                        $this.reloadModule();
                    },
                    error  : function (data) {
                        if (data.message) {
                            $this.alert(data.message);
                        }
                        if (JBZoo.isWidgetExists('JBZooQuantity')) {
                            $this.$('.js' + itemKey + ' .jsQuantity').JBZooQuantity('setValue', data.quantity);
                        }
                    }
                });
            }, $this.changeDelay);
        },

        /**
         * Get item info
         * @param rowId
         * @returns {*}
         * @private
         */
        _getItem: function (rowId) {
            return this.options.items[rowId];
        },

        /**
         * @private
         */
        reloadModule: function () {
            if (JBZoo.isWidgetExists('JBZooCartModule')) {
                $('.jsJBZooCartModule').each(function (i, module) {
                    var $module = $(module);
                    if (!JBZoo.empty($module.data('JBZooCartModule'))) {
                        $module.JBZooCartModule('reload');
                    }
                });
            }
        },

        /**
         * Set new params from responce
         * @param cart
         * @param context
         * @private
         */
        updatePrices: function (cart, context) {

            var $this = this;
            context   = $this._def(context, '');

            $.each(cart, function (key, value) {

                var selector = '.js' + key;
                if (context) {
                    selector = '.js' + context + ' ' + '.js' + key;
                }

                var $money = $this.$(selector + '>.jsMoney');

                key = "" + key; // force to string
                if (key.indexOf('-ajax') > 0) {

                    $this.$('.jsShippingAjax-' + key.replace('-ajax', ''))
                        .data('JBZooShippingAjax', value)
                        .trigger('jbzooShippingAjax');

                } else if ($money.length
                    && typeof value == 'object'
                    && (JBZoo.countProps(value) == 2 || !JBZoo.empty(value['MoneyWrap']))
                ) {

                    if (!JBZoo.empty(value['MoneyWrap'])) {
                        value = value['MoneyWrap'];
                    }


                    $money
                        .JBZooMoney({rates: $this.options.rates})
                        .JBZooMoney('setValue', value[0], value[1]);

                } else {

                    if (typeof value == 'object') {
                        $this.updatePrices(value, key);

                    } else {
                        var $block = $this.$(selector);
                        if ($block.length > 0) {
                            $block.html(value);
                        }
                    }

                }

            });
        }

    });

})(jQuery, window, document);
